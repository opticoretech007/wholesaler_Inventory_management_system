<?php

// namespace Database\Seeders;

// use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
// use Illuminate\Database\Seeder;

// class DatabaseSeeder extends Seeder
// {
//     use WithoutModelEvents;

//     /**
//      * Seed the application's database.
//      */
//     public function run(): void
//     {
//         // User::factory(10)->create();

//         User::factory()->create([
//             'name' => 'Test User',
//             'email' => 'test@example.com',
//         ]);
//     }
// }


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Power;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Product::truncate();
        Power::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Product::create(['name' => 'Hoaming Anti-Glare']);
        Product::create(['name' => 'FromEyes Blue Cut']);

        // SPH only powers
        $sphPowers = [
            '+0.25',
            '+0.50',
            '+0.75',
            '+1.00',
            '+1.25',
            '+1.50',
            '+1.75',
            '+2.00',
            '+2.25',
            '+2.50',
            '+2.75',
            '+3.00',
            '-0.25',
            '-0.50',
            '-0.75',
            '-1.00',
            '-1.25',
            '-1.50',
            '-2.00',
            '-2.25',
            '-2.50',
            '-2.75',
            '-3.00'
        ];

        foreach ($sphPowers as $sph) {
            Power::create(['sph' => $sph, 'cyl' => null]);
        }

        // SPH + CYL powers
        $sphCylPowers = [
            ['-1.00', '-0.50'],
            ['-1.00', '-0.75'],
            ['-1.00', '-1.00'],
            ['-2.00', '-0.50'],
            ['-2.00', '-0.75'],
            ['-2.00', '-1.00'],
            ['+1.00', '-0.50'],
            ['+1.00', '-0.75'],
            ['+1.00', '-1.00'],
        ];

        foreach ($sphCylPowers as $p) {
            Power::create(['sph' => $p[0], 'cyl' => $p[1]]);
        }
    }
}