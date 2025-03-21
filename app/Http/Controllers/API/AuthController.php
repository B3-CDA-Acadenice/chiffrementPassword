<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use phpseclib3\Crypt\AES; // Import de la classe AES

class AuthController extends Controller
{
    public function register(Request $request)
{
    try {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Correction : Ajout de `role_id` avec la valeur par défaut
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role_id' => 4, // 👈 Assigne automatiquement le rôle "Client" (à adapter selon la BDD)
        ]);

        return response()->json($user, 201);

    } catch (\Exception $e) {
        return response()->json(['error' => 'Server error', 'message' => $e->getMessage()], 500);
    }
}


//ajout de la méthode de chiffrement
private $secretKey = "MaCléSecrèteUltraSécure"; // Doit être la même que sur le Front

// Méthode pour déchiffrer un mot de passe
public function decryptPassword($encryptedPassword)
    {
        $aes = new AES('cbc');
        $aes->setKey($this->secretKey);
        return $aes->decrypt(base64_decode($encryptedPassword));
    }

    public function login(Request $request)
{
    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    // Déchiffrement du mot de passe reçu
    try {
        $decryptedPassword = Crypt::decryptString($request->password);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error decrypting password'], 400);
    }

    // Vérification avec le hash en base
    if (!Hash::check($decryptedPassword, $user->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    return response()->json(['token' => $user->createToken('API Token')->plainTextToken]);
}


    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->tokens()->delete(); 
            $request->user()->update(['remember_token' => null]);

            return response()->json(['message' => 'Logged out successfully']);
        }

        return response()->json(['message' => 'No authenticated user'], 401);
    }
}
