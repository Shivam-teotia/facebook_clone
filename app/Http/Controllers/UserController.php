<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Resources\User as UserResource;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {

    }
    public function show(User $user)
    {
        return new UserResource($user);
    }
}
