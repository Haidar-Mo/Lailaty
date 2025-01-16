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
        Service::insert(
            [
                [
                    'name' => 'wedding',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'luxury',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'travel',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'taxi',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'mood',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'shipping',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'drive_lessons',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]
        );
    }
}
