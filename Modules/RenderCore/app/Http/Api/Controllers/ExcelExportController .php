<?php

namespace Modules\RenderCore\App\Http\Api\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Modules\ExcelCore\Exports\DynamicArrayExport;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Modules\RenderCore\App\Http\Api\Services\ExcelExportService;

class ExcelExportController extends Controller
{
    protected ExcelExportService $excelService;

    public function __construct(ExcelExportService $excelService)
    {
        $this->excelService = $excelService;
    }

    public function export(Request $request)
    {
        $request->validate([
            'filename' => 'required|string',
            'headers' => 'required|array',
            'data' => 'required|array',
        ]);

        return $this->excelService->generateExcel(
            $request->input('headers'),
            $request->input('data'),
            $request->input('filename')
        );
    }
}