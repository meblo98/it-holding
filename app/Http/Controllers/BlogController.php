<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        $posts = Post::where('published', true)->latest()->paginate(9);
        return view('pages.blog.index', compact('posts'));
    }

    public function show($slug)
    {
        $post = Post::where('slug', $slug)->where('published', true)->firstOrFail();
        return view('pages.blog.show', compact('post'));
    }
}
