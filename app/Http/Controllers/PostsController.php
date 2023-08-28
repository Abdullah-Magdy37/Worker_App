<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddPostRequest;
use App\Models\Post;

class PostsController extends Controller {
    public function addPost(AddPostRequest $request) {
        $worker = auth('worker')->user();
        $post   = Post::create($request->validated() + ['worker_id' => $worker->id]);
        return response()->json([
          'message' => 'post added successfully' ,
          'post' => $post
        ] , 200);

    }
}
