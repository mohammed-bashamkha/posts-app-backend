<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Post;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        try
        {
            $validated = $request->validate([
            'content' => 'required|string|max:500',
            'commentable_id' => 'required|integer',
            'commentable_type' => 'required|string|in:post,image,video',
            ]);

            $model = match ($validated['commentable_type'])
            {
                'post' => Post::class,
                'image' => Image::class,
                'video' => Video::class,
            };
            $commentable = $model::findOrFail($validated['commentable_id']);
            $comment = $commentable->comments()->create(['content' => $validated['content']]);

            return response()->json([
                'message' => 'Comment Created Successfully',
                'comment' => $comment
            ]);
        }
        catch(\Exception $e)
        {
            return response()->json([
            'Message' => 'Failed To Create Comment!',
            'Error' => $e
            ]); 
        }
    }
}
