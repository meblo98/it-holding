<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Post;
use App\Models\Product;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        // Fetch data for home page sections
        $services = Service::where('active', true)->take(3)->get();
        // $posts = Post::where('published', true)->latest()->take(3)->get();
        
        return view('welcome', compact('services'));
    }

    public function about()
    {
        return view('pages.about');
    }
}
