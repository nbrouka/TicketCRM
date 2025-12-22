<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    private const ADMIN_EMAIL = 'admin@example.com';

    private const ADMIN_NAME = 'Admin User';

    private const DEFAULT_PASSWORD = 'password123';

    private const MANAGER_COUNT = 5;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create one admin user
        $admin = User::updateOrCreate(
            ['email' => self::ADMIN_EMAIL],
            [
                'name' => self::ADMIN_NAME,
                'email' => self::ADMIN_EMAIL,
                'password' => bcrypt(self::DEFAULT_PASSWORD),
            ]
        );
        $admin->assignRole('admin');

        // Create manager users
        for ($i = 1; $i <= self::MANAGER_COUNT; $i++) {
            $manager = User::updateOrCreate(
                ['email' => "manager{$i}@example.com"],
                [
                    'name' => "Manager User {$i}",
                    'email' => "manager{$i}@example.com",
                    'password' => bcrypt(self::DEFAULT_PASSWORD),
                ]
            );
            $manager->assignRole('manager');
        }
    }
}
