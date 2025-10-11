<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Configuration;
use Illuminate\Http\Request;
use App\Models\Avis;

class AboutController extends Controller
{
    public function index()
    {
        // Récupère la configuration du site
        $config = Configuration::first();

        // Récupère 3 catégories actives
        $categories = Category::where('is_active', true)
            ->latest()
            ->take(3)
            ->get();

        // Récupère 3 avis approuvés avec leurs produits associés
        $avis = Avis::with('product')
            ->where('approved', true)
            ->latest()
            ->take(3)
            ->get();

        return view('front-office.apropos.index', [
            'config' => $config,
            'categories' => $categories,
            'avis' => $avis
        ]);
    }

    public function faq()
    {

        return view('front-office.faq.index');
    }

    public function PolitiqueConfidentialite()
    {
        return view('front-office.politique-confidentialite.index');
    }

    public function MentionsLegales()
    {
        return view('front-office.mentions-legales.index');
    }
}


