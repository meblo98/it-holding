<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::where('active', true)->get();
        $latestPosts = \App\Models\Post::where('active', true)->latest()->take(3)->get();
        return view('pages.services.index', compact('services', 'latestPosts'));
    }

    public function show($slug)
    {
        $service = Service::where('slug', $slug)->where('active', true)->firstOrFail();
        $otherServices = Service::where('active', true)->where('id', '!=', $service->id)->take(5)->get();
        $latestPosts = \App\Models\Post::where('active', true)->latest()->take(3)->get();
        return view('pages.services.show', compact('service', 'otherServices', 'latestPosts'));
    }
}
