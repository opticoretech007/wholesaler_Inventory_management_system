@extends('layouts.app')
@section('title', 'Stock Grid')

@section('content')

<h1 class="text-2xl font-bold text-gray-800 mb-6">Stock Grid</h1>

<div class="bg-white rounded-xl shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-blue-800 text-white">
                <th class="px-4 py-3 text-left">Power</th>
                @foreach($products as $product)
                    <th class="px-4 py-3 text-center">{{ $product->name }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($powers as $power)
            <tr class="border-b hover:bg-gray-50">
                <td class="px-4 py-3 font-medium text-gray-700">
                    {{ $power->getLabel() }}
                </td>
                @foreach($products as $product)
                    @php $qty = $grid[$power->id][$product->id]; @endphp
                    <td class="px-4 py-3 text-center">
                        @if($qty == 0)
                            <span class="text-gray-300">—</span>
                        @elseif($qty < 5)
                            <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold">
                                {{ $qty }}
                            </span>
                        @else
                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">
                                {{ $qty }}
                            </span>
                        @endif
                    </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection