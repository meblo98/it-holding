<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartnerCatalogController extends Controller
{
    /**
     * Catalogue de l'espace partenaire : les produits actifs avec, pour chacun,
     * les liens de partage portant le code de parrainage du partenaire.
     * Réservé aux partenaires approuvés de type revendeur ou commercial.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        abort_unless(
            $user && $user->isPartner() && in_array($user->partner_type, ['revendeur', 'commercial'], true),
            403,
            'Accès réservé aux revendeurs et aux partenaires commerciaux approuvés.'
        );

        $query = Product::where('active', true)->with('images');

        if ($search = trim((string) $request->query('search', ''))) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        if ($categoryId = $request->query('category_id')) {
            $category = Category::with('children')->find($categoryId);
            if ($category) {
                $ids = $category->children->pluck('id')->push($category->id)->all();
                $query->whereIn('category_id', $ids);
            }
        }

        $products = $query->orderBy('name')->paginate(12)->withQueryString();
        $categories = Category::whereNull('parent_id')->orderBy('name')->get();
        $refCode = $user->username ?: $user->partner_code;

        return view('pages.shop.partner.catalog', compact('user', 'products', 'categories', 'refCode'));
    }
}
