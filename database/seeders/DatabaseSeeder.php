<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\CarBrand;
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
            'birth_date' => '2000/01/01',
            'deviceToken' => 'some_random_device_token',
            'is_active' => true,
        ]);
        $haidar = User::create([
            'email' => 'mohammad44.hiadar@gmail.com',
            'password' => bcrypt('password'),
            'phone_number' => '+200936287134',
            'first_name' => 'mohammad',
            'last_name' => 'haidar',
            'gender' => 'male',
            'birth_date' => '2000/01/01',
            'deviceToken' => 'QWERTYUIOPASDFGHJKLZXCVBNM',
            'is_active' => true,
        ]);


        $this->call(RolesSeeder::class);

        $this->call(PermissionSeeder::class);

        $this->call(CarBrandSeed::class);

        $this->call(ServicesSeeder::class);

        $this->call(AssignPermissionsToRolesSeeder::class);

        $mario->assignRole('freeDriver');
        $haidar->assignRole('fleetOwner');

    }
}
