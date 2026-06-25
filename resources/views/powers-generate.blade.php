@extends('layouts.app')
@section('title', 'Generate Powers')

@section('content')

<h1 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">Generate Lens Powers</h1>

@if(session('success'))
    <div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 text-sm">{{ session('success') }}</div>
@endif

<div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 text-sm text-blue-700">
    💡 Category → Class → Subclass select karo, phir SPH/CYL range daal ke Generate karo.
</div>

<div class="bg-white rounded-xl shadow p-6 max-w-2xl">
    <form method="POST" action="/powers/generate">
        @csrf

        {{-- Category --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Category <span class="text-red-500">*</span>
            </label>
            <select id="category_id" name="category_id" required
                onchange="loadClasses(this.value)"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">-- Select Category --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Class --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Class <span class="text-red-500">*</span>
            </label>
            <select id="class_id" name="class_id" required
                onchange="loadSubclasses(this.value)"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">-- Select Class --</option>
            </select>
        </div>

        {{-- Subclass --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Subclass <span class="text-red-500">*</span>
            </label>
            <select id="subclass_id" name="subclass_id" required
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">-- Select Subclass --</option>
            </select>
        </div>

        {{-- SPH Range --}}
        <h3 class="font-semibold text-gray-700 mb-3">SPH Range</h3>
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Start (e.g. 0.00)</label>
                <input type="number" step="0.25" name="sph_start" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">End (e.g. -6.00)</label>
                <input type="number" step="0.25" name="sph_end" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Step</label>
                <input type="number" step="0.01" name="sph_step" value="0.25" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
        </div>

        {{-- CYL Range (Optional) --}}
        <h3 class="font-semibold text-gray-700 mb-1">CYL Range <span class="text-gray-400 text-xs font-normal">(Optional — sirf CYL wale subclasses ke liye)</span></h3>
        <p class="text-xs text-gray-400 mb-3">N-SPH / Hi-SPH ke liye CYL khali chhod do</p>
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Start (e.g. 0.00)</label>
                <input type="number" step="0.25" name="cyl_start"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">End (e.g. -4.00)</label>
                <input type="number" step="0.25" name="cyl_end"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Step</label>
                <input type="number" step="0.01" name="cyl_step" value="0.25"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
        </div>

        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-6 text-xs text-yellow-700">
            ⚠️ Total combinations = SPH values × CYL values. Bohot bada range mat banao ek sath.
        </div>

        <button type="submit"
            class="w-full bg-blue-700 hover:bg-blue-800 text-white font-semibold py-2 rounded-lg transition text-sm">
            🚀 Generate Powers
        </button>
    </form>
</div>

<div class="mt-4">
    <a href="/powers" class="text-blue-700 text-sm hover:underline">→ View All Powers</a>
</div>

<script>
function loadClasses(categoryId) {
    const classSelect = document.getElementById('class_id');
    const subclassSelect = document.getElementById('subclass_id');

    classSelect.innerHTML = '<option value="">-- Select Class --</option>';
    subclassSelect.innerHTML = '<option value="">-- Select Subclass --</option>';

    if (!categoryId) return;

    fetch(`/api/classes/${categoryId}`)
        .then(r => r.json())
        .then(classes => {
            classes.forEach(c => {
                classSelect.innerHTML += `<option value="${c.id}">${c.name}</option>`;
            });
        });
}

function loadSubclasses(classId) {
    const subclassSelect = document.getElementById('subclass_id');
    subclassSelect.innerHTML = '<option value="">-- Select Subclass --</option>';

    if (!classId) return;

    fetch(`/api/subclasses/${classId}`)
        .then(r => r.json())
        .then(subclasses => {
            subclasses.forEach(s => {
                subclassSelect.innerHTML += `<option value="${s.id}">${s.name}</option>`;
            });
        });
}
</script>

@endsection