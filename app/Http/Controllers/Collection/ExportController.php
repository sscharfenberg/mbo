<?php

namespace App\Http\Controllers\Collection;

use App\Http\Controllers\Controller;
use App\Models\Container;
use App\Services\CsvExportService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    /**
     * Stream a CSV export of a single container's card stacks.
     *
     * Aborts with 403 if the container belongs to another user.
     */
    public function container(Request $request, Container $container): StreamedResponse
    {
        abort_if($container->user_id !== $request->user()->id, 403);

        return CsvExportService::streamContainerCsv($request->user(), $container);
    }

    /**
     * Stream a CSV export of the user's entire collection.
     */
    public function collection(Request $request): StreamedResponse
    {
        return CsvExportService::streamCollectionCsv($request->user());
    }
}
