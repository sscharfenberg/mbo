<?php

namespace App\Http\Controllers\Collection;

use App\Enums\ImportSource;
use App\Http\Controllers\Controller;
use App\Http\Requests\Collection\ShowImportRequest;
use App\Models\Container;
use App\Services\CardStackService;
use App\Services\ContainerService;
use App\Services\CsvImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ImportController extends Controller
{
    /**
     * Show the CSV import page.
     *
     * When a container is provided via route model binding, it is pre-selected
     * as the import target.
     */
    public function show(ShowImportRequest $request, ?Container $container = null): Response
    {
        if ($container) {
            $container->load('defaultCard.set', 'defaultCard.artist');
            $container->loadSum('cardStacks', 'amount');
            $container->total_price = ContainerService::totalPrice($container, $request->user()->currency);
        }

        $containers = Container::query()
            ->where('user_id', $request->user()->id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Collection/Import/CsvImportPage', [
            'container' => $container ? ContainerService::serializeContainer($container) : null,
            'containers' => $containers,
            'maxUploadBytes' => (int) config('cantrip.csv_upload.max_bytes'),
            'allowedTypes' => config('cantrip.csv_upload.allowed_types'),
            'sources' => array_column(ImportSource::cases(), 'value'),
            'results' => null,
        ]);
    }

    /**
     * Process the uploaded CSV and import cards into the collection.
     *
     * Validates source, container ownership, and file existence on the tmp disk,
     * then delegates to CsvImportService. Re-renders the import page with results.
     *
     * @throws ValidationException When validation fails or the CSV has missing headers.
     */
    public function store(Request $request): Response
    {
        $request->validate([
            'source' => ['required', Rule::enum(ImportSource::class)],
            'container' => ['nullable', Rule::exists(Container::class, 'id')],
            'filename' => ['required', 'string', 'regex:/^[a-f0-9\-]{36}\.csv$/'],
        ]);

        if ($request->container) {
            CardStackService::resolveOwnedContainer($request->user(), $request->container);
        }

        if (! Storage::disk('tmp')->exists($request->filename)) {
            throw ValidationException::withMessages([
                'filename' => [__('validation.custom.file.not_found')],
            ]);
        }

        $results = CsvImportService::import(
            $request->user(),
            $request->filename,
            ImportSource::from($request->source),
            $request->container ?: null,
        );

        $containers = Container::query()
            ->where('user_id', $request->user()->id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Collection/Import/CsvImportPage', [
            'container' => null,
            'containers' => $containers,
            'maxUploadBytes' => (int) config('cantrip.csv_upload.max_bytes'),
            'allowedTypes' => config('cantrip.csv_upload.allowed_types'),
            'sources' => array_column(ImportSource::cases(), 'value'),
            'results' => $results,
        ]);
    }

    /**
     * Upload a CSV file to temporary storage.
     *
     * Validates file size against config('cantrip.csv_upload.max_bytes'), checks for
     * binary content (null bytes), and verifies the file is parseable as CSV with
     * a consistent column count. Stores the file on the `tmp` disk and returns the
     * generated filename so the client can reference it on form submit.
     */
    public function upload(Request $request): JsonResponse
    {
        $maxBytes = config('cantrip.csv_upload.max_bytes');
        $maxKb = (int) ceil($maxBytes / 1024);

        $maxMb = round($maxBytes / (1024 * 1024), 1);

        $request->validate([
            'file' => ['required', 'file', "max:{$maxKb}"],
        ], [
            'file.max' => __('validation.custom.file.max', ['max' => $maxMb]),
        ]);

        $file = $request->file('file');
        $content = file_get_contents($file->getRealPath());

        // Reject binary files — legitimate CSVs never contain null bytes.
        if (str_contains($content, "\0")) {
            return response()->json([
                'errors' => ['file' => [__('validation.custom.file.csv_not_parseable')]],
            ], 422);
        }

        // Convert Windows-1252 to UTF-8 if needed.
        if (! mb_check_encoding($content, 'UTF-8')) {
            $content = mb_convert_encoding($content, 'UTF-8', 'Windows-1252');
        }

        // Strip UTF-8 BOM if present.
        if (str_starts_with($content, "\xEF\xBB\xBF")) {
            $content = substr($content, 3);
        }

        // Verify the file is parseable as CSV with consistent columns.
        $error = $this->validateCsvContent($content);
        if ($error) {
            return response()->json([
                'errors' => ['file' => [$error]],
            ], 422);
        }

        // Store the (possibly re-encoded) content on the tmp disk.
        $filename = Str::uuid().'.csv';
        Storage::disk('tmp')->put($filename, $content);

        return response()->json(['filename' => $filename]);
    }

    /**
     * Validate that raw CSV content is parseable and has a consistent column count.
     *
     * Opens the content as an in-memory stream and reads up to 5 rows with fgetcsv().
     * Returns an error message string on failure, or null when the content is valid.
     */
    private function validateCsvContent(string $content): ?string
    {
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $content);
        rewind($stream);

        $delimiter = CsvImportService::detectDelimiter($content);

        $headerColumnCount = null;
        $rowsChecked = 0;
        $maxRows = 5;

        while (($row = fgetcsv($stream, separator: $delimiter)) !== false && $rowsChecked < $maxRows) {
            // fgetcsv returns [null] for empty lines — skip them.
            if ($row === [null]) {
                continue;
            }

            if ($headerColumnCount === null) {
                $headerColumnCount = count($row);
                if ($headerColumnCount < 2) {
                    fclose($stream);

                    return __('validation.custom.file.csv_not_parseable');
                }
            } else {
                if (count($row) !== $headerColumnCount) {
                    fclose($stream);

                    return __('validation.custom.file.csv_not_parseable');
                }
            }
            $rowsChecked++;
        }

        fclose($stream);

        if ($rowsChecked === 0) {
            return __('validation.custom.file.csv_not_parseable');
        }

        return null;
    }
}
