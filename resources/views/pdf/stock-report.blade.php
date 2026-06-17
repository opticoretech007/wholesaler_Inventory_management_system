<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Stock Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
        h1 { color: #1e3a8a; font-size: 18px; }
        .header { margin-bottom: 20px; border-bottom: 2px solid #1e3a8a; padding-bottom: 10px; }
        .meta { color: #666; font-size: 11px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #1e3a8a; color: white; padding: 8px; text-align: left; font-size: 11px; }
        td { padding: 7px 8px; border-bottom: 1px solid #eee; }
        tr:nth-child(even) { background: #f8fafc; }
        .badge-low { color: #dc2626; font-weight: bold; }
        .badge-ok { color: #16a34a; }
        .total { margin-top: 15px; text-align: right; font-weight: bold; color: #1e3a8a; }
    </style>
</head>
<body>

<div class="header">
    <h1>🔵 Optical Inventory — Stock Report</h1>
</div>

<div class="meta">
    Generated on: {{ now()->format('d M Y, h:i A') }} &nbsp;|&nbsp;
    Total Stock: <strong>{{ $totalStock }} units</strong>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Product</th>
            <th>Power</th>
            <th>Quantity</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($stockSummary as $i => $stock)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $stock->product->name }}</td>
            <td>{{ $stock->power->getLabel() }}</td>
            <td>{{ $stock->quantity }}</td>
            <td>
                @if($stock->quantity < 5)
                    <span class="badge-low">Low Stock</span>
                @else
                    <span class="badge-ok">In Stock</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="total">Total: {{ $totalStock }} units</div>

</body>
</html>