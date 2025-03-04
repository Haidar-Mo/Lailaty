<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'insert-registration-document', 'guard_name' => 'api'],
            ['name' => 'insert-profile-image', 'guard_name' => 'api'],
            ['name' => 'create-office', 'guard_name' => 'api'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission['name'],
                'guard_name' => $permission['guard_name']
            ]);
        }



        $this->command->info('Permissions has been seeded');
    }
}
