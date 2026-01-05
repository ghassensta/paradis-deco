@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">
            <i class="ti ti-shopping-cart me-2"></i>Commandes
        </h4>
    </div>

    <!-- Filtre statut - Mobile friendly -->
    <div class="card mb-3">
        <div class="card-body py-3">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-4">
                    <select name="statut" id="statut-filter" class="form-select">
                        <option value="all" selected>📊 Tous les statuts</option>
                        @forelse ($statuts as $item)
                            <option value="{{ $item->id }}">{{ $item->name ?? 'Statut Inconnu' }}</option>
                        @empty
                            <option disabled>Aucun statut</option>
                        @endforelse
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <input type="text" id="search-input" class="form-control" placeholder="🔍 Rechercher...">
                </div>
            </div>
        </div>
    </div>

    <!-- Vue Desktop : DataTable -->
    <div class="d-none d-lg-block">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="datatables-commandes table table-hover">
                        <thead>
                            <tr>
                                <th>N° Commande</th>
                                <th>Date</th>
                                <th>Client</th>
                                <th class="text-center">Articles</th>
                                <th>Total TTC</th>
                                <th>Statut</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Vue Mobile : Cards -->
    <div class="d-lg-none" id="mobile-orders-container">
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Chargement...</span>
            </div>
        </div>
    </div>

    <!-- Pagination Mobile -->
    <div class="d-lg-none mt-3" id="mobile-pagination">
        <nav>
            <ul class="pagination pagination-sm justify-content-center"></ul>
        </nav>
    </div>
</div>

<!-- Modal Détails (Optimisé Mobile) -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-fullscreen-sm-down modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white">
                <div>
                    <h6 class="modal-title mb-0">
                        <i class="ti ti-receipt me-2"></i>Commande <span id="modal-order-number"></span>
                    </h6>
                    <small class="opacity-75" id="modal-order-date"></small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" id="orderDetailsContent"></div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Statut -->
<div class="modal fade" id="updateStatusModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-edit me-2"></i>Modifier le statut
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="updateStatusContent"></div>
        </div>
    </div>
</div>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
<style>
    /* Mobile Cards */
    .order-card {
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        margin-bottom: 1rem;
        border: 1px solid #e9ecef;
        overflow: hidden;
    }
    .order-card:active {
        transform: scale(0.98);
    }
    .order-card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1rem;
    }
    .order-card-body {
        padding: 1rem;
    }

    /* Info rows */
    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .info-row:last-child {
        border-bottom: none;
    }
    .info-label {
        color: #6c757d;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .info-value {
        font-weight: 600;
        text-align: right;
    }

    /* Product items dans modal */
    .product-item-mobile {
        border: 1px solid #e9ecef;
        border-radius: 10px;
        padding: 0.75rem;
        margin-bottom: 0.75rem;
        background: #fafafa;
    }
    .product-img-mobile {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
    }
    .product-placeholder-mobile {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Action buttons mobile */
    .action-btn-mobile {
        border-radius: 8px;
        padding: 0.5rem;
        font-size: 0.875rem;
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    /* Badges */
    .badge-mobile {
        padding: 0.5em 0.75em;
        border-radius: 20px;
        font-size: 0.813rem;
        font-weight: 500;
    }

    /* Summary section */
    .summary-section {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1rem;
    }
    .summary-line {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        border-bottom: 1px dashed #dee2e6;
    }
    .summary-line:last-child {
        border-bottom: none;
        border-top: 2px solid #007bff;
        padding-top: 1rem;
        margin-top: 0.5rem;
    }

    /* Desktop specific */
    @media (min-width: 992px) {
        .product-img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 8px;
        }
        .badge-status {
            padding: 0.5em 1em;
            font-size: 0.875rem;
            border-radius: 20px;
            font-weight: 500;
        }
    }

    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .order-card {
        animation: fadeInUp 0.3s ease;
    }
</style>
@endsection

@section('js')
<script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
'use strict';

$(function() {
    let dtCommande;
    let mobileOrders = [];
    let currentPage = 1;
    let itemsPerPage = 10;

    // Détection mobile
    const isMobile = window.innerWidth < 992;

    // ============================================
    // DESKTOP: DataTable
    // ============================================
    if (!isMobile && $('.datatables-commandes').length) {
        dtCommande = $('.datatables-commandes').DataTable({
            processing: true,
            serverSide: true,
            responsive: false,
            ajax: {
                url: "{{ route('commandes.get') }}",
                type: "GET",
                data: function(d) {
                    d._token = '{{ csrf_token() }}';
                    d.statut_id = $('#statut-filter').val();
                }
            },
            columns: [
                { data: 'numero_commande' },
                { data: 'date' },
                { data: 'client_name' },
                { data: 'items_count' },
                { data: 'total_ttc' },
                { data: 'statut_name' },
                { data: null }
            ],
            columnDefs: [
                {
                    targets: 0,
                    render: function(data, type, full) {
                        return `<span class="fw-bold text-primary">${data}</span>`;
                    }
                },
                {
                    targets: 2,
                    render: function(data, type, full) {
                        return `
                            <div class="d-flex flex-column">
                                <span class="fw-semibold">${data}</span>
                                <small class="text-muted">
                                    <i class="ti ti-phone ti-xs me-1"></i>${full.client_phone}
                                </small>
                            </div>
                        `;
                    }
                },
                {
                    targets: 3,
                    className: 'text-center',
                    render: function(data, type, full) {
                        return `<span class="badge bg-label-info rounded-pill">${data}</span>`;
                    }
                },
                {
                    targets: 4,
                    render: function(data, type, full) {
                        return `<span class="fw-bold text-success">${parseFloat(data).toLocaleString('fr-TN', {minimumFractionDigits: 3})} DT</span>`;
                    }
                },
                {
                    targets: 5,
                    render: function(data, type, full) {
                        return getStatusBadge(data);
                    }
                },
                {
                    targets: -1,
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: function(data, type, full) {
                        return getActionButtons(full);
                    }
                }
            ],
            order: [[1, 'desc']],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json',
                searchPlaceholder: "Rechercher...",
                search: ""
            }
        });
    }

    // ============================================
    // MOBILE: Cards View
    // ============================================
    if (isMobile) {
        loadMobileOrders();

        $('#search-input').on('keyup', debounce(function() {
            loadMobileOrders();
        }, 500));
    }

    function loadMobileOrders() {
        $.ajax({
            url: "{{ route('commandes.get') }}",
            type: "GET",
            data: {
                _token: '{{ csrf_token() }}',
                statut_id: $('#statut-filter').val(),
                start: 0,
                length: 100, // Charger plus pour le client-side filtering
                search: { value: $('#search-input').val() }
            },
            success: function(response) {
                mobileOrders = response.data;
                renderMobileOrders();
            }
        });
    }

    function renderMobileOrders() {
        const container = $('#mobile-orders-container');

        if (mobileOrders.length === 0) {
            container.html(`
                <div class="text-center py-5">
                    <i class="ti ti-inbox ti-lg text-muted mb-3"></i>
                    <p class="text-muted">Aucune commande trouvée</p>
                </div>
            `);
            return;
        }

        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const paginatedOrders = mobileOrders.slice(start, end);

        let html = '';
        paginatedOrders.forEach(order => {
            const statusBadge = getStatusBadgeClass(order.statut_name);

            html += `
                <div class="order-card" data-order-id="${order.id}">
                    <div class="order-card-header">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">${order.numero_commande}</h6>
                                <small class="opacity-75">
                                    <i class="ti ti-calendar ti-xs me-1"></i>${order.date}
                                </small>
                            </div>
                            <span class="badge ${statusBadge} badge-mobile">${order.statut_name}</span>
                        </div>
                    </div>
                    <div class="order-card-body">
                        <div class="info-row">
                            <span class="info-label">
                                <i class="ti ti-user"></i> Client
                            </span>
                            <span class="info-value">${order.client_name}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">
                                <i class="ti ti-phone"></i> Téléphone
                            </span>
                            <span class="info-value">${order.client_phone}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">
                                <i class="ti ti-package"></i> Articles
                            </span>
                            <span class="info-value">
                                <span class="badge bg-label-info rounded-pill">${order.items_count}</span>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">
                                <i class="ti ti-currency-dollar"></i> Total
                            </span>
                            <span class="info-value text-success fw-bold">
                                ${parseFloat(order.total_ttc).toLocaleString('fr-TN', {minimumFractionDigits: 3})} DT
                            </span>
                        </div>

                        <!-- Actions -->
                        <div class="d-flex gap-2 mt-3">
                            <button class="btn btn-primary action-btn-mobile view-order-mobile" data-order='${JSON.stringify(order).replace(/'/g, "&#39;")}'>
                                <i class="ti ti-eye"></i> Voir
                            </button>
                            <button class="btn btn-info action-btn-mobile edit-status" data-id="${order.id}">
                                <i class="ti ti-edit"></i>
                            </button>
                            <a href="{{ route('commandes.pdf', ':id') }}".replace(':id',${order.id})
                               target="_blank"
                               class="btn btn-success action-btn-mobile">
                                <i class="ti ti-file-download"></i>
                            </a>
                            <button class="btn btn-danger action-btn-mobile delete-order" data-id="${order.id}">
                                <i class="ti ti-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });

        container.html(html);
        renderMobilePagination();
    }

    function renderMobilePagination() {
        const totalPages = Math.ceil(mobileOrders.length / itemsPerPage);
        const pagination = $('#mobile-pagination ul');

        if (totalPages <= 1) {
            pagination.html('');
            return;
        }

        let html = '';

        // Previous
        html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${currentPage - 1}">‹</a>
        </li>`;

        // Pages
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                html += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>`;
            } else if (i === currentPage - 2 || i === currentPage + 2) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }

        // Next
        html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${currentPage + 1}">›</a>
        </li>`;

        pagination.html(html);
    }

    // Pagination click
    $(document).on('click', '#mobile-pagination .page-link', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        if (page && page > 0 && page <= Math.ceil(mobileOrders.length / itemsPerPage)) {
            currentPage = page;
            renderMobileOrders();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });

    // ============================================
    // MODAL DÉTAILS (Mobile & Desktop)
    // ============================================
    $(document).on('click', '.view-order, .view-order-mobile', function() {
        const order = $(this).data('order');
        showOrderDetails(order);
    });

    function showOrderDetails(order) {
        $('#modal-order-number').text(order.numero_commande);
        $('#modal-order-date').text(order.date_full || order.date);

        let itemsHtml = '';
        if (order.items && order.items.length > 0) {
            itemsHtml = order.items.map(item => {
                const imgHtml = item.image
                    ? `<img src="/storage/${item.image}" class="product-img-mobile" alt="${item.name}">`
                    : `<div class="product-placeholder-mobile">
                           <i class="ti ti-package text-white"></i>
                       </div>`;

                return `
                    <div class="product-item-mobile">
                        <div class="d-flex gap-3">
                            ${imgHtml}
                            <div class="flex-grow-1">
                                <h6 class="mb-2">${item.name}</h6>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Qté: ${item.quantity} × ${parseFloat(item.unit_price).toLocaleString('fr-TN', {minimumFractionDigits: 3})} DT</small>
                                    <strong class="text-success">${parseFloat(item.subtotal).toLocaleString('fr-TN', {minimumFractionDigits: 3})} DT</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        } else {
            itemsHtml = '<div class="alert alert-info m-3">Aucun article</div>';
        }

        const content = `
            <div class="p-3">
                <!-- Client -->
                <div class="mb-3">
                    <h6 class="fw-bold mb-3"><i class="ti ti-user me-2"></i>Client</h6>
                    <div class="info-row">
                        <span class="info-label">Nom</span>
                        <span class="info-value">${order.client_name}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Téléphone</span>
                        <span class="info-value">${order.client_phone}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Adresse</span>
                        <span class="info-value">${order.client_adresse}</span>
                    </div>
                </div>

                <!-- Produits -->
                <div class="mb-3">
                    <h6 class="fw-bold mb-3"><i class="ti ti-package me-2"></i>Produits (${order.items_count})</h6>
                    ${itemsHtml}
                </div>

                <!-- Récapitulatif -->
                <div class="summary-section">
                    <h6 class="fw-bold mb-3"><i class="ti ti-calculator me-2"></i>Récapitulatif</h6>
                    <div class="summary-line">
                        <span>Sous-total HT</span>
                        <strong>${parseFloat(order.subtotal_ht).toLocaleString('fr-TN', {minimumFractionDigits: 3})} DT</strong>
                    </div>
                    <div class="summary-line">
                        <span>Frais de livraison</span>
                        <strong>${parseFloat(order.shipping_cost).toLocaleString('fr-TN', {minimumFractionDigits: 3})} DT</strong>
                    </div>
                    <div class="summary-line">
                        <span class="fw-bold">Total TTC</span>
                        <strong class="text-success fs-5">${parseFloat(order.total_ttc).toLocaleString('fr-TN', {minimumFractionDigits: 3})} DT</strong>
                    </div>
                </div>
            </div>
        `;

        $('#orderDetailsContent').html(content);
        $('#orderDetailsModal').modal('show');
    }

    // ============================================
    // HELPER FUNCTIONS
    // ============================================
    function getStatusBadge(data) {
        let badgeClass = getStatusBadgeClass(data);
        return `<span class="badge ${badgeClass} badge-status">${data}</span>`;
    }

    function getStatusBadgeClass(data) {
        let statutLower = data.toLowerCase();
        if (statutLower.includes('livr')) return 'bg-label-success';
        if (statutLower.includes('annul')) return 'bg-label-danger';
        if (statutLower.includes('traitement')) return 'bg-label-warning';
        if (statutLower.includes('livraison')) return 'bg-label-info';
        return 'bg-label-secondary';
    }

    function getActionButtons(full) {
        return `
            <div class="d-flex justify-content-center gap-1">
                <button class="btn btn-sm btn-icon btn-primary view-order"
                        data-order='${JSON.stringify(full).replace(/'/g, "&#39;")}'
                        title="Voir">
                    <i class="ti ti-eye"></i>
                </button>
                <button class="btn btn-sm btn-icon btn-info edit-status"
                        data-id="${full.id}"
                        title="Modifier">
                    <i class="ti ti-edit"></i>
                </button>
                <a href="{{ route('commandes.pdf', ':id') }}".replace(':id', full.id)
                   target="_blank"
                   class="btn btn-sm btn-icon btn-success"
                   title="PDF">
                    <i class="ti ti-file-download"></i>
                </a>
                <button class="btn btn-sm btn-icon btn-danger delete-order"
                        data-id="${full.id}"
                        title="Supprimer">
                    <i class="ti ti-trash"></i>
                </button>
            </div>
        `;
    }

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // ============================================
    // ÉVÉNEMENTS COMMUNS
    // ============================================

    // Filtre statut
    $('#statut-filter').on('change', function() {
        if (isMobile) {
            currentPage = 1;
            loadMobileOrders();
        } else {
            dtCommande.ajax.reload();
        }
    });

    // Modifier statut
    $(document).on('click', '.edit-status', function() {
        const id = $(this).data('id');
        const editUrl = "{{ route('commandes.edit-status', ':id') }}".replace(':id', id);

        $('#updateStatusContent').html('<div class="text-center py-3"><div class="spinner-border spinner-border-sm"></div></div>');
        $('#updateStatusModal').modal('show');

        $('#updateStatusContent').load(editUrl);
    });

    // Soumission statut
    $(document).on('submit', '#updateStatusForm', function(e) {
        e.preventDefault();

        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Enregistrement...');

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#updateStatusModal').modal('hide');
                if (isMobile) {
                    loadMobileOrders();
                } else {
                    dtCommande.ajax.reload(null, false);
                }
                Swal.fire({
                    icon: 'success',
                    title: 'Succès!',
                    text: 'Statut mis à jour',
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Une erreur est survenue'
                });
                submitBtn.prop('disabled', false).html('<i class="ti ti-device-floppy me-1"></i> Enregistrer');
            }
        });
    });

    // Supprimer
    $(document).on('click', '.delete-order', function() {
        const id = $(this).data('id');
        const deleteUrl = "{{ route('commandes.destroy', ':id') }}".replace(':id', id);

        Swal.fire({
            title: 'Êtes-vous sûr?',
            text: "Cette action est irréversible!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Oui, supprimer!',
            cancelButtonText: 'Annuler',
            customClass: {
                confirmButton: 'btn btn-danger me-3',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: deleteUrl,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function() {
                        if (isMobile) {
                            loadMobileOrders();
                        } else {
                            dtCommande.ajax.reload(null, false);
                        }
                        Swal.fire({
                            icon: 'success',
                            title: 'Supprimée!',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                });
            }
        });
    });
});
</script>
@endsection
