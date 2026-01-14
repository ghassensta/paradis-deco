@extends('admin.layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="mb-4">Listes des Produits</h4>
        <div class="row g-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-datatable table-responsive">
                        <table class="datatables-paks table border-top w-100">
                            <thead>
                                <tr>
                                    <th></th> {{-- 0: Responsive control --}}
                                    <th>Images</th> {{-- 1 --}}
                                    <th>Nom</th> {{-- 2 --}}
                                    <th>Catégories</th> {{-- 3 --}}
                                    <th class="text-end">Prix</th> {{-- 4 --}}
                                    <th class="text-end">Stock</th> {{-- 5 --}}
                                    <th>Statut</th> {{-- 6 --}}
                                    <th>Actions</th> {{-- 7 --}}
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for Images -->
        <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="imageModalLabel">Images du produit</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="imageModalContent" class="d-flex flex-wrap gap-2"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/typography.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/katex.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/editor.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/dropzone/dropzone.css') }}" />
@endsection

@section('js')
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        'use strict';

        $(function() {
            const dtPakTable = $('.datatables-paks');
            if (!dtPakTable.length) return;

            /* Status badges */
            const statusObj = {
                0: {
                    title: 'Inactif',
                    class: 'bg-label-warning'
                },
                1: {
                    title: 'Actif',
                    class: 'bg-label-success'
                }
            };

            const dtPak = dtPakTable.DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('products.get') }}",
                    type: 'GET',
                    data: d => {
                        d._token = '{{ csrf_token() }}';
                    }
                },
                columns: [
                    { data: null }, // 0: Responsive control
                    { data: 'image_avant' }, // 1: Images
                    { data: 'name' }, // 2: Name
                    { data: 'categories' }, // 3: Categories
                    { data: 'price' }, // 4: Price
                    { data: 'stock' }, // 5: Stock
                    { data: 'is_active' }, // 6: Status
                    { data: null } // 7: Actions
                ],
                columnDefs: [
                    {
                        targets: 0,
                        className: 'control',
                        orderable: false,
                        searchable: false,
                        render: () => ''
                    },
                    {
                        targets: 1,
                        render: (_, __, row) => {
                            const base = '{{ asset('storage') }}/';
                            const coverImage = row.image_avant ? `<img src="${base}${row.image_avant}" width="50" height="50" class="img-thumbnail" loading="lazy">` : '-';
                            const viewAllButton = `<button class="btn btn-sm btn-primary ms-2 view-images" data-images='${JSON.stringify([row.image_avant, ...(row.images || [])])}' data-bs-toggle="modal" data-bs-target="#imageModal">Voir tout</button>`;
                            return `<div class="d-flex align-items-center">${coverImage}${viewAllButton}</div>`;
                        }
                    },
                    {
                        targets: 2,
                        render: (_, __, row) => `<span class="fw-medium">${row.name}</span>`
                    },
                    {
                        targets: 3,
                        render: (data, type, row) => {
                            if (!row.categories || !row.categories.length) return '-';
                            const cats = Array.isArray(row.categories) ? row.categories : [row.categories];
                            const badges = [];
                            let charSum = 0;

                            for (const cat of cats) {
                                const safe = $('<div>').text(cat).html();
                                if (charSum + safe.length > 50) {
                                    badges.push('<span class="badge bg-secondary">…</span>');
                                    break;
                                }
                                badges.push(`<span class="badge bg-primary mb-2 me-1">${safe}</span>`);
                                charSum += safe.length;
                            }
                            return badges.join('');
                        }
                    },
                    {
                        targets: 4,
                        className: 'text-end',
                        render: (_, __, row) => `${parseFloat(row.price).toFixed(2)} DT`
                    },
                    {
                        targets: 5,
                        className: 'text-end'
                    },
                    {
                        targets: 6,
                        render: (_, __, row) => {
                            const s = row.is_active ? 1 : 0;
                            return `<span class="badge ${statusObj[s].class}">${statusObj[s].title}</span>`;
                        }
                    },
                    {
                        targets: 7,
                        orderable: false,
                        searchable: false,
                        responsivePriority: 1,
                        render: (_, __, row) => {
                            const editUrl = "{{ route('produits.edit', ':id') }}".replace(':id', row.id);
                            const deleteUrl = "{{ route('produits.destroy', ':id') }}".replace(':id', row.id);
                            return `
                                <div class="d-flex align-items-center">
                                    <a href="${editUrl}" class="text-body me-2" title="Modifier">
                                        <i class="ti ti-edit ti-sm"></i>
                                    </a>
                                    <a href="javascript:;" data-url="${deleteUrl}" class="text-body delete-record" title="Supprimer">
                                        <i class="ti ti-trash ti-sm"></i>
                                    </a>
                                </div>`;
                        }
                    }
                ],
                order: [[2, 'asc']],
                buttons: [{
                    text: '<i class="ti ti-plus me-1"></i><span class="d-none d-sm-inline">Ajouter Produit</span>',
                    className: 'btn btn-primary mt-3',
                    action: () => window.location = "{{ route('produits.create') }}"
                }],
                dom: '<"row mx-2"<"col-sm-12 col-md-4"l><"col-sm-12 col-md-8 text-end"Bf>>t<"row mx-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                language: {
                    sEmptyTable: 'Aucune donnée disponible',
                    sInfo: 'Affichage _START_-_END_ sur _TOTAL_',
                    sInfoEmpty: 'Affichage 0-0 sur 0',
                    sInfoFiltered: '(filtré de _MAX_ au total)',
                    sLengthMenu: 'Afficher _MENU_',
                    sLoadingRecords: 'Chargement…',
                    sProcessing: 'Traitement…',
                    sSearch: '',
                    sZeroRecords: 'Aucun résultat',
                    oPaginate: {
                        sFirst: 'Premier',
                        sLast: 'Dernier',
                        sNext: 'Suivant',
                        sPrevious: 'Précédent'
                    },
                    searchPlaceholder: 'Chercher…'
                },
                responsive: {
                    details: {
                        type: 'column'
                    }
                }
            });

            /* UI adjustments */
            setTimeout(() => {
                $('.dataTables_filter .form-control').removeClass('form-control-sm');
                $('.dataTables_length .form-select').removeClass('form-select-sm');
            }, 300);

            /* Handle modal image display */
            dtPakTable.on('click', '.view-images', function() {
                const images = JSON.parse($(this).attr('data-images'));
                const modalContent = $('#imageModalContent');
                modalContent.empty();

                if (images && images.length) {
                    images.forEach(image => {
                        if (image && /\.(jpe?g|png|gif|webp|bmp|svg)$/i.test(image)) {
                            modalContent.append(`
                                <a href="{{ asset('storage') }}/${image}" target="_blank">
                                    <img src="{{ asset('storage') }}/${image}" class="img-fluid mb-2" style="max-width: 200px; loading:lazy; height: auto;" loading="lazy">
                                </a>
                            `);
                        }
                    });
                } else {
                    modalContent.append('<p>Aucune image disponible.</p>');
                }
            });

            /* Delete action */
            dtPakTable.on('click', '.delete-record', function() {
                const url = $(this).data('url');
                const row = $(this).closest('tr');

                Swal.fire({
                    title: 'Supprimer ?',
                    text: 'Cette action est définitive.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Oui, supprimer',
                    cancelButtonText: 'Annuler',
                    customClass: {
                        confirmButton: 'btn btn-primary me-3',
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false
                }).then(res => {
                    if (!res.isConfirmed) return;

                    $.ajax({
                        url,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        }
                    }).done(() => {
                        dtPak.row(row).remove().draw();
                        Swal.fire({
                            icon: 'success',
                            title: 'Supprimé',
                            timer: 1500
                        });
                    }).fail(() => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            timer: 1500
                        });
                    });
                });
            });
        });
    </script>
@endsection
