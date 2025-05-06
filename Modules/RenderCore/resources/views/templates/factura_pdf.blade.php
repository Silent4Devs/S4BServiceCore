<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Factura' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            padding: 30px;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            border: 1px solid #eee;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        }
        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .info-table, .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 5px 0;
        }
        .data-table th, .data-table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .data-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .total {
            text-align: right;
            padding-top: 20px;
            font-size: 16px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="invoice-title">{{ $title ?? 'Factura' }}</div>

        <table class="info-table">
            <tr>
                <td><strong>Fecha:</strong> {{ date('d/m/Y') }}</td>
                <td style="text-align: right;"><strong>Factura Nº:</strong> #{{ $invoice_number ?? '0001' }}</td>
            </tr>
        </table>

        <table class="data-table" style="margin-top: 20px;">
            <thead>
                <tr>
                    @foreach ($headers as $header)
                        <th>{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                    <tr>
                        @foreach ($row as $cell)
                            <td>{{ $cell }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if(isset($total))
            <div class="total">Total: ${{ number_format($total, 2) }}</div>
        @endif
    </div>
</body>
</html>
