@extends('admin.layouts.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Liste des Commandes</h4>
        </div>
        <div class="row g-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-datatable table-responsive p-4">
                        <div class="mb-4" role="group">
                            <label for="boutique-select" class="form-label fw-semibold mb-2">Filtrer par statut :</label>
                            <select name="statut" id="boutique-select" class="select2filtre form-select w-100 w-md-25"
                                aria-label="Sélection statut">
                                <option value="all" selected>Toutes</option>
                                @forelse ($statuts as $item)
                                    <option value="{{ $item->id }}">
                                        {{ $item->name ?? 'Statut Inconnu' }}
                                    </option>
                                @empty
                                    <option disabled>Aucun statut disponible</option>
                                @endforelse
                            </select>
                        </div>

                        <table class="datatables-commandes table table-hover border-top-0">
                            <thead class="bg-light">
                                <tr>
                                    <th></th>
                                    <th>Date</th>
                                    <th>Client</th>
                                    <th>Total</th>
                                    <th>Produits</th>
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

    <!-- Update Status Modal -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-simple">
            <div class="modal-content p-3 p-md-5">

                <!-- Titre du modal (ajouté) -->
                <div class="modal-header">
                    <h5 class="modal-title">Mettre&nbsp;à&nbsp;jour le statut</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>

                <div class="modal-body" id="bodyContentStatut">
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <style>
        /* Custom Styles for Improved Design */
        .card {
            border-radius: 0.75rem;
            overflow: hidden;
        }

        .card-datatable {
            padding: 1.5rem;
        }

        .table th,
        .table td {
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
            transition: all 0.2s ease;
        }

        .form-select {
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            font-size: 0.95rem;
        }

        .dataTables_wrapper .dataTables_filter input,
        .dataTables_wrapper .dataTables_length select {
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            border: 1px solid #ced4da;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 0.5rem;
            margin: 0 0.25rem;
            padding: 0.5rem 1rem;
            transition: all 0.2s ease;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background-color: #007bff;
            color: white !important;
            border: none;
        }

        .btn-action {
            border-radius: 0.5rem;
            padding: 0.5rem;
            transition: all 0.2s ease;
        }

        .btn-action:hover {
            background-color: #e9ecef;
        }

        /* Modal Styling */
        .modal-content {
            border-radius: 0.75rem;
            border: none;
        }

        .modal-header,
        .modal-footer {
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
            var dtCommandeTable = $('.datatables-commandes'),
                statusObj = {
                    0: {
                        title: 'En cours de traitement',
                        class: 'bg-label-warning'
                    },
                    1: {
                        title: 'Livrée et payée',
                        class: 'bg-label-success'
                    },
                    2: {
                        title: 'Annulé',
                        class: 'bg-label-danger'
                    }
                };

            if (dtCommandeTable.length) {
                var dtCommande = dtCommandeTable.DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('commandes.get') }}",
                        type: "GET",
                        data: function(d) {
                            d._token = '{{ csrf_token() }}';
                            d.statut_id = $('select[name="statut"]').val();
                        }
                    },
                    columns: [{
                            data: ''
                        },
                        {
                            data: 'date'
                        },
                        {
                            data: 'client'
                        },
                        {
                            data: 'subtotal_ht'
                        },
                        {
                            data: 'produits'
                        },
                        {
                            data: 'statut'
                        },
                        {
                            data: ''
                        }
                    ],
                    columnDefs: [{
                            className: 'control',
                            orderable: false,
                            searchable: false,
                            responsivePriority: 2,
                            targets: 0,
                            render: function() {
                                return '';
                            }
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
                                return full['client'] && full['client'].trim() !== '' ?
                                    `<span class="badge bg-primary">${full['client']}</span>` :
                                    `<span class="badge bg-secondary">Non spécifié</span>`;
                            }
                        },
                        {
                            targets: 3,
                            render: function(data, type, full) {
                                // Sécurise les valeurs numériques
                                const subtotal = Number(full.subtotal_ht ?? 0);
                                const shipping = Number(full.shipping_cost ?? 0);

                                // Si aucun montant → badge gris
                                if (subtotal === 0 && shipping === 0) {
                                    return '<span class="badge bg-label-secondary">Aucun&nbsp;Total</span>';
                                }

                                // Total TTC (HT + port)
                                const total = subtotal + shipping;

                                // Format “fr-TN” avec 3 décimales
                                const formatted = total.toLocaleString('fr-TN', {
                                    minimumFractionDigits: 3,
                                    maximumFractionDigits: 3
                                });

                                return `<span class="fw-bold text-success fs-tiny">${formatted}&nbsp;DT</span>`;
                            }
                        },

                        {
                            targets: 4,
                            responsivePriority: 3,
                            render: function(data, type, full) {
                                if (!Array.isArray(full.produits) || !full.produits.length) {
                                    return '<span class="text-muted">–</span>';
                                }
                                if (type !== 'display') {
                                    return full.produits.map(p => `${p.quantity}× ${p.name}`).join(
                                        ', ');
                                }
                                const MAX_VISIBLE = 3;
                                const visibles = full.produits.slice(0, MAX_VISIBLE);
                                const restants = full.produits.slice(MAX_VISIBLE);
                                let html = visibles.map(p =>
                                    `<span class="badge bg-label-info me-1 mb-1">${p.quantity}× ${p.name}</span>`
                                ).join('');
                                if (restants.length) {
                                    const tooltip = restants.map(p => `${p.quantity}× ${p.name}`)
                                        .join('\n');
                                    html += `
                                        <span class="badge bg-label-secondary me-1 mb-1" title="${tooltip.replace(/"/g,'"')}"
                                              data-bs-toggle="tooltip" data-bs-placement="top" style="cursor:pointer">
                                            +${restants.length}
                                        </span>`;
                                }
                                return html;
                            }
                        },
                        {
                            targets: 5,
                            render: function(data, type, full) {
                                let statut = full['statut']?.toLowerCase();
                                switch (statut) {
                                    case 'annulé':
                                        return '<span class="badge bg-label-danger">Annulé</span>';
                                    case 'livré et payé':
                                    case 'livrée et payée':
                                        return '<span class="badge bg-label-success">Livrée et Payée</span>';
                                    case 'en cours de traitement':
                                        return '<span class="badge bg-label-warning">En cours de traitement</span>';
                                    case 'en cours de livraison':
                                        return '<span class="badge bg-label-info">En cours de livraison</span>';
                                    default:
                                        return '<span class="badge bg-label-secondary">Inconnu</span>';
                                }
                            }
                        },
                        {
                            targets: -1,
                            title: 'Actions',
                            searchable: false,
                            orderable: false,
                            render: function(data, type, full) {
                                var deleteUrl = "{{ route('commandes.destroy', ':id') }}".replace(
                                    ':id', full.id);
                                var pdfUrl = "{{ route('commandes.pdf', ':id') }}".replace(':id',
                                    full.id);
                                return `
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="#" data-id="${full.id}" data-url="${deleteUrl}" class="delete-record btn-action text-danger" title="Supprimer">
                                            <i class="ti ti-trash ti-sm"></i>
                                        </a>
                                        <a href="${pdfUrl}" target="_blank" class="btn-action text-primary" title="Générer PDF">
                                            <i class="ti ti-file-text ti-sm"></i>
                                        </a>
                                        <a href="#" class="edit-record-settings-up btn-action text-info" data-id="${full.id}" title="Modifier Statut">
                                            <i class="ti ti-settings ti-sm"></i>
                                        </a>
                                    </div>`;
                            }
                        }
                    ],
                    order: [
                        [1, 'desc']
                    ],
                    dom: '<"row mx-2 align-items-center"' +
                        '<"col-md-6"l>' +
                        '<"col-md-6"f>' +
                        '>t' +
                        '<"row mx-2"' +
                        '<"col-sm-12 col-md-6"i>' +
                        '<"col-sm-12 col-md-6"p>' +
                        '>',
                    language: {
                        sEmptyTable: "Aucune donnée disponible dans le tableau",
                        sInfo: "Affichage de l'élément _START_ à _END_ sur _TOTAL_ éléments",
                        sInfoEmpty: "Affichage de l'élément 0 à 0 sur 0 élément",
                        sInfoFiltered: "(filtré à partir de _MAX_ éléments au total)",
                        sLengthMenu: "Afficher _MENU_ éléments",
                        sLoadingRecords: "Chargement...",
                        sProcessing: "Traitement...",
                        sSearch: "",
                        sZeroRecords: "Aucun résultat trouvé",
                        oPaginate: {
                            sFirst: "Premier",
                            sLast: "Dernier",
                            sNext: "Suivant",
                            sPrevious: "Précédent"
                        },
                        searchPlaceholder: "Rechercher une commande..."
                    },
                    responsive: {
                        details: {
                            display: $.fn.dataTable.Responsive.display.modal({
                                header: function(row) {
                                    var data = row.data();
                                    return 'Détails de la commande';
                                }
                            }),
                            type: 'column',
                            renderer: function(api, rowIdx, columns) {
                                var data = $.map(columns, function(col, i) {
                                    return col.title !== '' ?
                                        '<tr data-dt-row="' + col.rowIndex +
                                        '" data-dt-column="' + col.columnIndex + '">' +
                                        '<td>' + col.title + ':' + '</td> ' +
                                        '<td>' + col.data + '</td>' +
                                        '</tr>' : '';
                                }).join('');
                                return data ? $('<table class="table"/><tbody />').append(data) : false;
                            }
                        }
                    }
                });
            }

            $('.select2filtre').select2({
                placeholder: 'Sélectionner un statut',
                allowClear: true
            });

            $('select[name="statut"]').on('change', function() {
                dtCommande.ajax.reload();
            });

            $(document).on('click', '.edit-record-settings-up', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                const editUrl = "{{ route('commandes.edit-status', ':id') }}".replace(':id', id);

                $('#bodyContentStatut').load(editUrl, function() {
                    $("#updateStatusModal").modal('show');

                    $('#updateStatusModal .select2').select2({
                        dropdownParent: $('#updateStatusModal')
                    });

                    $('#StatutaddValidationErrors').hide().empty();

                    $('#updateStatusForm').off('submit').on('submit', function(event) {
                        event.preventDefault();
                        $.ajax({
                            url: $(this).attr('action'),
                            type: "POST",
                            dataType: "json",
                            data: $(this).serialize(),
                            success: function(response) {
                                $('#updateStatusModal').modal('hide');
                                dtCommande.ajax.reload();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Succès',
                                    text: 'Commande mise à jour avec succès.',
                                    customClass: {
                                        confirmButton: 'btn btn-success'
                                    }
                                });
                            },
                            error: function(xhr) {
                                $('#StatutaddValidationErrors').empty().show();
                                if (xhr.responseJSON && xhr.responseJSON
                                    .errors) {
                                    $.each(xhr.responseJSON.errors, function(
                                        key, value) {
                                        $('#StatutaddValidationErrors')
                                            .append('<div>' + value +
                                                '</div>');
                                    });
                                } else {
                                    $('#StatutaddValidationErrors').append(
                                        '<div>Une erreur est survenue.</div>'
                                    );
                                }
                            }
                        });
                    });
                });
            });

            dtCommandeTable.on('click', '.delete-record', function() {
                var row = $(this).closest('tr');
                var id = $(this).data('id');
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
                            data: {
                                "_token": "{{ csrf_token() }}"
                            },
                            success: function() {
                                dtCommande.row(row).remove().draw();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Supprimé !',
                                    text: 'La commande a été supprimée.',
                                    customClass: {
                                        confirmButton: 'btn btn-success'
                                    }
                                });
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erreur',
                                    text: 'Impossible de supprimer la commande.',
                                    customClass: {
                                        confirmButton: 'btn btn-danger'
                                    }
                                });
                            }
                        });
                    }
                });
            });

            setTimeout(() => {
                $('.dataTables_filter .form-control').removeClass('form-control-sm').addClass(
                    'form-control');
                $('.dataTables_length .form-select').removeClass('form-select-sm').addClass('form-select');
            }, 300);
        });
    </script>
@endsection
