<!DOCTYPE html>
<html>
<head>
    <title>Stock IN</title>
</head>
<body>

<h2>Stock IN System</h2>

@if(session('success'))
    <p style="color:green">{{ session('success') }}</p>
@endif

<form method="POST" action="/stock-in">
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
    <input type="number" name="quantity">

    <br><br>

    <button type="submit">Add Stock</button>
</form>

</body>
</html>