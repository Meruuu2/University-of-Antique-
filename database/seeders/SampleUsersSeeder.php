<?php

namespace Database\Seeders;

use App\Models\College;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SampleUsersSeeder extends Seeder
{
    /**
     * Seed the application's sample users.
     */
    public function run(): void
    {
        $password = Hash::make('password123');

        // Admins (keep existing placeholder admins)
        foreach (['A', 'B', 'C'] as $letter) {
            User::updateOrCreate(
                ['email' => "admin{$letter}@example.com"],
                [
                    'name' => "Admin {$letter}",
                    'password' => $password,
                    'role' => 'admin',
                    'college_name' => null,
                    'college_id' => null,
                    'organization_name' => null,
                ]
            );
        }

        // College staff (official test accounts)
        $collegeUsers = [
            'CCIS' => ['email' => 'college@ccis.com', 'name' => 'CCIS STAFF'],
            'CEA'  => ['email' => 'college@cea.com',  'name' => 'CEA STAFF'],
            'CMG'  => ['email' => 'college@cmg.com',  'name' => 'CMG STAFF'],
        ];

        foreach ($collegeUsers as $collegeName => $u) {
            $collegeId = College::where('name', $collegeName)->value('id');

            User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'password' => $password,
                    'role' => 'college_staff',
                    'college_name' => $collegeName,
                    'college_id' => $collegeId,
                    'organization_name' => null,
                ]
            );
        }

        // Org staff (keep placeholders)
        foreach (['A', 'B', 'C'] as $letter) {
            User::updateOrCreate(
                ['email' => "org{$letter}@example.com"],
                [
                    'name' => "Org {$letter} Staff",
                    'password' => $password,
                    'role' => 'org_staff',
                    'college_name' => null,
                    'college_id' => null,
                    'organization_name' => "Org {$letter}",
                ]
            );
        }

    }
}
