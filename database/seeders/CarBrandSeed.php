<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CarBrand;

class CarBrandSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $brands = [
            [
                'name' => 'مرسيدس',
            ],
            [
                'name' => 'كيا'
            ],


        ];
        foreach ($brands as $brand) {
            CarBrand::create($brand);
        }
    }
}
