<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        $posts = Post::where('published', true)->latest()->paginate(8);
        $latestPosts = Post::where('published', true)->latest()->take(3)->get();
        $categories = Post::where('published', true)->whereNotNull('category')->distinct()->pluck('category');
        
        return view('pages.blog.index', compact('posts', 'latestPosts', 'categories'));
    }

    public function show($slug)
    {
        $post = Post::where('slug', $slug)->where('published', true)->firstOrFail();
        $latestPosts = Post::where('published', true)->latest()->take(3)->get();
        $categories = Post::where('published', true)->whereNotNull('category')->distinct()->pluck('category');
        
        return view('pages.blog.show', compact('post', 'latestPosts', 'categories'));
    }
}
