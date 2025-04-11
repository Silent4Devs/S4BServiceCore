<?php

namespace Modules\RenderCore\App\Http\Api\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\RenderCore\App\Http\Api\Services\PdfExportService;

class PdfExportController extends Controller
{
    protected PdfExportService $pdfService;

    public function __construct(PdfExportService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    public function export(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'headers' => 'required|array',
            'data' => 'required|array',
        ]);

        return $this->pdfService->generate(
            $request->input('headers'),
            $request->input('data'),
            $request->input('title')
        );
    }
}
