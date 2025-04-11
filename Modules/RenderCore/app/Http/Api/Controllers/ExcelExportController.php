<?php

namespace Modules\RenderCore\App\Http\Api\Controllers;

use App\Http\Controllers\S4BBaseController;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Modules\ExcelCore\Exports\DynamicArrayExport;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Modules\RenderCore\App\Http\Api\Services\ExcelExportService;

class ExcelExportController extends S4BBaseController
{
    protected ExcelExportService $excelService;

    public function __construct(ExcelExportService $excelService)
    {
        $this->excelService = $excelService;
    }

    public function export(Request $request)
    {
        try {
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

        } catch (\Exception $e) {
            return $this->S4BSendError($e, ['Error al generar el archivo Excel: ' => $e]);
        }
    }
}
