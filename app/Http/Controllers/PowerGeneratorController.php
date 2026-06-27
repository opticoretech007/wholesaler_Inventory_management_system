<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Power;
use App\Models\Category;
use App\Models\LensClass;
use App\Models\Subclass;

class PowerGeneratorController extends Controller
{
    public function form()
    {
        $categories = Category::with('classes.subclasses')->get();
        return view('powers-generate', compact('categories'));
    }

    public function index(Request $request)
    {
        $query = Power::with('subclass.lensClass.category');

        if ($request->filled('subclass_id')) {
            $query->where('subclass_id', $request->subclass_id);
        } elseif ($request->filled('class_id')) {
            $subclassIds = Subclass::where('class_id', $request->class_id)->pluck('id');
            $query->whereIn('subclass_id', $subclassIds);
        } elseif ($request->filled('category_id')) {
            $classIds = LensClass::where('category_id', $request->category_id)->pluck('id');
            $subclassIds = Subclass::whereIn('class_id', $classIds)->pluck('id');
            $query->whereIn('subclass_id', $subclassIds);
        }

        $powers = $query->orderBy('sph')->paginate(50)->withQueryString();
        $categories = Category::with('classes.subclasses')->get();
        $totalPowers = Power::count();

        return view('powers-list', compact('powers', 'categories', 'totalPowers'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'subclass_id' => 'required|exists:subclasses,id',
            'sph_start'   => 'required|numeric',
            'sph_end'     => 'required|numeric',
            'sph_step'    => 'required|numeric|min:0.01',
            'cyl_start'   => 'nullable|numeric',
            'cyl_end'     => 'nullable|numeric',
            'cyl_step'    => 'nullable|numeric|min:0.01',
        ]);

        $subclass = Subclass::with('lensClass.category')->find($request->subclass_id);

        $sphValues = $this->generateRange($request->sph_start, $request->sph_end, $request->sph_step);

        $cylValues = [null];
        if ($request->filled('cyl_start') && $request->filled('cyl_end')) {
            $cylValues = $this->generateRange($request->cyl_start, $request->cyl_end, $request->cyl_step ?? 0.25);
        }

        $created = 0;
        $skipped = 0;

        foreach ($sphValues as $sph) {
            foreach ($cylValues as $cyl) {
                $exists = Power::where('sph', $sph)
                               ->where('cyl', $cyl)
                               ->where('subclass_id', $request->subclass_id)
                               ->exists();

                if (!$exists) {
                    Power::create([
                        'sph'         => $sph,
                        'cyl'         => $cyl,
                        'category'    => $subclass->lensClass->category->name,
                        'subclass_id' => $request->subclass_id,
                    ]);
                    $created++;
                } else {
                    $skipped++;
                }
            }
        }

        return redirect('/powers')->with('success',
            "✅ [{$subclass->lensClass->category->name} → {$subclass->lensClass->name} → {$subclass->name}] $created powers created. $skipped duplicates skipped.");
    }

    private function generateRange($start, $end, $step)
    {
        $values = [];
        $current = $start;

        if ($start <= $end) {
            while ($current <= $end + 0.0001) {
                $values[] = $this->formatPower($current);
                $current = round($current + $step, 2);
            }
        } else {
            while ($current >= $end - 0.0001) {
                $values[] = $this->formatPower($current);
                $current = round($current - $step, 2);
            }
        }

        return $values;
    }

    private function formatPower($value)
    {
        $formatted = number_format($value, 2);
        if ($value > 0) $formatted = '+' . $formatted;
        elseif ($value == 0) $formatted = '0.00';
        return $formatted;
    }

    public function destroy($id)
    {
        Power::findOrFail($id)->delete();
        return back()->with('success', 'Power deleted!');
    }

    public function destroyCategory($category)
    {
        $count = Power::where('category', $category)->count();
        Power::where('category', $category)->delete();
        return back()->with('success', "🗑️ '$category' deleted ($count powers removed).");
    }

    // API endpoints for dynamic dropdowns
    public function getClasses($categoryId)
    {
        $classes = LensClass::where('category_id', $categoryId)->get();
        return response()->json($classes);
    }

    public function getSubclasses($classId)
    {
        $subclasses = Subclass::where('class_id', $classId)->get();
        return response()->json($subclasses);
    }
    public function getPowers($subclassId)
{
    $powers = Power::where('subclass_id', $subclassId)
                   ->orderBy('sph')
                   ->orderBy('cyl')
                   ->get()
                   ->map(fn($p) => [
                       'id'    => $p->id,
                       'label' => $p->getLabel(),
                   ]);

    return response()->json($powers);
}

public function destroyAll()
{
    \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    Power::truncate();
    \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    return redirect('/powers')->with('success', '🗑️ All powers deleted successfully!');
}
}