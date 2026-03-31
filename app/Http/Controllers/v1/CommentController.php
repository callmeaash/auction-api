<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Comment;
use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    /**
     * Create a new comment.
     * 
     * @authenticated
     * 
     * @param Request $request Validated comment data.
     * @param Item $item The item to comment on.
     * @return JsonResponse Returns the created comment.
     */
    public function store(Request $request, Item $item) {
        $validated = $request->validate([
            'comment' => 'required|string',
        ]);

        $comment = $item->comments()->create([
            'user_id' => auth('sanctum')->id(),
            'comment' => $validated['comment'],
        ]);

        $comment->load('user');

        return $this->success(new CommentResource($comment), 'Comment created successfully');
    }
}
