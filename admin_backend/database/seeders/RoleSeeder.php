<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $roles = [
            [
                'id' => 1,
                'name' => 'user',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 11,
                'name' => 'seller',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 12,
                'name' => 'moderator',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 13,
                'name' => 'deliveryman',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 21,
                'name' => 'manager',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 99,
                'name' => 'admin',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        foreach ($roles as $role) {
            Role::updateOrInsert(['id' => $role['id']], $role);
        }
    }
}
