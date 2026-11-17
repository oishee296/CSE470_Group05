<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['Test Citizen', 'citizen@share.test', User::ROLE_CITIZEN],
            ['Test Donor', 'donor@share.test', User::ROLE_DONOR],
            ['Test Logistics', 'logistics@share.test', User::ROLE_LOGISTICS],
            ['Command Admin', 'admin@share.test', User::ROLE_ADMIN],
        ];

        foreach ($accounts as [$name, $email, $role]) {
            User::updateOrCreate(
                ['email' => $email],
                ['name' => $name, 'password' => 'password123', 'role' => $role]
            );
        }
    }
}