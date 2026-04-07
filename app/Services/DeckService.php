<?php

namespace App\Services;

use App\Enums\CardFormat;
use App\Models\Deck;
use App\Models\DefaultCard;
use App\Models\OracleCard;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class DeckService
{
    /**
     * Resolve the newest default card (printing) for an oracle card.
     *
     * Joins through the set's released_at to pick the most recent printing.
     * Falls back to the first default card if no set dates are available.
     */
    public static function newestDefaultCard(string $oracleCardId): ?DefaultCard
    {
        return DefaultCard::query()
            ->where('oracle_id', $oracleCardId)
            ->whereHas('set')
            ->orderByDesc(
                fn (Builder $q) => $q->select('released_at')
                    ->from('sets')
                    ->whereColumn('sets.id', 'default_cards.set_id')
                    ->limit(1)
            )
            ->first();
    }

    /**
     * Validate that an oracle card is a legal commander for the given format.
     *
     * Uses CommandZoneService's search with an exact name match — if the card
     * appears in results, it's valid.
     */
    public static function isLegalCommander(string $oracleCardId, CardFormat $format): bool
    {
        $oracle = OracleCard::find($oracleCardId);

        if (! $oracle) {
            return false;
        }

        $parsed = ['name_segments' => [$oracle->name], 'set_code' => null, 'collector_number' => null];
        $filters = [
            'rule0' => false,
            'partner' => false,
            'friends_forever' => false,
            'doctors_companion' => false,
            'background' => false,
            'partner_type' => null,
            'exclude' => null,
        ];

        $results = CommandZoneService::searchCommanders($format, $parsed, $filters);

        return $results->contains('id', $oracleCardId);
    }

    /**
     * Validate that an oracle card is a legal Oathbreaker planeswalker.
     */
    public static function isLegalOathbreaker(string $oracleCardId, CardFormat $format): bool
    {
        $oracle = OracleCard::find($oracleCardId);

        if (! $oracle) {
            return false;
        }

        $parsed = ['name_segments' => [$oracle->name], 'set_code' => null, 'collector_number' => null];

        $results = CommandZoneService::searchOathbreaker($format, $parsed, 'planeswalker', null, false, null);

        return $results->contains('id', $oracleCardId);
    }

    /**
     * Validate that an oracle card is a legal signature spell for the given planeswalker.
     */
    public static function isLegalSignatureSpell(string $oracleCardId, string $planeswalkerOracleCardId, CardFormat $format): bool
    {
        $oracle = OracleCard::find($oracleCardId);
        $planeswalker = OracleCard::find($planeswalkerOracleCardId);

        if (! $oracle || ! $planeswalker) {
            return false;
        }

        $parsed = ['name_segments' => [$oracle->name], 'set_code' => null, 'collector_number' => null];

        $results = CommandZoneService::searchOathbreaker(
            $format,
            $parsed,
            'spell',
            $planeswalker->color_identity,
            false,
            $planeswalkerOracleCardId,
        );

        return $results->contains('id', $oracleCardId);
    }

    /**
     * Validate that a companion is a legal pairing for the given commander.
     *
     * Resolves the commander's companion type and searches for the companion
     * using the appropriate filter.
     */
    public static function isLegalCompanion(string $companionOracleCardId, string $commanderOracleCardId, CardFormat $format): bool
    {
        $commander = OracleCard::with(['faces' => fn ($q) => $q->orderBy('face_index')])->find($commanderOracleCardId);

        if (! $commander) {
            return false;
        }

        $allOracleText = $commander->faces->pluck('oracle_text')->implode("\n");
        $frontTypeLine = $commander->faces->first()?->type_line ?? '';
        $companion = CommandZoneService::resolveCompanionType($allOracleText, $frontTypeLine);

        if (! $companion['type']) {
            return false;
        }

        $filters = [
            'rule0' => false,
            'partner' => $companion['type'] === 'partner',
            'friends_forever' => $companion['type'] === 'friends_forever',
            'doctors_companion' => $companion['type'] === 'doctors_companion',
            'background' => $companion['type'] === 'background',
            'partner_type' => $companion['type'] === 'partner_type' ? $companion['partner_with_name'] : null,
            'exclude' => $commanderOracleCardId,
        ];

        // For "partner_with" the companion is predetermined — just check name.
        if ($companion['type'] === 'partner_with') {
            $partnerOracle = OracleCard::where('name', $companion['partner_with_name'])->first();

            return $partnerOracle && $partnerOracle->id === $companionOracleCardId;
        }

        $parsed = ['name_segments' => [], 'set_code' => null, 'collector_number' => null];
        $results = CommandZoneService::searchCommanders($format, $parsed, $filters);

        return $results->contains('id', $companionOracleCardId);
    }

    /**
     * Create a new deck with optional command zone cards.
     *
     * Validates command zone cards against format rules via CommandZoneService
     * search methods. Aborts with 422 if any validation fails.
     *
     * @param  array{format: string, deck_name: string, deck_description?: string|null, commander_id?: string|null, companion_id?: string|null, signature_spell_id?: string|null}  $data
     */
    public static function createDeck(User $user, array $data): Deck
    {
        $format = CardFormat::from($data['format']);
        $profile = $format->rules();

        $commanderOracleId = $data['commander_id'] ?? null;
        $companionOracleId = $data['companion_id'] ?? null;
        $signatureSpellOracleId = $data['signature_spell_id'] ?? null;

        // Validate command zone cards against format rules.
        if ($profile->requiresCommander() && $commanderOracleId) {
            if ($profile->hasSignatureSpell()) {
                abort_unless(self::isLegalOathbreaker($commanderOracleId, $format), 422, 'Invalid planeswalker.');

                if ($signatureSpellOracleId) {
                    abort_unless(
                        self::isLegalSignatureSpell($signatureSpellOracleId, $commanderOracleId, $format),
                        422,
                        'Invalid signature spell.',
                    );
                }
            } else {
                abort_unless(self::isLegalCommander($commanderOracleId, $format), 422, 'Invalid commander.');

                if ($companionOracleId) {
                    abort_unless(
                        self::isLegalCompanion($companionOracleId, $commanderOracleId, $format),
                        422,
                        'Invalid companion.',
                    );
                }
            }
        }

        $deck = Deck::create([
            'user_id' => $user->id,
            'name' => $data['deck_name'],
            'description' => $data['deck_description'] ?? null,
            'format' => $format,
        ]);

        // Attach command zone cards.
        if ($profile->requiresCommander() && $commanderOracleId) {
            $commanderDefault = self::newestDefaultCard($commanderOracleId);
            abort_unless($commanderDefault !== null, 422, 'No printing found for commander.');

            $deck->commanders()->attach($commanderOracleId, [
                'default_card_id' => $commanderDefault->id,
                'is_partner' => false,
            ]);

            if ($profile->hasSignatureSpell() && $signatureSpellOracleId) {
                $spellDefault = self::newestDefaultCard($signatureSpellOracleId);
                abort_unless($spellDefault !== null, 422, 'No printing found for signature spell.');

                $deck->commanders()->attach($signatureSpellOracleId, [
                    'default_card_id' => $spellDefault->id,
                    'is_partner' => true,
                ]);
            } elseif ($companionOracleId) {
                $companionDefault = self::newestDefaultCard($companionOracleId);
                abort_unless($companionDefault !== null, 422, 'No printing found for companion.');

                $deck->commanders()->attach($companionOracleId, [
                    'default_card_id' => $companionDefault->id,
                    'is_partner' => true,
                ]);
            }
        }

        return $deck;
    }
}
