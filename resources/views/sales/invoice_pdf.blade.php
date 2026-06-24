<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $sale->invoice_no }}</title>
    <style>
        body{font-family: DejaVu Sans, sans-serif;}
        table{width:100%;border-collapse:collapse}
        td,th{border:1px solid #ccc;padding:6px}
    </style>
</head>
<body>
    <h2>Invoice: {{ $sale->invoice_no }}</h2>
    <p>Customer: {{ $sale->customer->name }}</p>
    <p>Date: {{ $sale->invoice_date }}</p>
    <table>
        <thead>
            <tr><th>Product</th><th>Power</th><th>Qty</th><th>Unit</th><th>Total</th></tr>
        </thead>
        <tbody>
            @foreach($sale->items as $it)
            <tr>
                <td>{{ $it->product->name ?? $it->product_id }}</td>
                <td>{{ $it->power->getLabel() ?? $it->power_id }}</td>
                <td>{{ $it->quantity }}</td>
                <td>{{ number_format($it->unit_price,2) }}</td>
                <td>{{ number_format($it->total_price,2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <p>Net: {{ number_format($sale->net_total,2) }}</p>
</body>
</html>
