@extends('admin.layouts.app')
{{-- --------------------------------------------------------------------
|  Feuilles de style spécifiques
--------------------------------------------------------------------- --}}
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/dropzone/dropzone.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
@endsection

{{-- --------------------------------------------------------------------
|  Contenu principal
--------------------------------------------------------------------- --}}
@section('content')
    <div class="container-fluid">
        <div class="card mb-4 shadow-sm">
            <div class="card-header text-white">
                <h4 class="mb-0">Modifier le produit</h4>
            </div>

            <div class="card-body">
                <form id="productForm" class="needs-validation" action="{{ route('produits.update', $product->id) }}"
                    method="POST" enctype="multipart/form-data" novalidate>
                    @csrf
                    @method('PUT')

                    {{-- ----------------   Image de couverture (image_avant)   ---------------- --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold" for="image_avant">
                            Image de couverture
                            <span class="fw-normal">(image principale du produit, format WebP recommandé)</span>
                        </label>
                        <div class="input-group">
                            <input type="file" name="image_avant" id="image_avant" class="form-control"
                                accept="image/*">
                            <div class="invalid-feedback">Veuillez sélectionner une image valide (JPEG, PNG, JPG, GIF, SVG).</div>
                        </div>
                        @if ($product->image_avant)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $product->image_avant) }}" loading="lazy" alt="Cover Image"
                                    style="max-width: 150px; height: auto;">
                                <input type="hidden" name="old_image_avant" value="{{ $product->image_avant }}">
                            </div>
                        @endif
                        @error('image_avant')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- ----------------   Upload d’images multiples (mediaimage)   ---------------- --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            Images du produit
                            <span class="fw-normal">(la première image ajoutée sera la miniature du produit)</span>
                        </label>

                        <div id="dropzone-multi" class="dropzone needsclick border rounded p-4">
                            <input type="file" id="file-input-covert" name="mediaimage[]" class="d-none" accept="image/*"
                                multiple>

                            {{-- images déjà enregistrées (JSON) --}}
                            <input type="hidden" id="file-input-covert-old" name="old_media_images"
                                value="{{ json_encode($product->images) }}">

                            <div class="dz-message needsclick text-center">
                                Déposez vos images ici ou cliquez pour téléverser<br>
                                <small class="text-muted">(Formats acceptés : JPG/PNG · 5 Mo max)</small>
                            </div>
                        </div>

                        @error('mediaimage.*')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- ----------------   Nom   ---------------- --}}
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="name" class="form-label fw-bold">
                                Nom du produit <span class="text-danger">*</span>
                            </label>

                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="ti ti-package"></i></span>
                                <input id="name" name="name" class="form-control" type="text"
                                    value="{{ old('name', $product->name) }}" required>
                                <div class="invalid-feedback">Veuillez entrer un nom de produit.</div>
                            </div>
                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label fw-bold" for="category_ids">
                                Catégories <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-merge">
                                <select name="category_ids[]" id="category_ids" class="select2 form-select"
                                    multiple="multiple" data-placeholder="Sélectionnez au moins deux catégories" required
                                    style="width: 100%;">
                                    @php
                                        $selected = $product->category_ids ?? [];
                                    @endphp
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ in_array($category->id, $selected) ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">
                                    Vous devez choisir au moins deux catégories.
                                </div>
                            </div>
                            @error('category_ids')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- ----------------   Prix & Stock   ---------------- --}}
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="price" class="form-label fw-bold">
                                Prix <span class="text-danger">*</span>
                            </label>

                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="ti ti-moneybag"></i></span>
                                <input id="price" name="price" class="form-control" type="number" step="0.01"
                                    value="{{ old('price', $product->price) }}" required>
                                <div class="invalid-feedback">Veuillez entrer un prix valide.</div>
                            </div>
                            @error('price')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="stock" class="form-label fw-bold">
                                Stock <span class="text-danger">*</span>
                            </label>

                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="ti ti-box"></i></span>
                                <input id="stock" name="stock" class="form-control" type="number"
                                    value="{{ old('stock', $product->stock) }}" required>
                                <div class="invalid-feedback">Veuillez entrer une quantité valide.</div>
                            </div>
                            @error('stock')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- ----------------   Description   ---------------- --}}
                    <div class="mb-4">
                        <label for="description" class="form-label fw-bold">
                            Description <span class="text-danger">*</span>
                        </label>
                        <textarea id="description" name="description" class="form-control" rows="6" required>{{ old('description', $product->description) }}</textarea>
                        <div class="invalid-feedback">Veuillez entrer une description.</div>
                        @error('description')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- ----------------   SEO   ---------------- --}}
                    <div class="mb-4">
                        <h5 class="fw-bold">Optimisation SEO</h5>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="meta_title" class="form-label">Titre SEO</label>
                                <input id="meta_title" name="meta_title" class="form-control" type="text"
                                    value="{{ old('meta_title', $product->meta_title) }}">
                                @error('meta_title')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="meta_keywords" class="form-label">Mots-clés SEO</label>
                                <input id="meta_keywords" name="meta_keywords" class="form-control" type="text"
                                    value="{{ old('meta_keywords', $product->meta_keywords) }}">
                                @error('meta_keywords')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="meta_description" class="form-label">Description SEO</label>
                            <textarea id="meta_description" name="meta_description" class="form-control" rows="3">{{ old('meta_description', $product->meta_description) }}</textarea>
                            @error('meta_description')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- ----------------   Statut   ---------------- --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Statut</label>
                        <div class="form-check">
                            <input id="is_active" name="is_active" class="form-check-input" type="checkbox"
                                value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                            <label for="is_active" class="form-check-label">
                                Produit actif (visible sur le site)
                            </label>
                        </div>
                    </div>

                    {{-- ----------------   Actions   ---------------- --}}
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('produits.index') }}" class="btn btn-secondary">Annuler</a>
                        <button type="submit" class="btn btn-primary" id="boutiqueForm">Mettre à jour</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

{{-- --------------------------------------------------------------------
|  Scripts spécifiques
--------------------------------------------------------------------- --}}
@section('js')
    <script src="{{ asset('assets/vendor/libs/dropzone/dropzone.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>

    <script>
        (function() {
            'use strict';

            // Dropzone Preview Template
            const previewTemplate = `
            <div class="dz-preview dz-file-preview">
              <div class="dz-details">
                <div class="dz-thumbnail">
                  <img data-dz-thumbnail>
                  <span class="dz-nopreview">No preview available</span>
                  <div class="dz-success-mark"></div>
                  <div class="dz-error-mark"></div>
                  <div class="dz-error-message"><span data-dz-errormessage></span></div>
                  <div class="progress">
                    <div class="progress-bar progress-bar-primary"
                         role="progressbar"
                         aria-valuemin="0"
                         aria-valuemax="100"
                         data-dz-uploadprogress></div>
                  </div>
                </div>
                <div class="dz-filename" data-dz-name></div>
                <div class="dz-size" data-dz-size></div>
              </div>
            </div>`;

            const dropzoneMulti = document.querySelector('#dropzone-multi');

            if (dropzoneMulti) {
                let mediaImagesRaw = @json($product->images);
                console.log('mediaImages (raw):', mediaImagesRaw);
                console.log('Type of mediaImages:', typeof mediaImagesRaw);
                let mediaImagesArray;
                if (typeof mediaImagesRaw === 'string') {
                    try {
                        mediaImagesArray = JSON.parse(mediaImagesRaw);
                    } catch (err) {
                        console.error('Failed to parse JSON:', err);
                        mediaImagesArray = [];
                    }
                } else {
                    mediaImagesArray = mediaImagesRaw;
                }

                if (Array.isArray(mediaImagesArray) && mediaImagesArray.length > 0) {
                    mediaImagesArray = mediaImagesArray.filter(function(item) {
                        return (typeof item === 'string' && item.trim() !== '');
                    });
                } else {
                    console.error('mediaImages is not a valid array or is empty:', mediaImagesArray);
                    mediaImagesArray = [];
                }

                const myDropzoneMulti = new Dropzone(dropzoneMulti, {
                    url: "#",
                    paramName: 'mediaimage[]', // Match form field name
                    maxFilesize: 5, // MB
                    parallelUploads: 1,
                    addRemoveLinks: true,
                    acceptedFiles: 'image/*',
                    previewTemplate: previewTemplate,
                    dictRemoveFile: "Supprimer",

                    init: function() {
                        const dz = this;

                        // If we have valid items, emit them
                        mediaImagesArray.forEach(function(image) {
                            const mockFile = {
                                name: image,
                                size: 236598,
                                type: 'image/*',
                                accepted: true
                            };
                            dz.emit("addedfile", mockFile);
                            dz.emit("thumbnail", mockFile, "{{ asset('storage/') }}/" + image);
                            dz.emit("complete", mockFile);
                            dz.files.push(mockFile);
                        });

                        // On success
                        dz.on("success", function(file, response) {
                            console.log("File uploaded successfully:", response);
                        });

                        // On remove
                        dz.on("removedfile", function(file) {
                            console.log("File removed:", file);
                            // Update old_media_images
                            mediaImagesArray = mediaImagesArray.filter(img => img !== file.name);
                            document.getElementById('file-input-covert-old').value = JSON.stringify(mediaImagesArray);
                        });

                        // Form submission
                        document.querySelector("#boutiqueForm").addEventListener("click", function(e) {
                            e.preventDefault(); // Prevent default button behavior
                            handleFormSubmission(dz);
                        });
                    }
                });

                // Handle form + files
                function handleFormSubmission(dz) {
                    dz.processQueue();
                    submitFormWithFiles(dz.getAcceptedFiles());
                }

                function fileListFrom(files) {
                    const dataTransfer = new DataTransfer();
                    const oldImages = [];
                    files.forEach(file => {
                        if (file instanceof File) {
                            dataTransfer.items.add(file);
                        } else {
                            oldImages.push(file.name);
                        }
                    });
                    console.log("file-input-covert-old", oldImages);
                    document.getElementById("file-input-covert-old").value = JSON.stringify(oldImages);
                    return dataTransfer.files;
                }

                function submitFormWithFiles(files) {
                    const fileListCovert = fileListFrom(files);
                    const fileInputCovert = document.getElementById('file-input-covert');
                    fileInputCovert.files = fileListCovert;
                    document.getElementById('productForm').submit(); // Submit the form
                }
            }

            // Initialize Select2
            $('.select2').select2({
                placeholder: function() {
                    return $(this).data('placeholder');
                },
                allowClear: true,
                width: 'resolve'
            });

            // Form validation for category_ids
            $('#productForm').on('submit', function(e) {
                let $select = $('#category_ids');
                let values = $select.val() || [];
                if (values.length < 2) {
                    e.preventDefault();
                    $select.addClass('is-invalid');
                }
            });

            $('#category_ids').on('change', function() {
                if (($(this).val() || []).length >= 2) {
                    $(this).removeClass('is-invalid');
                }
            });
        })();
    </script>
@endsection
