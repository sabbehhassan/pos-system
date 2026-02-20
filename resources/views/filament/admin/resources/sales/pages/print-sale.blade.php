<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $record->invoice_no }}</title>

    <style>
        /* ===== RESET ===== */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }

        /* ===== PAGE SIZE ===== */
        @page {
            size: A4;
            margin: 15mm;
        }

        /* ===== HIDE FILAMENT UI ===== */
        header,
        nav,
        aside,
        .fi-sidebar,
        .fi-topbar,
        .fi-header,
        .fi-breadcrumbs,
        .fi-global-search,
        .fi-user-menu,
        button {
            display: none !important;
        }

        /* ===== INVOICE WRAPPER ===== */
        .invoice {
            max-width: 100%;
            margin: 0 auto;
        }

        /* ===== HEADER ===== */
        .invoice-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .invoice-header h2 {
            margin: 0;
            font-size: 20px;
            letter-spacing: 1px;
        }

        .company-name {
            font-size: 14px;
            font-weight: bold;
        }

        /* ===== META ===== */
        .invoice-meta {
            width: 100%;
            margin-bottom: 15px;
        }

        .invoice-meta td {
            padding: 4px 0;
            vertical-align: top;
        }

        /* ===== TABLE ===== */
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.items th,
        table.items td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        table.items th {
            background: #f0f0f0;
            font-weight: bold;
        }

        table.items td.qty,
        table.items td.price,
        table.items td.total {
            text-align: right;
        }

        /* ===== TOTAL ===== */
        .grand-total {
            margin-top: 15px;
            text-align: right;
            font-size: 14px;
            font-weight: bold;
        }

        /* ===== FOOTER ===== */
        .invoice-footer {
            margin-top: 30px;
            text-align: center;
            font-size: 11px;
        }

        /* ===== PRINT FIX ===== */
        @media print {
            body {
                margin: 0;
            }
        }
    </style>
</head>

<body>

<div class="invoice">

    <!-- HEADER -->
    <div class="invoice-header">
        <div class="company-name">{{ config('app.name') }}</div>
        <h2>INVOICE</h2>
    </div>

    <!-- META -->
    <table class="invoice-meta">
        <tr>
            <td>
                <strong>Invoice #:</strong> {{ $record->invoice_no }}<br>
                <strong>Date:</strong> {{ $record->created_at->format('d M Y H:i') }}
            </td>
            <td align="right">
                <strong>Cashier:</strong> {{ $record->user?->name ?? '-' }}<br>
                <strong>Payment:</strong> {{ ucfirst($record->payment_method) }}
            </td>
        </tr>
    </table>

    <!-- ITEMS -->
    <table class="items">
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="45%">Item</th>
                <th width="10%">Qty</th>
                <th width="20%">Price</th>
                <th width="20%">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($record->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->product_name }}</td>
                    <td class="qty">{{ $item->qty }}</td>
                    <td class="price">PKR {{ number_format($item->price, 2) }}</td>
                    <td class="total">PKR {{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- TOTAL -->
    <div class="grand-total">
        Grand Total: PKR {{ number_format($record->total, 2) }}
    </div>

    <!-- FOOTER -->
    <div class="invoice-footer">
        Thank you for your business<br>
        Printed on {{ now()->format('d M Y H:i') }}
    </div>

</div>

<script>
    window.onload = function () {
        setTimeout(() => window.print(), 300);
    };
</script>

</body>
</html>