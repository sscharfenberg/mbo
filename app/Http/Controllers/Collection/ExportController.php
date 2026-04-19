<?php

namespace App\Http\Controllers\Collection;

use App\Http\Controllers\Controller;
use App\Http\Requests\Collection\ExportContainerRequest;
use App\Models\Container;
use App\Services\CsvExportService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    /**
     * Stream a CSV export of a single container's card stacks.
     */
    public function container(ExportContainerRequest $request, Container $container): StreamedResponse
    {
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
