<!DOCTYPE html>
<html>
<head>
    <title>Stock OUT</title>
</head>
<body>

<h2>Stock OUT System</h2>

<a href="/stock-in">Go to Stock IN</a>

<br><br>

@if(session('success'))
    <p style="color:green">{{ session('success') }}</p>
@endif

@if(session('error'))
    <p style="color:red">{{ session('error') }}</p>
@endif

<form method="POST" action="/stock-out">
    @csrf

    <label>Product:</label>
    <select name="product_id">
        @foreach($products as $product)
            <option value="{{ $product->id }}">{{ $product->name }}</option>
        @endforeach
    </select>

    <br><br>

    <label>Power:</label>
    <select name="power_id">
        @foreach($powers as $power)
            <option value="{{ $power->id }}">{{ $power->getLabel() }}</option>
        @endforeach
    </select>

    <br><br>

    <label>Quantity:</label>
    <input type="number" name="quantity" min="1">

    <br><br>

    <button type="submit">Stock OUT</button>
</form>

</body>
</html>