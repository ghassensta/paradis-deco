<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <!-- Brand Section -->

    <div class="app-brand demo"
        style="background-color: #1C2526; padding: 10px 20px; display: flex; align-items: center; justify-content: space-between;">
        <a href="/" class="app-brand-link" style="text-decoration: none;">
            <span class="app-brand-logo demo">
                <h1
                    style="color: #FFD700; font-size: 24px; font-weight: bold; margin: 0; font-family: Arial, sans-serif;">
                    Paradis-<span style="color: #D4A017; font-style: italic;">Deco</span>
                </h1>
            </span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"
                style="color: #FFD700; font-size: 20px;"></i>
            <i class="ti ti-x d-block d-xl-none ti-sm align-middle" style="color: #FFD700; font-size: 20px;"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>
    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        <li class="menu-item {{ request()->routeIs('superadmin.dashborad') ? 'active' : '' }}">
            <a href="{{ route('superadmin.dashborad') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-layout-dashboard"></i>
                <div>Tableau de bord</div>
            </a>
        </li>

        <!-- Boutiques -->
        <li class="menu-item {{ request()->routeIs('commandes.*') ? 'active' : '' }}">
            <a href="{{ route('commandes.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-truck-delivery"></i>
                <div>Listes des commandes</div>
            </a>
        </li>

        <!-- Packs -->
        <li class="menu-item {{ request()->routeIs('categories.*') ? 'active' : '' }}">
            <a href="{{ route('categories.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-category"></i>
                <div>Liste des Categories</div>
            </a>
        </li>
        <!-- Packs -->
        <li class="menu-item {{ request()->routeIs('produits.*') ? 'active' : '' }}">
            <a href="{{ route('produits.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-package"></i>
                <div>Liste des Produits</div>
            </a>
        </li>

        <!-- config -->
        <li class="menu-item {{ request()->routeIs('configurations.*') ? 'active' : '' }}">
            <a href="{{ route('configurations.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-spiral"></i>
                <div>Configuration</div>
            </a>
        </li>
        <li class="menu-item {{ request()->routeIs('inspirations.*') ? 'active' : '' }}">
            <a href="{{ route('inspirations.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-settings-up"></i>
                <div>Inspirations</div>
            </a>
        </li>
        <!-- reviews -->
        <li class="menu-item {{ request()->routeIs('avis.*') ? 'active' : '' }}">
            <a href="{{ route('avis.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-stars"></i>
                <div>Liste des avis</div>
            </a>
        </li>

       {{--  <!-- Mon compte -->
        <li class="menu-item">
            <a href="" class="menu-link">
                <i class="menu-icon tf-icons ti ti-user-circle"></i>
                <div>Mon compte</div>
            </a>
        </li> --}}
    </ul>
</aside>
