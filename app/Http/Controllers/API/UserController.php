<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Liste des utilisateurs (Accès admin uniquement).
     */
    public function index()
    {
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $users = User::all();
        return response()->json($users);
    }

    /**
     * Afficher un utilisateur spécifique (Accès restreint).
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

        // Un utilisateur ne peut voir que son propre profil sauf s'il est admin
        if (Auth::id() !== $user->id && !Auth::user()->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($user);
    }

    /**
     * Mise à jour d'un utilisateur (Ne permet PAS la modification du rôle sauf pour les admins).
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Vérifier si l'utilisateur est admin ou s'il modifie son propre compte
        if (!Auth::user()->hasRole('admin') && Auth::id() !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Empêcher la modification du rôle
        $validatedData = $request->except(['role_id']);

        $user->update($validatedData);

        return response()->json(['message' => 'User updated successfully', 'user' => $user]);
    }

    /**
     * Modification du rôle d’un utilisateur (Réservé aux admins).
     */
    public function updateRole(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Seuls les administrateurs peuvent modifier le rôle
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Vérifier que le rôle envoyé existe
        if (!$request->has('role_id')) {
            return response()->json(['error' => 'Role ID is required'], 400);
        }

        $role = Role::find($request->role_id);
        if (!$role) {
            return response()->json(['error' => 'Role not found'], 404);
        }

        // Mettre à jour uniquement le rôle
        $user->role_id = $request->role_id;
        $user->save();

        return response()->json(['message' => 'User role updated successfully', 'user' => $user]);
    }

    /**
     * Suppression d'un utilisateur (Réservé aux admins).
     */
    public function destroy($id)
    {
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    /**
     * Récupérer l'utilisateur actuellement connecté.
     */
    public function getUser(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Récupérer la liste des rôles (Accessible uniquement aux admins et gestionnaires-> le gestionnaire ne peut pas voir les rôles au dessus de lui)
     */
    public function getRoles()
{
    $user = Auth::user();

    if ($user->hasRole('admin')) {
        return response()->json(Role::all());
    }

    if ($user->hasRole('gestionnaire')) {
        return response()->json(Role::whereIn('name', ['prestataire', 'client'])->get());
    }

    return response()->json(['error' => 'Unauthorized'], 403);
}

}
