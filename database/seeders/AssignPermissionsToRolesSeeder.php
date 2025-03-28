<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AssignPermissionsToRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::findByName('admin');
        $client = Role::findByName('client');
        $fleetOwner = Role::findByName('fleetOwner');
        $freeDriver = Role::findByName('freeDriver');
        $employeeDriver = Role::findByName('employeeDriver');

        // Assign permissions for Admin Role
        if ($admin) {
            $admin->syncPermissions([
                '',
                ''
            ]);

            $this->command->info('Permissions assigned to admin role successfully.');
        } else {
            $this->command->error('Role "admin" not found. Please ensure it is exists.');
        }

        // Assign permissions for Client Role
        if ($client) {
            $client->syncPermissions([
                '',
                ''
            ]);

            $this->command->info('Permissions assigned to client role successfully.');
        } else {
            $this->command->error('Role "client" not found. Please ensure it is exists.');
        }

        // Assign permissions for Office-Owner Role
        if ($fleetOwner) {
            $fleetOwner->syncPermissions([
                'create-office',
                ''
            ]);

            $this->command->info('Permissions assigned to fleetOwner role successfully.');
        } else {
            $this->command->error('Role "fleetOwner" not found. Please ensure it is exists.');
        }


        // Assign permissions for free-driver Role
        if ($freeDriver) {
            $freeDriver->syncPermissions([
                'insert-profile-image',
                'insert-registration-document'
            ]);

            $this->command->info('Permissions assigned to freeDriver role successfully.');
        } else {
            $this->command->error('Role "freeDriver" not found. Please ensure it is exists.');
        }


        // Assign permissions for employee-driver Role
        if ($employeeDriver) {
            $employeeDriver->syncPermissions([
                'insert-profile-image',
                'insert-registration-document'
            ]);

            $this->command->info('Permissions assigned to employeeDriver role successfully.');
        } else {
            $this->command->error('Role "employeeDriver" not found. Please ensure it is exists.');
        }
    }
}
