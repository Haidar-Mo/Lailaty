<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;

class ServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*Service::insert(
           [
               [
                   'name' => 'in_city'
               ],
               [
                   'name' => 'luxury'
               ],
               [
                   'name' => 'across_cities'
               ],
               [
                   'name' => 'wedding'
               ],
               [
                   'name' => 'your_mood'
               ],
               [
                   'name' => 'driving_teaching'
               ],
               [
                   'name' => 'shipping'
               ],
           ]
       );*/
        $services = [
            [
                'name' => 'زفاف',
            ],
            [
                'name' => 'سفر'
            ],
            [
                'name' => 'داخلي'
            ],
            [
                'name' => 'على مودك'
            ],
            [
                'name' => 'شحن'
            ],
            [
                'name' => 'تعليم قيادة'
            ],

        ];
        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
