<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .cards { display: flex; gap: 20px; margin-bottom: 30px; }
        .card { background: #f0f0f0; padding: 20px; border-radius: 8px; min-width: 150px; text-align: center; }
        .card h1 { margin: 0; font-size: 40px; color: #333; }
        .card p { margin: 5px 0 0; color: #666; }
        .low-stock { color: red; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f5f5f5; }
        .badge-in { color: green; font-weight: bold; }
        .badge-out { color: red; font-weight: bold; }
        nav { margin-bottom: 20px; }
        nav a { margin-right: 15px; text-decoration: none; color: blue; }
    </style>
</head>
<body>

<nav>
    <a href="/">Dashboard</a>
    <a href="/stock-in">Stock IN</a>
    <a href="/stock-out">Stock OUT</a>
</nav>

<h2>Dashboard</h2>

{{-- Summary Cards --}}
<div class="cards">
    <div class="card">
        <h1>{{ $totalStock }}</h1>
        <p>Total Stock</p>
    </div>
    <div class="card">
        <h1 class="low-stock">{{ $lowStock->count() }}</h1>
        <p>Low Stock Alerts</p>
    </div>
</div>

{{-- Low Stock Alerts --}}
@if($lowStock->count() > 0)
<h3 style="color:red">⚠️ Low Stock Alerts</h3>
<table>
    <tr>
        <th>Product</th>
        <th>Power</th>
        <th>Quantity</th>
    </tr>
    @foreach($lowStock as $item)
    <tr>
        <td>{{ $item->product->name }}</td>
        <td>{{ $item->power->getLabel() }}</td>
        <td style="color:red">{{ $item->quantity }}</td>
    </tr>
    @endforeach
</table>
@endif

{{-- Recent Transactions --}}
<h3>Recent Transactions</h3>
<table>
    <tr>
        <th>Date</th>
        <th>Product</th>
        <th>Power</th>
        <th>Type</th>
        <th>Quantity</th>
    </tr>
    @forelse($recentTransactions as $tx)
    <tr>
        <td>{{ $tx->created_at->format('d M Y, h:i A') }}</td>
        <td>{{ $tx->product->name }}</td>
        <td>{{ $tx->power->getLabel() }}</td>
        <td>
            @if($tx->type == 'IN')
                <span class="badge-in">IN</span>
            @else
                <span class="badge-out">OUT</span>
            @endif
        </td>
        <td>{{ $tx->quantity }}</td>
    </tr>
    @empty
    <tr>
        <td colspan="5" style="text-align:center">No transactions yet</td>
    </tr>
    @endforelse
</table>

</body>
</html>