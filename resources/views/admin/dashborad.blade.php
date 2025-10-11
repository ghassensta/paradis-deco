{{-- resources/views/admin/dashboard.blade.php --}}
@extends('admin.layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header avec fil d'ariane -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold py-3 mb-0">
                <span class="text-muted fw-light">Admin /</span> Tableau de bord
            </h4>
            <div class="btn-group">
                <button class="btn btn-outline-secondary" disabled>
                    <i class="ti ti-calendar me-1"></i> {{ now()->format('d M Y') }}
                </button>
            </div>
        </div>

        {{-- === Cartes statistiques === --}}
        <div class="row mb-4 g-4">
            <!-- Produits totaux -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card card-statistic h-100 border-start border-start-4 border-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-muted mb-2">PRODUITS TOTAUX</h6>
                                <h3 class="mb-0 text-primary">{{ $totalProducts }}</h3>
                            </div>
                            <div class="avatar avatar-sm bg-opacity-10 bg-primary p-1 rounded-3">
                                <i class="ti ti-package text-white"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="badge bg-primary bg-opacity-10">
                                <i class="ti ti-arrow-up me-1"></i>
                                {{ round(($activeProducts / $totalProducts) * 100) }}% actifs
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Produits actifs -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card card-statistic h-100 border-start border-start-4 border-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-muted mb-2">PRODUITS ACTIFS</h6>
                                <h3 class="mb-0 text-success">{{ $activeProducts }}</h3>
                            </div>
                            <div class="avatar avatar-sm bg-success bg-opacity-10 p-1 rounded-3">
                                <i class="ti ti-check text-success"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="badge bg-success bg-opacity-10 text-success">
                                <i class="ti ti-arrow-up me-1"></i>
                                {{ $productsThisMonth }} ce mois
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Commandes payées -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card card-statistic h-100 border-start border-start-4 border-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-muted mb-2">COMMANDES PAYÉES</h6>
                                <h3 class="mb-0 text-warning">{{ $nbPaidOrders }}</h3>
                            </div>
                            <div class="avatar avatar-sm bg-warning bg-opacity-10 p-1 rounded-3">
                                <i class="ti ti-shopping-cart text-warning"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="badge bg-warning bg-opacity-10 text-warning">
                                <i class="ti ti-currency-dollar me-1"></i>
                                {{ number_format($paidRevenue, 0, ',', ' ') }} TND
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenu encaissé -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card card-statistic h-100 border-start border-start-4 border-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-muted mb-2">REVENU ENCAISSÉ</h6>
                                <h3 class="mb-0 text-success">{{ number_format($paidRevenue, 0, ',', ' ') }} TND</h3>
                            </div>
                            <div class="avatar avatar-sm bg-success bg-opacity-10 p-1 rounded-3">
                                <i class=" text-success">DT</i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="badge bg-success bg-opacity-10 text-success">
                                <i class="ti ti-chart-line me-1"></i>
                                CA global
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- === Top produits vendus === --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0">
                    <i class="ti ti-star me-2"></i>TOP 5 PRODUITS VENDUS
                </h5>
            </div>
            <div class="table-responsive">
                <table class="table table-borderless table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>RANG</th>
                            <th>PRODUIT</th>
                            <th class="text-end">QUANTITÉ</th>
                            <th class="text-end">REVENU</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php use Illuminate\Support\Str; @endphp

                        @forelse ($topProducts as $index => $p)
                            <tr>
                                {{-- Rang --}}
                                <td width="50">
                                    <span class="badge bg-primary bg-opacity-10  rounded-pill p-2">
                                        #{{ $index + 1 }}
                                    </span>
                                </td>

                                {{-- Produit --}}
                                <td>
                                    @php
                                        // images est déjà casté en array par Eloquent
                                        $firstImage =
                                            !empty($p->images) && is_array($p->images)
                                                ? asset('storage/' . $p->images[0])
                                                : asset('assets/img/placeholder-100x100.png');
                                    @endphp

                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3">
                                            <img src="{{ $firstImage }}" alt="{{ $p->name }}" class="rounded-2"
                                                width="40" height="40">
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ Str::limit($p->name, 30) }}</h6>
                                            <small class="text-muted">Ref :
                                                PROD{{ str_pad($p->id, 4, '0', STR_PAD_LEFT) }}</small>
                                        </div>
                                    </div>
                                </td>

                                {{-- Quantité vendue --}}
                                <td class="text-end align-middle">
                                    <span class="fw-medium">{{ $p->sold_qty }}</span>
                                </td>

                                {{-- Chiffre d’affaires du produit --}}
                                <td class="text-end align-middle">
                                    <span class="fw-bold text-success">
                                        {{ number_format($p->sold_qty * $p->price, 2, ',', ' ') }} TND
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="ti ti-mood-sad text-muted mb-2" style="font-size:2rem;"></i>
                                        <span class="text-muted">Aucune vente enregistrée</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
        </div>


    </div>
@endsection

@section('styles')
    <style>
        .card-statistic {
            transition: all 0.3s ease;
            border-radius: 0.5rem;
        }

        .card-statistic:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.08);
        }

        .avatar-sm {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.375rem;
        }

        .table-hover tbody tr {
            transition: all 0.2s ease;
            border-radius: 0.5rem;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(var(--bs-primary-rgb), 0.05);
        }

        .table thead th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            color: #6c757d;
        }
    </style>
@endsection
