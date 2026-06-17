<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Transactions Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
        h1 { color: #1e3a8a; font-size: 18px; }
        .header { margin-bottom: 20px; border-bottom: 2px solid #1e3a8a; padding-bottom: 10px; }
        .meta { color: #666; font-size: 11px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #1e3a8a; color: white; padding: 8px; text-align: left; font-size: 11px; }
        td { padding: 7px 8px; border-bottom: 1px solid #eee; }
        tr:nth-child(even) { background: #f8fafc; }
        .in { color: #16a34a; font-weight: bold; }
        .out { color: #dc2626; font-weight: bold; }
    </style>
</head>
<body>

<div class="header">
    <h1>🔵 Optical Inventory — Transactions Report</h1>
</div>

<div class="meta">
    Generated on: {{ now()->format('d M Y, h:i A') }} &nbsp;|&nbsp;
    Total Records: <strong>{{ $transactions->count() }}</strong>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Date</th>
            <th>Product</th>
            <th>Power</th>
            <th>Type</th>
            <th>Quantity</th>
        </tr>
    </thead>
    <tbody>
        @foreach($transactions as $i => $tx)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $tx->created_at->format('d M Y, h:i A') }}</td>
            <td>{{ $tx->product->name }}</td>
            <td>{{ $tx->power->getLabel() }}</td>
            <td>
                @if($tx->type == 'IN')
                    <span class="in">IN</span>
                @else
                    <span class="out">OUT</span>
                @endif
            </td>
            <td>{{ $tx->quantity }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>