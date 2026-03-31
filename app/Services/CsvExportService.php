<?php

namespace App\Services;

use App\Models\CardStack;
use App\Models\Container;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExportService
{
    /** @var array<string> */
    private const HEADERS = [
        'Scryfall ID',
        'Count',
        'Name',
        'Edition',
        'Condition',
        'Language',
        'Foil',
        'Last Modified',
        'Collector Number',
        'Container',
    ];

    /**
     * Stream a CSV export of a single container's card stacks.
     */
    public static function streamContainerCsv(User $user, Container $container): StreamedResponse
    {
        $query = $container->cardStacks()
            ->join('default_cards', 'card_stacks.default_card_id', '=', 'default_cards.id')
            ->leftJoin('sets', 'default_cards.set_id', '=', 'sets.id')
            ->select([
                'card_stacks.id',
                'card_stacks.amount',
                'card_stacks.condition',
                'card_stacks.finish',
                'card_stacks.language',
                'card_stacks.updated_at',
                'default_cards.id as scryfall_id',
                'default_cards.name as card_name',
                'default_cards.collector_number',
                'sets.code as set_code',
            ]);

        $filename = self::filename($user, Str::slug($container->name));

        return self::streamCsv($query, $filename, $container->name);
    }

    /**
     * Stream a CSV export of the user's entire collection.
     */
    public static function streamCollectionCsv(User $user): StreamedResponse
    {
        $query = CardStack::query()
            ->where('card_stacks.user_id', $user->id)
            ->join('default_cards', 'card_stacks.default_card_id', '=', 'default_cards.id')
            ->leftJoin('sets', 'default_cards.set_id', '=', 'sets.id')
            ->leftJoin('containers', 'card_stacks.container_id', '=', 'containers.id')
            ->select([
                'card_stacks.id',
                'card_stacks.amount',
                'card_stacks.condition',
                'card_stacks.finish',
                'card_stacks.language',
                'card_stacks.updated_at',
                'default_cards.id as scryfall_id',
                'default_cards.name as card_name',
                'default_cards.collector_number',
                'sets.code as set_code',
                'containers.name as container_name',
            ]);

        $filename = self::filename($user, 'collection');

        return self::streamCsv($query, $filename);
    }

    /**
     * Build a CSV filename: mbo-{username}-{label}-{timestamp}.csv
     */
    private static function filename(User $user, string $label): string
    {
        return 'mbo-'.Str::slug($user->name)."-{$label}-".now()->format('Y-m-d').'.csv';
    }

    /**
     * Stream a CSV response from the given query.
     *
     * Chunks results in batches of 1000 to keep memory flat for large exports.
     *
     * @param  string|null  $containerName  Fixed container name for single-container exports.
     */
    private static function streamCsv(BuilderContract $query, string $filename, ?string $containerName = null): StreamedResponse
    {
        return new StreamedResponse(function () use ($query, $containerName) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM so Excel interprets the file correctly.
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, self::HEADERS);

            $query->orderBy('card_stacks.id')->chunk(1000, function ($rows) use ($handle, $containerName) {
                foreach ($rows as $row) {
                    fputcsv($handle, [
                        $row->scryfall_id ?? '',
                        $row->amount,
                        $row->card_name,
                        strtoupper($row->set_code ?? ''),
                        $row->condition?->value ?? '',
                        $row->language?->value ?? 'en',
                        $row->finish?->label() ?? '',
                        $row->updated_at?->toIso8601String() ?? '',
                        $row->collector_number ?? '',
                        $containerName ?? $row->container_name ?? '',
                    ]);
                }
            });

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
