<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use Illuminate\Http\Request;
use App\UploadFileTrait;
use App\Models\Post;
use Exception;
use Illuminate\Support\Facades\Auth;

class ImageController extends Controller
{
    use UploadFileTrait;

    public function index()
    {
        $vediosPosts = Post::with('images')->where('type', 'images')->get();
        return response()->json([
            'message' => 'Image Posts Retrieved Successfully',
            'posts' => $vediosPosts,
        ], 200);
    }
    public function store(StorePostRequest $request)
    {
        try
        {
            $request->validated();
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
