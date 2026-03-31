<?php

namespace App\Http\Controllers\Collection;

use App\Http\Controllers\Controller;
use App\Models\Container;
use App\Services\ContainerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ImportController extends Controller
{
    /**
     * Show the CSV import page.
     *
     * When a container is provided via route model binding, it is pre-selected
     * as the import target. Aborts with 403 if the container belongs to another user.
     */
    public function show(Request $request, ?Container $container = null): Response
    {
        if ($container) {
            abort_if($container->user_id !== $request->user()->id, 403);
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
            'maxUploadBytes' => (int) config('mbo.csv_upload_max_bytes'),
            'allowedTypes' => config('mbo.csv_upload_allowed_types'),
        ]);
    }

    /**
     * Upload a CSV file to temporary storage.
     *
     * Validates file size against config('mbo.csv_upload_max_bytes'), checks for
     * binary content (null bytes), and verifies the file is parseable as CSV with
     * a consistent column count. Stores the file on the `tmp` disk and returns the
     * generated filename so the client can reference it on form submit.
     */
    public function upload(Request $request): JsonResponse
    {
        $maxBytes = config('mbo.csv_upload_max_bytes');
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

        $headerColumnCount = null;
        $rowsChecked = 0;
        $maxRows = 5;

        while (($row = fgetcsv($stream)) !== false && $rowsChecked < $maxRows) {
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
