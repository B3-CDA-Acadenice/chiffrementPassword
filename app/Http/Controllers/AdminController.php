<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Logique pour l'accès au dashboard admin
        return response()->json(['message' => 'Admin Dashboard']);
    }
}
