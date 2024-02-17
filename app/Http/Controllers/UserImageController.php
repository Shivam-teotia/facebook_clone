<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserImage;
use Illuminate\Http\Request;

class UserImageController extends Controller
{
    public function store()
    {
        $data = request()->validate([
            'image' => '',
            'width' => '',
            'height' => '',
            'location' => '',
        ]);
        $image = $data['image']->store('user-images', 'public');
        $userImage = auth()->user()->images()->create([
            'path' => $image,
            'height' => $data['height'],
            'width' => $data['width'],
            'location' => $data['location'],
        ]);
        return new UserImage($userImage);
    }
}
