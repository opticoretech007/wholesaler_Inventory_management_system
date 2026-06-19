<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Power;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        \App\Models\StockTransaction::truncate();
        \App\Models\Stock::truncate();
        Product::truncate();
        Power::truncate();

        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Products
        Product::create(['name' => 'Hoaming Anti-Glare']);
        Product::create(['name' => 'FromEyes Blue Cut']);

        // Powers ab manually /powers/generate page se add hongi
        // (category-wise: hoaming-normal, hoaming-cross, hoaming-high,
        //  fromeyes-normal, fromeyes-high, fromeyes-cross)

        \App\Models\User::create([
    'name' => 'admin',
    'email' => 'admin@opticore.com',
    'password' => 'mypass123', // plain text yahan likho
]);
    }
}