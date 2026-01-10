<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UploadFileTrait;
use App\Models\Post;
use Exception;
use Illuminate\Support\Facades\Auth;

class ImageController extends Controller
{
    use UploadFileTrait;

    public function store(Request $request)
    {
        try
        {
            $request->validate([
            'description' => 'nullable|string|max:500',
            'image_url'   => 'required|array|min:1',
            'image_url.*' => 'image|mimes:jpg,png,jpeg|max:2048',
        ]);
            $post = Post::create([
                'user_id' => Auth::id(),
                'type'    => 'image',
            ]);

            foreach ($request->file('image_url') as $url)
            {
                $path = $this->uploadFile($url, 'posts/images', 'public');

                $post->images()->create([
                    'user_id' => Auth::id(),
                    'post_id' => $post->id,
                    'image_url'     => $path,
                    'description' => $request->description,
                ]);
            }

            return response()->json([
                'message' => 'Image post created successfully',
                'post' => $post,
                'images' => $path,
                'description' => $request->description,
            ], 201);
        }
        catch(Exception $e)
        {
            return response()->json([
                'message' => 'Failed To Create Image Post',
                'error' => $e->getMessage()
            ]);
        }
    }

}
