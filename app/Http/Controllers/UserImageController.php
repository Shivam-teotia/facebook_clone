<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserImage;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

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
        // Image::make($data['image'])
        //     ->fit($data['width'], $data['height'])
        //     ->save(storage_path('app/public/user-images/' . $data['image']->hashName()));
        $userImage = auth()->user()->images()->create([
            'path' => $image,
            'height' => $data['height'],
            'width' => $data['width'],
            'location' => $data['location'],
        ]);
        return new UserImage($userImage);
    }
}
