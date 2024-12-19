<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Service;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call(RolesSeeder::class);

        $this->call(PermissionSeeder::class);
        
        $this->call(AssignPermissionsToRolesSeeder::class);
        


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


    }
}
