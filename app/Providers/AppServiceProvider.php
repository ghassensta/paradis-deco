<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Category;
use App\Models\Configuration;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Le composer s'applique à toutes les vues, partials compris : sans
        // mémorisation, la configuration serait relue à chaque rendu.
        //
        // Le binding est « scoped » et non capturé dans la closure : une
        // variable retenue par référence resterait figée pour tout le
        // processus (Octane, suite de tests) et l'administration ne verrait
        // plus ses modifications se répercuter sur le site.
        $this->app->scoped(
            'site.configuration',
            // Les layouts lisent $config->… sans null-safe : on fournit toujours
            // un modèle, vide tant qu'aucune configuration n'a été enregistrée.
            fn () => Configuration::first() ?? new Configuration()
        );

        view()->composer('*', function ($view) {
            $view->with(['config' => app('site.configuration')]);
        });

        // Arborescence de navigation : deux requêtes au plus, mémorisées pour
        // la durée de la requête HTTP, et uniquement pour les vues qui s'en
        // servent — l'administration n'en paie pas le coût.
        $this->app->scoped('site.navigation', function () {
            $racines = Category::active()
                ->roots()
                ->ordered()
                ->with('activeChildren')
                ->get();

            // L'URL d'une sous-catégorie contient le slug de son parent :
            // sans cette association, chaque enfant rechargeait son parent
            // (dix requêtes supplémentaires sur la page d'accueil).
            $racines->each(
                fn (Category $racine) => $racine->activeChildren->each(
                    fn (Category $enfant) => $enfant->setRelation('parent', $racine)
                )
            );

            return $racines;
        });

        view()->composer(
            ['front-office.layouts.header', 'front-office.layouts.footer'],
            fn ($view) => $view->with('navCategories', app('site.navigation'))
        );
    }
}
