<?php

namespace Modules\RenderCore\App\Http\Api\Services;

use Maatwebsite\Excel\Facades\Excel;
use Modules\RenderCore\App\Http\Api\Exports\Excel\DynamicArrayExport;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExcelExportService
{
    public function generateExcel(array $headers, array $data, string $filename): BinaryFileResponse
    {
        return Excel::download(new DynamicArrayExport($headers, $data), $filename . '.xlsx');
    }
}
