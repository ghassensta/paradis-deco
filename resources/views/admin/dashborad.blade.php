@extends('admin.layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />
    <style>
        .kpi-card .kpi-value { font-size: 1.5rem; font-weight: 700; line-height: 1.2; }
        .kpi-card .kpi-label { font-size: .8125rem; }
        .trend-up { color: #71dd37; }
        .trend-down { color: #ff3e1d; }
        .trend-flat { color: #a1acb8; }
        .dashboard-table { white-space: nowrap; }
        @media (max-width: 767.98px) {
            .kpi-card .kpi-value { font-size: 1.25rem; }
        }
    </style>
@endsection

@php
    /** Formatage monétaire unique pour tout le tableau de bord. */
    $money = fn ($v) => number_format((float) $v, 3, ',', ' ') . ' DT';

    $trendBadge = function (?float $t) {
        if ($t === null) {
            return '<span class="trend-flat small">Données insuffisantes</span>';
        }

        $class = $t > 0 ? 'trend-up' : ($t < 0 ? 'trend-down' : 'trend-flat');
        $icon = $t > 0 ? 'ti-trending-up' : ($t < 0 ? 'ti-trending-down' : 'ti-minus');

        return '<span class="' . $class . ' small"><i class="ti ' . $icon . '"></i> '
            . ($t > 0 ? '+' : '') . number_format($t, 1, ',', ' ') . ' % vs période précédente</span>';
    };
@endphp

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- En-tête + filtre de période --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h4 class="mb-1">Tableau de bord</h4>
            <p class="text-muted mb-0 small">
                {{ $periodLabel }} — du {{ $rangeStart->format('d/m/Y') }} au {{ $rangeEnd->format('d/m/Y') }}
            </p>
        </div>

        <form method="GET" action="{{ route('superadmin.dashborad') }}" class="d-flex align-items-center gap-2">
            <label for="period" class="form-label mb-0 small text-muted">Période</label>
            <select name="period" id="period" class="form-select form-select-sm" style="width: auto"
                    onchange="this.form.submit()">
                @foreach ($periods as $value => $label)
                    <option value="{{ $value }}" {{ (int) $value === $period ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- KPI --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card h-100 kpi-card">
                <div class="card-body">
                    <div class="kpi-label text-muted mb-1">Chiffre d'affaires</div>
                    <div class="kpi-value">{{ $money($current['revenue']) }}</div>
                    <div class="mt-1">{!! $trendBadge($trends['revenue']) !!}</div>
                    <div class="text-muted small mt-2">Aujourd'hui : {{ $money($today['revenue']) }}</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card h-100 kpi-card">
                <div class="card-body">
                    <div class="kpi-label text-muted mb-1">Commandes</div>
                    <div class="kpi-value">{{ number_format($current['orders'], 0, ',', ' ') }}</div>
                    <div class="mt-1">{!! $trendBadge($trends['orders']) !!}</div>
                    <div class="text-muted small mt-2">Aujourd'hui : {{ $today['orders'] }}</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card h-100 kpi-card">
                <div class="card-body">
                    <div class="kpi-label text-muted mb-1">Panier moyen</div>
                    <div class="kpi-value">{{ $money($current['aov']) }}</div>
                    <div class="mt-1">{!! $trendBadge($trends['aov']) !!}</div>
                    <div class="text-muted small mt-2">Commandes annulées exclues</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card h-100 kpi-card">
                <div class="card-body">
                    <div class="kpi-label text-muted mb-1">Unités vendues</div>
                    <div class="kpi-value">{{ number_format($current['units'], 0, ',', ' ') }}</div>
                    <div class="mt-1">{!! $trendBadge($trends['units']) !!}</div>
                    <div class="text-muted small mt-2">{{ $catalogue['clients'] }} client(s) enregistré(s)</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Catalogue & avis --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card h-100"><div class="card-body">
                <div class="kpi-label text-muted mb-1">Produits</div>
                <div class="kpi-value">{{ $catalogue['products']['total'] }}</div>
                <div class="text-muted small mt-2">{{ $catalogue['products']['active'] }} actif(s)</div>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100"><div class="card-body">
                <div class="kpi-label text-muted mb-1">Stock</div>
                <div class="kpi-value">{{ $catalogue['products']['out_of_stock'] }}</div>
                <div class="text-muted small mt-2">
                    en rupture · {{ $catalogue['products']['low_stock'] }} sous {{ $lowStockThreshold }} unités
                </div>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100"><div class="card-body">
                <div class="kpi-label text-muted mb-1">Avis</div>
                <div class="kpi-value">{{ $catalogue['reviews']['total'] ?? 0 }}</div>
                <div class="text-muted small mt-2">
                    {{ $catalogue['reviews']['approved'] ?? 0 }} publié(s) · {{ $catalogue['reviews']['pending'] ?? 0 }} en attente
                </div>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100"><div class="card-body">
                <div class="kpi-label text-muted mb-1">Questions FAQ</div>
                <div class="kpi-value">{{ $catalogue['faqs']['total'] ?? 0 }}</div>
                <div class="text-muted small mt-2">
                    {{ $catalogue['faqs']['products'] ?? 0 }} produit(s) · {{ $catalogue['faqs']['categories'] ?? 0 }} catégorie(s)
                </div>
            </div></div>
        </div>
    </div>

    {{-- Graphiques --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-xl-8">
            <div class="card h-100">
                <div class="card-header"><h5 class="mb-0">Évolution du chiffre d'affaires</h5></div>
                <div class="card-body">
                    @if ($series['hasData'])
                        <div id="revenueChart"></div>
                    @else
                        <p class="text-muted mb-0">Aucune donnée disponible pour cette période.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card h-100">
                <div class="card-header"><h5 class="mb-0">Évolution des commandes</h5></div>
                <div class="card-body">
                    @if ($series['hasData'])
                        <div id="ordersChart"></div>
                    @else
                        <p class="text-muted mb-0">Aucune donnée disponible pour cette période.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Top produits / catégories --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-xl-7">
            <div class="card h-100">
                <div class="card-header"><h5 class="mb-0">Produits les plus vendus</h5></div>
                <div class="table-responsive">
                    <table class="table dashboard-table mb-0">
                        <thead><tr><th>Produit</th><th class="text-end">Unités</th><th class="text-end">CA</th><th class="text-end">Stock</th></tr></thead>
                        <tbody>
                            @forelse ($topProducts as $row)
                                <tr>
                                    <td><a href="{{ route('produits.edit', $row['product']->id) }}">{{ $row['product']->name }}</a></td>
                                    <td class="text-end">{{ $row['units'] }}</td>
                                    <td class="text-end">{{ $money($row['revenue']) }}</td>
                                    <td class="text-end">
                                        <span class="badge {{ $row['product']->stock == 0 ? 'bg-label-danger' : ($row['product']->stock <= $lowStockThreshold ? 'bg-label-warning' : 'bg-label-success') }}">
                                            {{ $row['product']->stock }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-muted">Aucune vente sur cette période.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-5">
            <div class="card h-100">
                <div class="card-header"><h5 class="mb-0">Catégories les plus vendues</h5></div>
                <div class="table-responsive">
                    <table class="table dashboard-table mb-0">
                        <thead><tr><th>Catégorie</th><th class="text-end">Unités</th><th class="text-end">CA</th></tr></thead>
                        <tbody>
                            @forelse ($topCategories as $row)
                                <tr>
                                    <td>{{ $row['category']->name }}</td>
                                    <td class="text-end">{{ $row['units'] }}</td>
                                    <td class="text-end">{{ $money($row['revenue']) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-muted">Aucune vente sur cette période.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Alertes + score SEO --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-xl-7">
            <div class="card h-100">
                <div class="card-header"><h5 class="mb-0">Alertes</h5></div>
                <div class="card-body">
                    @forelse ($alerts as $alert)
                        <div class="d-flex align-items-center justify-content-between border-bottom py-2">
                            <div>
                                <span class="badge bg-label-{{ $alert['level'] }} me-2">{{ $alert['count'] }}</span>
                                {{ $alert['label'] }}
                            </div>
                            <a href="{{ $alert['route'] }}" class="btn btn-sm btn-label-secondary">Voir</a>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Aucune alerte : le catalogue est complet et les stocks sont suffisants.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-5">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Score de complétude SEO</h5>
                    <small class="text-muted">Champs éditoriaux renseignés — ce n'est pas une position Google.</small>
                </div>
                <div class="card-body">
                    @if ($seoScore['global'] === null)
                        <p class="text-muted mb-0">Aucun contenu à évaluer.</p>
                    @else
                        <div class="d-flex align-items-baseline gap-2 mb-3">
                            <span class="kpi-value">{{ $seoScore['global'] }} %</span>
                            <span class="text-muted small">de complétude</span>
                        </div>
                        @foreach ($seoScore['checks'] as $label => $value)
                            <div class="mb-2">
                                <div class="d-flex justify-content-between small">
                                    <span>{{ $label }}</span><span>{{ $value }} %</span>
                                </div>
                                <div class="progress" style="height: 6px">
                                    <div class="progress-bar {{ $value >= 80 ? 'bg-success' : ($value >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                         style="width: {{ $value }}%"
                                         role="progressbar"
                                         aria-valuenow="{{ $value }}" aria-valuemin="0" aria-valuemax="100"
                                         aria-label="{{ $label }}"></div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Produits à surveiller --}}
    <div class="row g-3 mb-4">
        @foreach ([
            ['title' => 'En rupture', 'rows' => $watchList['outOfStock'], 'empty' => 'Aucun produit en rupture.'],
            ['title' => 'Stock faible', 'rows' => $watchList['lowStock'], 'empty' => 'Aucun stock faible.'],
            ['title' => 'Jamais vendus', 'rows' => $watchList['neverSold'], 'empty' => 'Tous les produits ont été vendus au moins une fois.'],
        ] as $panel)
            <div class="col-12 col-lg-4">
                <div class="card h-100">
                    <div class="card-header"><h6 class="mb-0">{{ $panel['title'] }}</h6></div>
                    <div class="card-body">
                        @forelse ($panel['rows'] as $product)
                            <div class="d-flex justify-content-between align-items-center py-1">
                                <a href="{{ route('produits.edit', $product->id) }}" class="text-truncate me-2">{{ $product->name }}</a>
                                <span class="badge bg-label-secondary">{{ $product->stock }}</span>
                            </div>
                        @empty
                            <p class="text-muted small mb-0">{{ $panel['empty'] }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Commandes récentes --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Commandes récentes</h5>
            <a href="{{ route('commandes.index') }}" class="btn btn-sm btn-label-secondary">Toutes les commandes</a>
        </div>
        <div class="table-responsive">
            <table class="table dashboard-table mb-0">
                <thead><tr><th>Numéro</th><th>Client</th><th>Date</th><th class="text-end">Montant</th><th>Statut</th></tr></thead>
                <tbody>
                    @forelse ($recentOrders as $order)
                        <tr>
                            <td>{{ $order->numero_commande }}</td>
                            {{-- Ni email ni téléphone : le dashboard n'a pas besoin de données personnelles. --}}
                            <td>{{ $order->client?->name ?? '—' }}</td>
                            <td>{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                            <td class="text-end">{{ $money($order->total_ttc) }}</td>
                            <td><span class="badge bg-label-secondary">{{ $order->statut?->name ?? '—' }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-muted">Aucune commande enregistrée.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('js')
@if ($series['hasData'])
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script>
        (function () {
            if (typeof ApexCharts === 'undefined') {
                return;
            }

            var labels = @json($series['labels']);

            new ApexCharts(document.querySelector('#revenueChart'), {
                chart: { type: 'area', height: 300, toolbar: { show: false } },
                series: [{ name: 'Chiffre d\'affaires (DT)', data: @json($series['revenue']) }],
                xaxis: { categories: labels },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 2 },
                colors: ['#696cff'],
                tooltip: { y: { formatter: function (v) { return v.toFixed(3) + ' DT'; } } }
            }).render();

            new ApexCharts(document.querySelector('#ordersChart'), {
                chart: { type: 'bar', height: 300, toolbar: { show: false } },
                series: [{ name: 'Commandes', data: @json($series['orders']) }],
                xaxis: { categories: labels },
                dataLabels: { enabled: false },
                colors: ['#03c3ec'],
                plotOptions: { bar: { borderRadius: 4 } }
            }).render();
        })();
    </script>
@endif
@endsection
