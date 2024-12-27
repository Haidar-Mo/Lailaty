<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Service;
use Illuminate\Database\Seeder;
use App\Models\User;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $mario = User::create([
            'email' => 'example@gmail.com',
            'password' => bcrypt('password'),
            'phone_number' => '+201231231236',
            'first_name' => 'ماريو',
            'last_name' => 'اندراوس',
            'gender' => 'male',
            'deviceToken' => 'some_random_device_token',
            'is_active' => true,
            'rate' => 5,
            'email_verified_at' => now(),
        ]);
        $mario->assignRole('freeDriver');
/*
        $this->call(RolesSeeder::class);

        $this->call(PermissionSeeder::class);

        $this->call(AssignPermissionsToRolesSeeder::class);*/



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
