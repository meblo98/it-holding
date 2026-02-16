<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::latest()->paginate(10);
        return view('admin.projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.projects.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'client' => 'nullable|string|max:255',
            'completion_date' => 'nullable|date',
            'technologies' => 'nullable|string', // Comma separated
            'url' => 'nullable|url',
            'image' => 'nullable|image|max:2048',
        ]);

        $validated['slug'] = Str::slug($request->title);
        
        // Convert technologies string to array
        if ($request->filled('technologies')) {
            $validated['technologies'] = array_map('trim', explode(',', $request->technologies));
        } else {
            $validated['technologies'] = [];
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('projects', 'public');
            $validated['image'] = $path;
        }

        Project::create($validated);

        return redirect()->route('admin.projects.index')->with('success', 'Projet créé avec succès.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $project = Project::findOrFail($id);
        return view('admin.projects.edit', compact('project'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $project = Project::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'client' => 'nullable|string|max:255',
            'completion_date' => 'nullable|date',
            'technologies' => 'nullable|string',
            'url' => 'nullable|url',
            'image' => 'nullable|image|max:2048',
        ]);

        $validated['slug'] = Str::slug($request->title);

        if ($request->filled('technologies')) {
            $validated['technologies'] = array_map('trim', explode(',', $request->technologies));
        } else {
            $validated['technologies'] = [];
        }

        if ($request->hasFile('image')) {
            if ($project->image) {
                Storage::disk('public')->delete($project->image);
            }
            $path = $request->file('image')->store('projects', 'public');
            $validated['image'] = $path;
        }

        $project->update($validated);

        return redirect()->route('admin.projects.index')->with('success', 'Projet mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $project = Project::findOrFail($id);

        if ($project->image) {
            Storage::disk('public')->delete($project->image);
        }

        $project->delete();

        return redirect()->route('admin.projects.index')->with('success', 'Projet supprimé avec succès.');
    }
}
