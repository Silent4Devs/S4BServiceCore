<?php

namespace Modules\RenderCore\App\Http\Api\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class PdfExportService
{
    public function generate(array $headers, array $data, string $title = 'Documento'): Response
    {
        $pdf = Pdf::loadView('rendercore::templates.factura_pdf', [
            'headers' => $headers,
            'data' => $data,
            'title' => $title,
        ]);

        return $pdf->download($title . '.pdf');
    }
}
