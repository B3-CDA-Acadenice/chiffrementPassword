<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
{
    // Exécuter les seeders des rôles en premier
    $this->call(RoleSeeder::class);

    // Vérifier que le rôle 'admin' existe bien avant d'affecter l'utilisateur
    $adminRole = Role::where('name', 'admin')->first();

    if (!$adminRole) {
        throw new \Exception('Le rôle admin n\'existe pas. Vérifie le RoleSeeder.');
    }

    // Ensuite, insérer un utilisateur de test avec un rôle valide
    User::factory()->create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
        'role_id' => $adminRole->id, // Récupération correcte du rôle
    ]);
}

}
