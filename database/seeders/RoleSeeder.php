<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = ['admin', 'gestionnaire', 'prestataire', 'client'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]); 
        }
    }
}
