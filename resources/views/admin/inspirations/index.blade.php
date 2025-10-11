@extends('admin.layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="mb-4">Liste des Inspirations</h4>
        <div class="row g-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-datatable table-responsive">
                        <table class="datatables-inspirations table border-top">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Image</th>
                                    <th>Titre</th>
                                    <th>Description</th>
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
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 40px;
            height: 20px;
        }
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 20px;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 2px;
            bottom: 2px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        input:checked + .slider {
            background-color: #28a745;
        }
        input:checked + .slider:before {
            transform: translateX(20px);
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
            var dtInspirationTable = $('.datatables-inspirations');

            if (dtInspirationTable.length) {
                var dtInspiration = dtInspirationTable.DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('inspirations.get') }}",
                    columns: [
                        { data: '' },
                        { data: 'image' },
                        { data: 'title' },
                        { data: 'resume' },
                        { data: 'is_active' },
                        { data: '' }
                    ],
                    columnDefs: [
                        {
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
                            render: function(data) {
                                return data;
                            }
                        },
                        {
                            targets: 2,
                            responsivePriority: 4,
                            render: function(data, type, full) {
                                return '<span class="fw-medium">' + full['title'] + '</span>';
                            }
                        },
                        {
                            targets: 3,
                            render: function(data) {
                                return data;
                            }
                        },
                        {
                            targets: 4,
                            render: function(data) {
                                return data;
                            }
                        },
                        {
                            targets: -1,
                            title: 'Actions',
                            searchable: false,
                            orderable: false,
                            render: function(data, type, full) {
                                var editUrl = "{{ route('inspirations.edit', ':id') }}".replace(':id', full.id);
                                var deleteUrl = "{{ route('inspirations.destroy', ':id') }}".replace(':id', full.id);
                                return '<div class="d-flex align-items-center">' +
                                    '<a href="' + editUrl + '" class="text-body"><i class="ti ti-edit ti-sm mx-2"></i></a>' +
                                    '<a href="javascript:;" data-id="' + full.id + '" data-url="' + deleteUrl +
                                    '" class="text-body delete-record"><i class="ti ti-trash ti-sm mx-2"></i></a>' +
                                    '</div>';
                            }
                        }
                    ],
                    order: [[2, 'asc']],
                    dom: '<"row mx-2"' +
                        '<"col-sm-12 col-md-4 col-lg-6"l>' +
                        '<"col-sm-12 col-md-8 col-lg-6"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-md-end justify-content-center flex-sm-nowrap flex-wrap me-1"Bf>>' +
                        '>t' +
                        '<"row mx-2"' +
                        '<"col-sm-12 col-md-6"i>' +
                        '<"col-sm-12 col-md-6"p>' +
                        '>',
                    language: {
                        sEmptyTable: "Aucune donnée disponible dans le tableau",
                        sInfo: "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
                        sInfoEmpty: "Affichage de 0 à 0 sur 0 entrées",
                        sInfoFiltered: "(filtré de _MAX_ entrées totales)",
                        sLengthMenu: "Afficher _MENU_ entrées",
                        sLoadingRecords: "Chargement...",
                        sProcessing: "Traitement...",
                        sSearch: "",
                        sZeroRecords: "Aucun enregistrement correspondant trouvé",
                        oPaginate: {
                            sFirst: "Premier",
                            sLast: "Dernier",
                            sNext: "Suivant",
                            sPrevious: "Précédent"
                        },
                        searchPlaceholder: "Rechercher..."
                    },
                    buttons: [{
                        text: '<i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Ajouter Inspiration</span>',
                        className: 'add-new btn btn-primary ms-2 ms-sm-0',
                        action: function() {
                            window.location.href = "{{ route('inspirations.create') }}";
                        }
                    }],
                    responsive: {
                        details: {
                            display: $.fn.dataTable.Responsive.display.modal({
                                header: function(row) {
                                    var data = row.data();
                                    return 'Détails de ' + data['title'];
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

            // Toggle is_active status
            dtInspirationTable.on('click', '.toggle-active', function() {
                var id = $(this).data('id');
                console.log(id);
                var isChecked = $(this).is(':checked');
                $.ajax({
                    url: "{{ route('inspirations.toggle', ':id') }}".replace(':id', id),
                    type: "put",
                    dataType: "json",
                    data: {
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Succès',
                            text: 'Statut de l\'inspiration mis à jour avec succès.',
                            customClass: {
                                confirmButton: 'btn btn-success'
                            }
                        });
                    },
                    error: function() {
                        $(this).prop('checked', !isChecked); // Revert checkbox state
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: 'Impossible de mettre à jour le statut.',
                            customClass: {
                                confirmButton: 'btn btn-danger'
                            }
                        });
                    }
                });
            });

            // Delete Inspiration
            dtInspirationTable.on('click', '.delete-record', function() {
                var row = $(this).closest('tr');
                var id = $(this).data('id');
                var url = $(this).data('url');
                Swal.fire({
                    title: 'Êtes-vous sûr ?',
                    text: "Vous ne pourrez pas revenir en arrière !",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Oui, supprimer !',
                    cancelButtonText: 'Non, annuler !',
                    customClass: {
                        confirmButton: 'btn btn-primary me-3',
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: "POST",
                            dataType: "json",
                            data: {
                                "_token": "{{ csrf_token() }}",
                                "_method": "DELETE"
                            },
                            success: function() {
                                dtInspiration.row(row).remove().draw();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Supprimé !',
                                    text: 'L\'inspiration a été supprimée.',
                                    customClass: {
                                        confirmButton: 'btn btn-success'
                                    }
                                });
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erreur',
                                    text: 'Impossible de supprimer l\'inspiration.',
                                    customClass: {
                                        confirmButton: 'btn btn-danger'
                                    }
                                });
                            }
                        });
                    }
                });
            });

            // Adjust filter form control size
            setTimeout(() => {
                $('.dataTables_filter .form-control').removeClass('form-control-sm');
                $('.dataTables_length .form-select').removeClass('form-select-sm');
            }, 300);
        });
    </script>
@endsection
