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
            'title'    => 'nullable|string|max:255',
            'content'  => 'nullable|string',
            'description' => 'nullable|string|max:500',
            'url'   => 'required|array|min:1',
            'url.*' => 'image|mimes:jpg,png,jpeg|max:2048',
        ]);
            $post = Post::create([
                'user_id' => Auth::id(),
                'type'    => 'image',
                'title'   => $request->title,
            ]);

            foreach ($request->file('url') as $url) 
            {
                $path = $this->uploadFile($url, 'posts/images', 'public');

                $post->images()->create([
                    'user_id' => Auth::id(),
                    'post_id' => $post->id,
                    'title'   => $request->title,
                    'url'     => $path,
                    'description' => $request->description,
                ]);
            }

            return response()->json([
                'message' => 'Image post created successfully',
                'post' => $post
            ], 201);
        }
        catch(Exception $e)
        {
            return response()->json([
                'message' => 'Failed To Create Post',
                'error' => $e->getMessage()
            ]);
        }
    }

}
