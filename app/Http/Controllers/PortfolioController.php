<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    public function index()
    {
        $projects = Project::latest()->get();
        $latestPosts = \App\Models\Post::where('active', true)->latest()->take(3)->get();
        return view('pages.portfolio.index', compact('projects', 'latestPosts'));
    }

    public function show($slug)
    {
        $project = Project::where('slug', $slug)->firstOrFail();
        $otherProjects = Project::where('id', '!=', $project->id)->latest()->take(3)->get();
        $latestPosts = \App\Models\Post::where('active', true)->latest()->take(3)->get();
        return view('pages.portfolio.show', compact('project', 'otherProjects', 'latestPosts'));
    }
}
