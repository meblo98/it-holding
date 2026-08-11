<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketingAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MarketingAssetController extends Controller
{
    public function index()
    {
        $assets = MarketingAsset::latest()->paginate(15);
        return view('admin.marketing-assets.index', compact('assets'));
    }

    public function create()
    {
        return view('admin.marketing-assets.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|in:image,pdf,document,template,other',
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('marketing_assets', 'public');
            $validated['file_path'] = $path;
        }

        MarketingAsset::create($validated);

        return redirect()->route('admin.marketing-assets.index')->with('success', 'Ressource marketing ajoutée avec succès.');
    }

    public function edit(MarketingAsset $marketingAsset)
    {
        return view('admin.marketing-assets.edit', compact('marketingAsset'));
    }

    public function update(Request $request, MarketingAsset $marketingAsset)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|in:image,pdf,document,template,other',
            'file' => 'nullable|file|max:10240', // 10MB max
        ]);

        if ($request->hasFile('file')) {
            // Delete old file
            if ($marketingAsset->file_path) {
                Storage::disk('public')->delete($marketingAsset->file_path);
            }
            $path = $request->file('file')->store('marketing_assets', 'public');
            $validated['file_path'] = $path;
        }

        $marketingAsset->update($validated);

        return redirect()->route('admin.marketing-assets.index')->with('success', 'Ressource marketing mise à jour avec succès.');
    }

    public function destroy(MarketingAsset $marketingAsset)
    {
        if ($marketingAsset->file_path) {
            Storage::disk('public')->delete($marketingAsset->file_path);
        }

        $marketingAsset->delete();

        return redirect()->route('admin.marketing-assets.index')->with('success', 'Ressource marketing supprimée avec succès.');
    }
}
