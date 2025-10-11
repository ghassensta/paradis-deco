@extends('admin.layouts.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Liste des Avis</h4>
            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAvisModal">
                <i class="ti ti-plus me-1"></i> Ajouter un avis
            </a>
        </div>
        <div class="row g-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-datatable table-responsive p-4">
                        <div class="mb-4" role="group">
                            <label for="product-select" class="form-label fw-semibold mb-2">Filtrer par produit :</label>
                            <select name="product_id" id="product-select" class="select2filtre form-select w-100 w-md-25"
                                aria-label="Sélection produit">
                                <option value="all" selected>Tous</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <table class="datatables-avis table table-hover border-top-0">
                            <thead class="bg-light">
                                <tr>
                                    <th></th>
                                    <th>Date</th>
                                    <th>Produit</th>
                                    <th>Note</th>
                                    <th>Commentaire</th>
                                    <th>Client</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Avis Modal -->
    <div class="modal fade" id="createAvisModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-simple">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter un nouvel avis</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <form id="createAvisForm" action="{{ route('avis.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="product_id" class="form-label">Produit</label>
                            <select name="product_id" id="product_id" class="form-select select2" required>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="rating" class="form-label">Note</label>
                            <select name="rating" id="rating" class="form-select" required>
                                <option value="1">1 étoile</option>
                                <option value="2">2 étoiles</option>
                                <option value="3">3 étoiles</option>
                                <option value="4">4 étoiles</option>
                                <option value="5">5 étoiles</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="comment" class="form-label">Commentaire</label>
                            <textarea name="comment" id="comment" class="form-control" rows="4" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Nom du client</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="approved" class="form-label">Statut</label>
                            <select name="approved" id="approved" class="form-select">
                                <option value="1">Approuvé</option>
                                <option value="0" selected>En attente</option>
                            </select>
                        </div>
                        <div id="createValidationErrors" class="alert alert-danger" style="display:none;"></div>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Avis Modal -->
    <div class="modal fade" id="editAvisModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-simple">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier l'avis</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body" id="editAvisContent"></div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <style>
        .card {
            border-radius: 0.75rem;
            overflow: hidden;
        }
        .card-datatable {
            padding: 1.5rem;
        }
        .table th, .table td {
            vertical-align: middle;
            padding: 1rem;
        }
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }
        .badge {
            font-size: 0.85rem;
            padding: 0.5em 0.75em;
            border-radius: 0.5rem;
        }
        .form-select, .form-control {
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 0.5rem;
            margin: 0 0.25rem;
            padding: 0.5rem 1rem;
        }
        .modal-content {
            border-radius: 0.75rem;
            border: none;
        }
    </style>
@endsection

@section('js')
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        'use strict';

        $(function() {
            var dtAvisTable = $('.datatables-avis');

            if (dtAvisTable.length) {
                var dtAvis = dtAvisTable.DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('avis.get') }}",
                        type: "GET",
                        data: function(d) {
                            d._token = '{{ csrf_token() }}';
                            d.product_id = $('select[name="product_id"]').val();
                        }
                    },
                    columns: [
                        { data: '' },
                        { data: 'date' },
                        { data: 'product' },
                        { data: 'rating' },
                        { data: 'comment' },
                        { data: 'name' },
                        { data: 'approved' },
                        { data: '' }
                    ],
                    columnDefs: [
                        {
                            className: 'control',
                            orderable: false,
                            searchable: false,
                            targets: 0,
                            render: function() { return ''; }
                        },
                        {
                            targets: 1,
                            render: function(data, type, full) {
                                return `<span class="fw-medium text-dark">${full['date']}</span>`;
                            }
                        },
                        {
                            targets: 2,
                            render: function(data, type, full) {
                                return full['product'] && full['product'].trim() !== '' ?
                                    `<span class="badge bg-primary">${full['product']}</span>` :
                                    `<span class="badge bg-secondary">Non spécifié</span>`;
                            }
                        },
                        {
                            targets: 3,
                            render: function(data, type, full) {
                                const rating = Number(full.rating ?? 0);
                                let stars = '';
                                for (let i = 1; i <= 5; i++) {
                                    stars += i <= rating ?
                                        '<i class="ti ti-star-filled text-warning"></i>' :
                                        '<i class="ti ti-star text-muted"></i>';
                                }
                                return stars;
                            }
                        },
                        {
                            targets: 4,
                            render: function(data, type, full) {
                                return full['comment'].length > 50 ?
                                    full['comment'].substring(0, 50) + '...' :
                                    full['comment'];
                            }
                        },
                        {
                            targets: 5,
                            render: function(data, type, full) {
                                return `<span class="badge bg-info">${full['name']}</span>`;
                            }
                        },
                        {
                            targets: 6,
                            render: function(data, type, full) {
                                return full['approved'] === 'Approuvé' ?
                                    '<span class="badge bg-label-success">Approuvé</span>' :
                                    '<span class="badge bg-label-warning">En attente</span>';
                            }
                        },
                        {
                            targets: -1,
                            title: 'Actions',
                            searchable: false,
                            orderable: false,
                            render: function(data, type, full) {
                                var deleteUrl = "{{ route('avis.destroy', ':id') }}".replace(':id', full.id);
                                var editUrl = "{{ route('avis.edit', ':id') }}".replace(':id', full.id);
                                return `
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="#" data-id="${full.id}" data-url="${editUrl}" class="edit-record btn-action text-info" title="Modifier">
                                            <i class="ti ti-edit ti-sm"></i>
                                        </a>
                                        <a href="#" data-id="${full.id}" data-url="${deleteUrl}" class="delete-record btn-action text-danger" title="Supprimer">
                                            <i class="ti ti-trash ti-sm"></i>
                                        </a>
                                    </div>`;
                            }
                        }
                    ],
                    order: [[1, 'desc']],
                    dom: '<"row mx-2 align-items-center"<"col-md-6"l><"col-md-6"f>>t<"row mx-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                    language: {
                        sEmptyTable: "Aucune donnée disponible",
                        sInfo: "Affichage de _START_ à _END_ sur _TOTAL_ avis",
                        sInfoEmpty: "Affichage de 0 à 0 sur 0 avis",
                        sInfoFiltered: "(filtré à partir de _MAX_ avis)",
                        sLengthMenu: "Afficher _MENU_ avis",
                        sLoadingRecords: "Chargement...",
                        sProcessing: "Traitement...",
                        sSearch: "",
                        sZeroRecords: "Aucun résultat trouvé",
                        searchPlaceholder: "Rechercher un avis...",
                        oPaginate: {
                            sFirst: "Premier",
                            sLast: "Dernier",
                            sNext: "Suivant",
                            sPrevious: "Précédent"
                        }
                    },
                    responsive: {
                        details: {
                            display: $.fn.dataTable.Responsive.display.modal({
                                header: function(row) {
                                    return 'Détails de l\'avis';
                                }
                            }),
                            type: 'column',
                            renderer: function(api, rowIdx, columns) {
                                var data = $.map(columns, function(col, i) {
                                    return col.title !== '' ?
                                        '<tr data-dt-row="' + col.rowIndex + '" data-dt-column="' + col.columnIndex + '">' +
                                        '<td>' + col.title + ':' + '</td><td>' + col.data + '</td></tr>' : '';
                                }).join('');
                                return data ? $('<table class="table"/><tbody />').append(data) : false;
                            }
                        }
                    }
                });
            }

            $('.select2filtre, .select2').select2({
                placeholder: 'Sélectionner un produit',
                allowClear: true,
            });

            $('select[name="product_id"]').on('change', function() {
                dtAvis.ajax.reload();
            });

            $('#createAvisForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: $(this).attr('action'),
                    type: "POST",
                    dataType: "json",
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#createAvisModal').modal('hide');
                        dtAvis.ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Succès',
                            text: response.message,
                            customClass: { confirmButton: 'btn btn-success' }
                        });
                    },
                    error: function(xhr) {
                        $('#createValidationErrors').empty().show();
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                $('#createValidationErrors').append('<div>' + value + '</div>');
                            });
                        } else {
                            $('#createValidationErrors').append('<div>Une erreur est survenue.</div>');
                        }
                    }
                });
            });

            $(document).on('click', '.edit-record', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                const editUrl = $(this).data('url');

                $('#editAvisContent').load(editUrl, function() {
                    $('#editAvisModal').modal('show');
                    $('#editAvisContent .select2').select2({
                        dropdownParent: $('#editAvisModal')
                    });
                });
            });

            dtAvisTable.on('click', '.delete-record', function() {
                var row = $(this).closest('tr');
                var url = $(this).data('url');
                Swal.fire({
                    title: 'Êtes-vous sûr(e) ?',
                    text: "Vous ne pourrez pas revenir en arrière !",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Oui, supprimez-le !',
                    cancelButtonText: 'Non, annulez !',
                    customClass: {
                        confirmButton: 'btn btn-primary me-3',
                        cancelButton: 'btn btn-label-secondary'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: "DELETE",
                            dataType: "json",
                            data: { "_token": "{{ csrf_token() }}" },
                            success: function(response) {
                                dtAvis.row(row).remove().draw();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Supprimé !',
                                    text: response.message,
                                    customClass: { confirmButton: 'btn btn-success' }
                                });
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erreur',
                                    text: 'Impossible de supprimer l\'avis.',
                                    customClass: { confirmButton: 'btn btn-danger' }
                                });
                            }
                        });
                    }
                });
            });

            setTimeout(() => {
                $('.dataTables_filter .form-control').removeClass('form-control-sm').addClass('form-control');
                $('.dataTables_length .form-select').removeClass('form-select-sm').addClass('form-select');
            }, 300);
        });
    </script>
@endsection
