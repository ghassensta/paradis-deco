@extends('admin.layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="mb-4">Liste des Categories</h4>
        <div class="row g-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-datatable table-responsive">
                        <table class="datatables-categories table border-top">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Image</th>
                                    <th>Nom Categorie</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-simple">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center mb-4">
                        <h3 class="mb-2">Ajouter Categorie</h3>
                    </div>
                    <form id="addCategoryModalForm" action="{{ route('categories.store') }}" method="POST" class="row g-3"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="modalAddCategoryName">Nom Categorie</label>
                            <input type="text" id="modalAddCategoryName" name="name" required class="form-control"
                                placeholder="Category name" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="modalAddCategoryImage">Image</label>
                            <input type="file" id="modalAddCategoryImage" name="image" required class="form-control"
                                accept="image/*" />
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="modalAddCategoryStatus">Statut</label>
                            <select id="modalAddCategoryStatus" required name="is_active" class="select2 form-select">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="col-12 text-center mt-4">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1">Enregistrer</button>
                            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                                aria-label="Close">Fermer</button>
                        </div>
                        <div class="col-12">
                            <div id="addValidationErrors" class="alert alert-danger" style="display: none;"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-simple">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center mb-4">
                        <h3 class="mb-2">Modifier Categorie</h3>
                    </div>
                    <form id="editCategoryModalForm" action="" method="POST" class="row g-3"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" id="editCategoryModalId">
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="modalEditCategoryName">Name</label>
                            <input type="text" id="modalEditCategoryName" name="name" required class="form-control"
                                placeholder="Category name" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="modalEditCategoryImage">Image</label>
                            <input type="file" id="modalEditCategoryImage" name="image" class="form-control"
                                accept="image/*" />
                            <small class="form-text text-muted">Laissez vide pour conserver l’image actuelle.</small>
                            <div id="currentImagePreview" class="mt-2" style="display: none;">
                                <img id="currentImage" src="" width="100" alt="Current Image">
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="modalEditCategoryStatus">Statut</label>
                            <select id="modalEditCategoryStatus" required name="is_active" class="select2 form-select">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="col-12 text-center mt-4">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1">Enregistrer</button>
                            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                                aria-label="Close">Fermer</button>
                        </div>
                        <div class="col-12">
                            <div id="editValidationErrors" class="alert alert-danger" style="display: none;"></div>
                        </div>
                    </form>
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
@endsection

@section('js')
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        'use strict';

        $(function() {
            var dtCategoryTable = $('.datatables-categories'),
                statusObj = {
                    0: {
                        title: 'Inactive',
                        class: 'bg-label-warning'
                    },
                    1: {
                        title: 'Active',
                        class: 'bg-label-success'
                    }
                };

            if (dtCategoryTable.length) {
                var dtCategory = dtCategoryTable.DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('category.get') }}",
                    columns: [{
                            data: ''
                        },
                        {
                            data: 'image'
                        },
                        {
                            data: 'name'
                        },
                        {
                            data: 'is_active'
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
                            render: function(data) {
                                return data; // Image rendering handled server-side
                            }
                        },
                        {
                            targets: 2,
                            responsivePriority: 4,
                            render: function(data, type, full) {
                                return '<span class="fw-medium">' + full['name'] + '</span>';
                            }
                        },
                        {
                            targets: 3,
                            render: function(data) {
                                return data; // Status badge handled server-side
                            }
                        },
                        {
                            targets: -1,
                            title: 'Actions',
                            searchable: false,
                            orderable: false,
                            render: function(data, type, full) {
                                var editUrl = "{{ route('categories.edit', ':id') }}".replace(':id',
                                    full.id);
                                var deleteUrl = "{{ route('categories.destroy', ':id') }}".replace(
                                    ':id', full.id);
                                return '<div class="d-flex align-items-center">' +
                                    '<a href="javascript:;" data-id="' + full.id +
                                    '" class="text-body edit-record" data-bs-toggle="modal" data-bs-target="#editCategoryModal"><i class="ti ti-edit ti-sm mx-2"></i></a>' +
                                    '<a href="javascript:;" data-id="' + full.id + '" data-url="' +
                                    deleteUrl +
                                    '" class="text-body delete-record"><i class="ti ti-trash ti-sm mx-2"></i></a>' +
                                    '</div>';
                            }
                        }
                    ],
                    order: [
                        [2, 'asc']
                    ],
                    dom: '<"row mx-2"' +
                        '<"col-sm-12 col-md-4 col-lg-6"l>' +
                        '<"col-sm-12 col-md-8 col-lg-6"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-md-end justify-content-center flex-sm-nowrap flex-wrap me-1"Bf>>' +
                        '>t' +
                        '<"row mx-2"' +
                        '<"col-sm-12 col-md-6"i>' +
                        '<"col-sm-12 col-md-6"p>' +
                        '>',
                    language: {
                        sEmptyTable: "No data available in the table",
                        sInfo: "Showing _START_ to _END_ of _TOTAL_ entries",
                        sInfoEmpty: "Showing 0 to 0 of 0 entries",
                        sInfoFiltered: "(filtered from _MAX_ total entries)",
                        sLengthMenu: "Show _MENU_ entries",
                        sLoadingRecords: "Loading...",
                        sProcessing: "Processing...",
                        sSearch: "",
                        sZeroRecords: "No matching records found",
                        oPaginate: {
                            sFirst: "First",
                            sLast: "Last",
                            sNext: "Next",
                            sPrevious: "Previous"
                        },
                        searchPlaceholder: "Search..."
                    },
                    buttons: [{
                        text: '<i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Ajouter Categorie</span>',
                        className: 'add-new btn btn-primary ms-2 ms-sm-0',
                        action: function() {
                            $('#addCategoryModal').modal('show');
                        }
                    }],
                    responsive: {
                        details: {
                            display: $.fn.dataTable.Responsive.display.modal({
                                header: function(row) {
                                    var data = row.data();
                                    return 'Details of ' + data['name'];
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

            // Pour le modal de création
            $('#addCategoryModal').on('shown.bs.modal', function() {
                // 'this' est le <div id="addCategoryModal">
                $(this).find('.select2').select2({
                    placeholder: function() {
                        return $(this).data('placeholder');
                    },
                    allowClear: true,
                    width: 'resolve',
                    dropdownParent: $(this) // on attache le dropdown à l'intérieur du modal
                });
            });

            // Pour le modal d’édition
            $('#editCategoryModal').on('shown.bs.modal', function() {
                $(this).find('.select2').select2({
                    placeholder: function() {
                        return $(this).data('placeholder');
                    },
                    allowClear: true,
                    width: 'resolve',
                    dropdownParent: $(this)
                });
            });

            // Add Category Form Submission
            $('#addCategoryModalForm').on('submit', function(event) {
                event.preventDefault();
                var formData = new FormData(this);
                $.ajax({
                    url: $(this).attr('action'),
                    type: "POST",
                    dataType: "json",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#addCategoryModal').modal('hide');
                        $('#addCategoryModalForm')[0].reset();
                        dtCategory.ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Category added successfully.',
                            customClass: {
                                confirmButton: 'btn btn-success'
                            }
                        });
                    },
                    error: function(xhr) {
                        $('#addValidationErrors').empty().show();
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                $('#addValidationErrors').append('<div>' + value +
                                    '</div>');
                            });
                        } else {
                            $('#addValidationErrors').append('<div>An error occurred.</div>');
                        }
                    }
                });
            });

            // Edit Category - Populate Modal
            dtCategoryTable.on('click', '.edit-record', function() {
                var id = $(this).data('id');
                $.ajax({
                    url: "{{ route('categories.edit', ':id') }}".replace(':id', id),
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        $('#editCategoryModalId').val(data.id);
                        $('#modalEditCategoryName').val(data.name);
                        $('#modalEditCategoryStatus').val(data.is_active).trigger('change');
                        // Show current image preview
                        if (data.image_url) {
                            $('#currentImage').attr('src', data.image_url);
                            $('#currentImagePreview').show();
                        } else {
                            $('#currentImagePreview').hide();
                        }
                        $('#editCategoryModalForm').attr('action',
                            "{{ route('categories.update', ':id') }}".replace(':id', data
                                .id));
                        $('#editCategoryModal').modal('show');
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Unable to load category data.',
                            customClass: {
                                confirmButton: 'btn btn-danger'
                            }
                        });
                    }
                });
            });

            // Edit Category Form Submission
            $('#editCategoryModalForm').on('submit', function(event) {
                event.preventDefault();
                var formData = new FormData(this);
                formData.append('_method', 'PUT');
                $.ajax({
                    url: $(this).attr('action'),
                    type: "POST",
                    dataType: "json",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#editCategoryModal').modal('hide');
                        dtCategory.ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Category updated successfully.',
                            customClass: {
                                confirmButton: 'btn btn-success'
                            }
                        });
                    },
                    error: function(xhr) {
                        $('#editValidationErrors').empty().show();
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                $('#editValidationErrors').append('<div>' + value +
                                    '</div>');
                            });
                        } else {
                            $('#editValidationErrors').append('<div>An error occurred.</div>');
                        }
                    }
                });
            });

            // Delete Category
            dtCategoryTable.on('click', '.delete-record', function() {
                var row = $(this).closest('tr');
                var id = $(this).data('id');
                var url = $(this).data('url');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, cancel!',
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
                                dtCategory.row(row).remove().draw();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'The category has been deleted.',
                                    customClass: {
                                        confirmButton: 'btn btn-success'
                                    }
                                });
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Unable to delete the category.',
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
