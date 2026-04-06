<?php

namespace App\Services;

use App\Enums\CardFormat;
use App\Models\OracleCard;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class CommanderService
{
    /**
     * Search for cards that can be a commander (or companion).
     *
     * @param  array{name_segments: string[], set_code: string|null, collector_number: string|null}  $parsed
     * @param  array{rule0: bool, partner: bool, friends_forever: bool, doctors_companion: bool, background: bool, partner_type: string|null, exclude: string|null}  $filters
     */
    public static function searchCommanders(CardFormat $format, array $parsed, array $filters): Collection
    {
        $query = OracleCard::query();

        if ($parsed['set_code']) {
            $query->whereHas('defaults', fn (Builder $q) => $q->whereHas(
                'set',
                fn (Builder $sq) => $sq->where('code', $parsed['set_code'])
            ));
        }

        foreach ($parsed['name_segments'] as $segment) {
            $query->where('name', 'like', "%$segment%");
        }

        if ($filters['exclude']) {
            $query->where('id', '!=', $filters['exclude']);
        }

        // Rule 0: skip commander-legality and format-legality filters when the user opts in.
        if (! $filters['rule0']) {
            $query->legalIn($format);

            // Background search has its own type-line filter — skip the commander constraint.
            if (! $filters['background']) {
                // Must qualify as a commander: front face is a legendary creature,
                // or any face explicitly says "can be your commander".
                $query->where(function (Builder $q): void {
                    $q->whereHas('faces', function (Builder $fq): void {
                        $fq->where('face_index', 0)
                            ->where('type_line', 'like', '%Legendary%')
                            ->whereNotNull('power')
                            ->whereNotNull('toughness');
                    })->orWhereHas('faces', function (Builder $fq): void {
                        $fq->where('oracle_text', 'like', '%can be your commander%');
                    });
                });
            }
        }

        // When searching for a partner, only return cards with a partner keyword.
        if ($filters['partner']) {
            $query->whereHas('faces', function (Builder $fq): void {
                $fq->where('oracle_text', 'regexp', '\\bPartner\\b|Legendary partner');
            });
        }

        // When searching for a Doctor's companion, only return cards with that keyword.
        if ($filters['doctors_companion']) {
            $query->whereHas('faces', function (Builder $fq): void {
                $fq->where('oracle_text', 'like', '%Doctor\'s companion%');
            });
        }

        // When searching for a "Friends forever" partner, only return other "Friends forever" cards.
        if ($filters['friends_forever']) {
            $query->whereHas('faces', function (Builder $fq): void {
                $fq->where('oracle_text', 'like', '%Friends forever%');
            });
        }

        // When searching for a background, only return cards with the Background subtype.
        if ($filters['background']) {
            $query->whereHas('faces', function (Builder $fq): void {
                $fq->where('type_line', 'like', '%Background%');
            });
        }

        // When searching for a typed partner (e.g. "Partner—Survivors"), only return
        // cards whose oracle text contains the same "Partner—<type>" tag.
        if ($filters['partner_type']) {
            $type = $filters['partner_type'];
            $query->whereHas('faces', function (Builder $fq) use ($type): void {
                $fq->where('oracle_text', 'like', "%Partner—$type%");
            });
        }

        return $query->select('id', 'name', 'color_identity')
            ->with(['faces' => fn ($q) => $q->select('oracle_card_id', 'face_index', 'mana_cost', 'type_line', 'oracle_text')
                ->orderBy('face_index')])
            ->orderBy('name')
            ->limit(50)
            ->get()
            ->map(fn (OracleCard $card) => self::mapCommanderCard($card));
    }

    /**
     * Transform an eager-loaded OracleCard into the commander response shape.
     *
     * Expects the `faces` relation to be loaded with at least
     * `oracle_card_id`, `face_index`, `mana_cost`, `type_line`, and `oracle_text`.
     *
     * @return array{id: string, name: string, color_identity: string|null, companion_type: string|null, partner_with_name: string|null, faces: array}
     */
    public static function mapCommanderCard(OracleCard $card): array
    {
        $allOracleText = $card->faces->pluck('oracle_text')->implode("\n");
        $frontTypeLine = $card->faces->first()?->type_line ?? '';
        $companion = self::resolveCompanionType($allOracleText, $frontTypeLine);

        return [
            'id' => $card->id,
            'name' => $card->name,
            'color_identity' => $card->color_identity,
            'companion_type' => $companion['type'],
            'partner_with_name' => $companion['partner_with_name'],
            'faces' => $card->faces->map(fn ($face) => [
                'type_line' => $face->type_line,
                'mana_cost' => $face->mana_cost,
            ])->values(),
        ];
    }

    /**
     * Determine the companion type from the combined oracle text and front-face type line.
     *
     * @return array{type: 'partner'|'partner_with'|'partner_type'|'friends_forever'|'doctors_companion'|'background'|null, partner_with_name: string|null}
     */
    public static function resolveCompanionType(string $oracleText, string $frontTypeLine): array
    {
        if (preg_match('/Choose a Background/i', $oracleText)) {
            return ['type' => 'background', 'partner_with_name' => null];
        }

        if (preg_match('/Partner with ([^\n(]+)/i', $oracleText, $matches)) {
            return ['type' => 'partner_with', 'partner_with_name' => trim($matches[1])];
        }

        if (preg_match('/Friends forever/i', $oracleText)) {
            return ['type' => 'friends_forever', 'partner_with_name' => null];
        }

        // Time Lord Doctors can have a Doctor's companion in the command zone.
        if ($frontTypeLine === 'Legendary Creature — Time Lord Doctor') {
            return ['type' => 'doctors_companion', 'partner_with_name' => null];
        }

        // "Partner—Survivors", "Partner—Character Select", etc.
        // These can only pair with other cards sharing the same typed partner tag.
        if (preg_match('/Partner\x{2014}([^\n(]+)/iu', $oracleText, $matches)) {
            return ['type' => 'partner_type', 'partner_with_name' => trim($matches[1])];
        }

        if (preg_match('/\bPartner\b|Legendary partner/i', $oracleText)) {
            return ['type' => 'partner', 'partner_with_name' => null];
        }

        return ['type' => null, 'partner_with_name' => null];
    }
}
