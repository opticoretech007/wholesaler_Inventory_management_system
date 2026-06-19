<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Power;
use App\Models\Product;

class PowerGeneratorController extends Controller
{
    public function form()
    {
        $products = Product::all();
        return view('powers-generate', compact('products'));
    }

    public function index(Request $request)
{
    $query = Power::query();

    if ($request->filled('category')) {
        $query->where('category', $request->category);
    }

    $powers = $query->orderBy('category')->orderBy('sph')->paginate(50)->withQueryString();

    $categories = Power::select('category')->distinct()->pluck('category');

    return view('powers-list', compact('powers', 'categories'));
}

    public function generate(Request $request)
    {
        $request->validate([
            'category'  => 'required|string',
            'sph_start' => 'required|numeric',
            'sph_end'   => 'required|numeric',
            'sph_step'  => 'required|numeric|min:0.01',
            'cyl_start' => 'required|numeric',
            'cyl_end'   => 'required|numeric',
            'cyl_step'  => 'required|numeric|min:0.01',
        ]);

        $sphValues = $this->generateRange($request->sph_start, $request->sph_end, $request->sph_step);
        $cylValues = $this->generateRange($request->cyl_start, $request->cyl_end, $request->cyl_step);

        $created = 0;
        $skipped = 0;

        foreach ($sphValues as $sph) {
            foreach ($cylValues as $cyl) {
                $exists = Power::where('sph', $sph)
                                ->where('cyl', $cyl)
                                ->where('category', $request->category)
                                ->exists();

                if (!$exists) {
                    Power::create([
                        'sph' => $sph,
                        'cyl' => $cyl,
                        'category' => $request->category
                    ]);
                    $created++;
                } else {
                    $skipped++;
                }
            }
        }

        return redirect('/powers')->with('success', "✅ [{$request->category}] $created powers created. $skipped duplicates skipped.");
    }

    private function generateRange($start, $end, $step)
    {
        $values = [];
        $current = $start;

        if ($start <= $end) {
            while ($current <= $end + 0.0001) {
                $values[] = $this->formatPower($current);
                $current += $step;
            }
        } else {
            while ($current >= $end - 0.0001) {
                $values[] = $this->formatPower($current);
                $current -= $step;
            }
        }

        return $values;
    }

    private function formatPower($value)
    {
        $formatted = number_format($value, 2);
        if ($value > 0) {
            $formatted = '+' . $formatted;
        } elseif ($value == 0) {
            $formatted = '0.00';
        }
        return $formatted;
    }

    public function destroy($id)
    {
        Power::findOrFail($id)->delete();
        return back()->with('success', 'Power deleted successfully!');
    }

    public function destroyCategory($category)
{
    $count = Power::where('category', $category)->count();
    Power::where('category', $category)->delete();

    return back()->with('success', "🗑️ Category '$category' deleted ($count powers removed).");
}
}