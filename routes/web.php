<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\AccueilController;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AvisController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboradController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InspirationsController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Artisan;


Route::get('/', [AccueilController::class, 'nouveautes'])->name('welcome')->middleware('throttle:60,1');

ute::get('/artisan/clear', function () {

    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');

    // storage:link (évite erreur si déjà existant)
    try {
        Artisan::call('storage:link');
    } catch (\Exception $e) {
        // ignore si le lien existe déjà
    }

    return response()->json([
        'status' => 'success',
        'message' => 'Cache, routes, config, views nettoyés + storage link OK'
    ]);
});
Route::get('/article/{slug}', action: [AccueilController::class, 'ProduitShow'])->name('preview-article');
Route::get('/inspiration/{slug}', action: [AccueilController::class, 'InspirationShow'])->name('preview-inspiration');
Route::resource('/checkout', CheckoutController::class);
Route::post('/order/submit', [CheckoutController::class, 'storeOrder'])
    ->name('order.submit');
Route::get('/toutes/produits', [AccueilController::class, 'AllProduits'])->name('allproduits');

Route::get('/toutes/inspirations', [AccueilController::class, 'AllInspirations'])->name('allinspirations');
Route::get('/cat', function () {
    return view('products.cat');
});
Route::get('/show', function () {
    return view('products.show');
});

Route::get('/inspiration', function () {
    return view('products.inspiration');
});

Route::get('/a-propos', [AboutController::class, 'index'])
    ->name('about');

// Page Contact
Route::get('/contact', [ContactController::class, 'index'])
    ->name('contact');

// Soumission du formulaire de contact
Route::post('/contact', [ContactController::class, 'submit'])
    ->name('contact.submit');

Route::get('/collections/{slug}', [AccueilController::class, 'CategorieProduits'])->name('categorie.produits');
Route::post('avis/produit/store/', [AccueilController::class, 'storeReview'])->name('avis.storeReview');

Route::get('/faq', [AboutController::class, 'faq'])
    ->name('faq');

Route::get('/politique-confidentialite', [AboutController::class, 'PolitiqueConfidentialite'])
    ->name('politique-confidentialite');

Route::get('/mentions-legales', [AboutController::class, 'MentionsLegales'])
    ->name('mentions-legales');
// --- Toutes les routes admin sous /admin/paradis-deco ---
Route::prefix('admin/paradis-deco')->group(function () {
    //
    // 1) Routes accessibles aux **invités** seulement
    //
    Route::middleware('guest')->group(function () {
        // Login
        Route::get('login', [LoginController::class, 'showLoginForm'])
            ->name('login');
        Route::post('login', [LoginController::class, 'login']);

        // Mot de passe oublié
        Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])
            ->name('password.request');
        Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
            ->name('password.email');

        // Réinitialisation
        Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])
            ->name('password.reset');
        Route::post('password/reset', [ResetPasswordController::class, 'reset'])
            ->name('password.update');
    });

    //
    // 2) Routes **protégées** (auth + superadmin)
    //
    Route::middleware(['auth', 'role:superadmin'])->group(function () {
        // Logout
        Route::post('logout', [LoginController::class, 'logout'])
            ->name('logout');

        // Dashboard & Home
        Route::get('home', [HomeController::class, 'index'])
            ->name('home');
        Route::get('dashborad', [DashboradController::class, 'index'])
            ->name('superadmin.dashborad');

        Route::put('inspirations/toggle/{id}', [InspirationsController::class, 'toggleActive'])->name('inspirations.toggle');
        // CRUD commandes
        Route::resource('commandes', OrderController::class);
        Route::resource('produits', ProductController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('configurations', ConfigurationController::class);
        Route::resource('avis', AvisController::class);
        Route::resource('inspirations', InspirationsController::class);
        Route::resource('avis', AvisController::class);

        //get-commandes-ajax
        Route::get('ajax/get-commandes', [AjaxController::class, 'getCommandes'])->name('commandes.get');
        Route::get('ajax/get-products', [AjaxController::class, 'getProducts'])->name('products.get');
        Route::get('ajax/get-category', [AjaxController::class, 'getCategory'])->name('category.get');
        Route::get('ajax/get-inspiration', [AjaxController::class, 'getInspiration'])->name('inspirations.get');
        Route::get('ajax/get-avis', [AjaxController::class, 'getAvis'])->name('avis.get');

        Route::get('/commandes/{id}/edit-status', [OrderController::class, 'editStatus'])->name('commandes.edit-status');
        Route::put('/commandes/{id}/status', [OrderController::class, 'updateStatus'])->name('commandes.update-status');
        Route::get('/commandes/{id}/pdf', [PdfController::class, 'generatePDF'])
            ->where('id', '[0-9]+')
            ->name('commandes.pdf');

    });
});
