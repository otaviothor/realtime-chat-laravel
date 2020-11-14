<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function index()
    {
        $userLogged = Auth::user();
//        $users = User::where('id', '!=', $userLogged->id)->get();
        $users = User::where('id', '!=', 1)->get();

        return response()->json([
            'users' => $users
        ], Response::HTTP_OK);
    }

    public function show(User $user)
    {
        return response()->json([
            'user' => $user
        ], Response::HTTP_OK);
    }
}