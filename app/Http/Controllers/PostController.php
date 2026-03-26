<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\PostLike;

class PostController extends Controller
{
    public function show(Request $request)
    {
        return view('partials.post', ['id' => $request.id]);
    }

    public function like($id)
    {
        event(new PostLike($id));

        return response()->json(['status' => 'liked']);
    }
}
