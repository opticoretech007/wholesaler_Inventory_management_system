<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Power;
use App\Models\Category;
use App\Models\LensClass;
use App\Models\Subclass;
use App\Models\Stock;
use App\Models\StockTransaction;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        StockTransaction::truncate();
        Stock::truncate();
        Power::truncate();
        Subclass::truncate();
        \DB::table('classes')->truncate();
        Category::truncate();
        Product::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Products
        Product::create(['name' => 'Hoaming Anti-Glare']);
        Product::create(['name' => 'FromEyes Blue Cut']);

        // Categories
        $categories = ['SHMC+/-CC', 'SHMC+', 'SHMC-'];
        $categoryModels = [];
        foreach ($categories as $cat) {
            $categoryModels[$cat] = Category::create(['name' => $cat]);
        }

        // Classes per category
        $classData = [
            'SHMC+/-CC' => ['SHMC+ 1.56 Index 60mm', 'SHMC+ 1.56 Index 55mm', 'SHMC- 1.56 Index'],
            'SHMC+'     => ['SHMC+ 1.56 Index 60mm', 'SHMC+ 1.56 Index 55mm'],
            'SHMC-'     => ['SHMC- 1.56 Index'],
        ];

        $classModels = [];
        foreach ($classData as $catName => $classes) {
            foreach ($classes as $className) {
                $classModels[$catName . '_' . $className] = LensClass::create([
                    'category_id' => $categoryModels[$catName]->id,
                    'name'        => $className,
                ]);
            }
        }

        // Subclasses
        $subclassNames = ['Cross-Compound', 'Hi-CYL', 'N-CYL', 'Hi-SPH', 'N-SPH'];

        foreach ($classModels as $key => $classModel) {
            foreach ($subclassNames as $subName) {
                Subclass::create([
                    'class_id' => $classModel->id,
                    'name'     => $subName,
                ]);
            }
        }

        // Admin user
        \App\Models\User::firstOrCreate(
            ['email' => 'admin@opticore.com'],
            [
                'name'     => 'Admin',
                'password' => bcrypt('mypass123'),
            ]
        );
    }
}