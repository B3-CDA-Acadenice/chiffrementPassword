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
    // Validation stricte pour éviter toute donnée non prévue
    $validatedData = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (!Auth::attempt($validatedData)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $user = Auth::user();
    $token = $user->createToken('authToken')->plainTextToken;

    return response()->json([
        'message' => 'Login successful',
        'token' => $token,
        'user' => $user
    ]);

    // Déchiffrement du mot de passe
    $decryptedPassword = $this->decryptPassword($request->password);

    if (Auth::attempt(['email' => $request->email, 'password' => $decryptedPassword])) {
        $user = Auth::user();
        $token = $user->createToken('authToken')->plainTextToken;
        return response()->json(['token' => $token], 200);
    }

    return response()->json(['message' => 'Invalid credentials'], 401);
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
