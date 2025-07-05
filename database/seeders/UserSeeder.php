<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'John Administrator',
                'email' => 'admin@citruse.com',
                'role' => 'system_administrator',  // Changed to match database
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Sarah Purchase Manager',
                'email' => 'pm@citruse.com',
                'role' => 'purchasing_manager',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Mike Field Sales',
                'email' => 'fsa@citruse.com',
                'role' => 'field_sales_associate',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Jane Purchasing',
                'email' => 'jane@citruse.com',
                'role' => 'purchasing_manager',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'David Sales',
                'email' => 'david@citruse.com',
                'role' => 'field_sales_associate',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            $this->command->info("Created/Updated user: {$userData['name']} ({$userData['role']})");
        }

        $this->command->info('Test users created successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('- admin@citruse.com / password (System Admin)');
        $this->command->info('- pm@citruse.com / password (Purchasing Manager)');
        $this->command->info('- fsa@citruse.com / password (Field Sales Associate)');
    }
}