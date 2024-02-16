<?php

namespace App\Http\Controllers;

use App\Exceptions\UserNotFoundException;
use App\Models\Friend;
use App\Http\Resources\Friend as FriendResource;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class FriendRequestController extends Controller
{
    public function store(Request $request)
    {
        // try {
        //     $data = $request->validate([
        //         'friend_id' => 'required',
        //     ]);
        // } catch (ValidationException $e) {
        //     // dd($e->getMessage());
        //     throw new ValidationErrorException(json_encode($e->errors()));
        // }
        //it is now done automatically with the help of Handler.php
        $data = $request->validate([
            'friend_id' => 'required',
        ]);
        try {
            User::findOrFail($data['friend_id'])
                ->friends()->syncWithoutDetaching(auth()->user());
        } catch (ModelNotFoundException $e) {
            throw new UserNotFoundException();
        }

        return new FriendResource(
            Friend::where('user_id', auth()->user()->id)
                ->where('friend_id', $data['friend_id'])
                ->first()
        );
    }
}
