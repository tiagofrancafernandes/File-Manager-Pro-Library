<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        $this->initialUsersSeeder();
    }

    public function initialUsersSeeder(): void
    {
        $adminUsers = [
            [
                'name' => 'Admin1',
                'email' => 'admin@mail.com',
                'username' => 'admin',
                'password' => Hash::make('power@123'),
            ],
        ];

        collect($adminUsers)->each(
            function ($userData) {
                /**
                 * @var array $fakeData
                 */
                $fakeData = collect(User::factory()->make($userData)->setHidden([]))->toArray();

                User::updateOrCreate(
                    [
                        'email' => $fakeData['email'],
                        'username' => $fakeData['username'],
                    ],
                    $fakeData
                );
            }
        );
    }
}
