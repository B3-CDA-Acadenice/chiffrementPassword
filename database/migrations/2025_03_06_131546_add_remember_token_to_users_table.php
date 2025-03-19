<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Appliquer la migration : Ajout de remember_token à la table users.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Vérifie si la colonne n'existe pas déjà avant de l'ajouter
            if (!Schema::hasColumn('users', 'remember_token')) {
                $table->string('remember_token')->nullable();
            }
        });
    }

    /**
     * Annuler la migration : Suppression de remember_token.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Vérifie si la colonne existe avant de la supprimer
            if (Schema::hasColumn('users', 'remember_token')) {
                $table->dropColumn('remember_token');
            }
        });
    }
};
