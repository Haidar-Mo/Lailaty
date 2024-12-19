<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles =
            [
                [
                    'name' => 'admin',
                    'guard_name' => 'api'
                ],
                [
                    'name' => 'officeOwner',
                    'guard_name' => 'api'
                ],
                [
                    'name' => 'freeDriver',
                    'guard_name' => 'api'
                ],
                [
                    'name' => 'employeeDriver',
                    'guard_name' => 'api'
                ],
                [
                    'name' => 'client',
                    'guard_name' => 'api'
                ],
            ];

        foreach ($roles as $role) {
            Role::firstOrCreate([
                'name' => $role['name'],
                'guard_name' => $role['guard_name'],
            ]);
        }


        $this->command->info('Roles has been seeded');

    }
}
