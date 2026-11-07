@extends('admin.layouts.app')
{{-- --------------------------------------------------------------------
|  Feuilles de style spécifiques
--------------------------------------------------------------------- --}}
@section('css')
    {{-- Quill : chargé uniquement sur les écrans d'édition produit --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/typography.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/editor.css') }}" />
    <style>
        #description-editor { min-height: 260px; }
        #description-editor .ql-editor { min-height: 260px; font-size: .95rem; }
        /* La toolbar reste utilisable sur petit écran. */
        .ql-toolbar.ql-snow { flex-wrap: wrap; }
        @media (max-width: 575.98px) {
            .ql-toolbar.ql-snow .ql-formats { margin-right: .5rem; }
        }
    </style>
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

                        @include('admin.partials.product-slug', ['product' => $product])

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

                                        @if ($category->activeChildren->isNotEmpty())
                                            <optgroup label="&nbsp;&nbsp;↳ {{ $category->name }}">
                                                @foreach ($category->activeChildren as $child)
                                                    <option value="{{ $child->id }}"
                                                        {{ in_array($child->id, $selected) ? 'selected' : '' }}>
                                                        {{ $child->name }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endif
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
                            <label for="sku" class="form-label fw-bold">Référence (SKU)</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="ti ti-barcode"></i></span>
                                <input id="sku" name="sku" class="form-control" type="text"
                                    value="{{ old('sku', $product->sku) }}"
                                    placeholder="Référence interne, ex. BOUGIE001">
                            </div>
                            <small class="text-muted">Facultatif, mais doit rester unique s'il est renseigné.</small>
                            @error('sku')
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
                    <!-- Description courte -->
                    <div class="mb-4">
                        <label class="form-label fw-bold" for="description_courte">Description courte</label>
                        <textarea name="description_courte" id="description_courte" class="form-control js-short-description"
                            rows="3" maxlength="600"
                            placeholder="Ex. : Bougie artisanale aux notes d'eucalyptus et de menthe, 50 h de combustion.">{{ old('description_courte', $product->description_courte) }}</textarea>
                        <div class="d-flex justify-content-between align-items-start mt-1">
                            <small class="text-muted">
                                Résumé court du produit, affiché près du prix et du bouton d'achat.
                                1 à 4 phrases, idéalement 150 à 300 caractères.
                            </small>
                            <small class="text-muted text-nowrap ms-3"><span class="js-short-count">0</span>/600</small>
                        </div>
                        @error('description_courte')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Description longue (éditeur enrichi) -->
                    @php
                        // Sanitisé avant injection : old() contient la saisie brute
                        // renvoyée après une erreur de validation.
                        $descriptionHtml = \App\Support\RichText::toHtml(
                            old('description', $product->editor_description)
                        );
                    @endphp
                    <div class="mb-4">
                        <label class="form-label fw-bold" for="description-editor">
                            Description longue <span class="text-danger">*</span>
                        </label>

                        <div id="description-editor" class="js-quill-editor bg-white">{!! $descriptionHtml !!}</div>

                        {{-- Valeur réellement postée, tenue à jour par Quill --}}
                        <textarea name="description" id="description" hidden aria-hidden="true">{{ $descriptionHtml }}</textarea>

                        <div id="description-error" class="text-danger mt-1 d-none">Veuillez entrer une description.</div>

                        <small class="text-muted d-block mt-1">
                            Description longue : présentez le produit, ses caractéristiques, matières, dimensions,
                            utilisation, entretien et marque. Utilisez des titres et des listes pour faciliter la lecture.
                            <strong>N'inventez aucune information.</strong><br>
                            Conseil SEO : rédigez un contenu unique, naturel et utile. Évitez le bourrage de mots-clés.
                        </small>

                        @error('description')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    @include('admin.partials.variant-editor', ['variants' => $product->variants])

                    @include('admin.partials.faq-editor', ['faqs' => $product->faqs, 'uid' => 'product-faq'])

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
    @include('admin.partials.faq-editor-js')
    @include('admin.partials.variant-editor-js')

    {{-- Quill 1.3.7, fourni avec le thème admin : aucune dépendance npm ajoutée --}}
    <script src="{{ asset('assets/vendor/libs/quill/quill.js') }}"></script>
    <script>
        (function () {
            var container = document.getElementById('description-editor');
            var field = document.getElementById('description');

            if (!container || !field || typeof Quill === 'undefined') {
                return;
            }

            var quill = new Quill(container, {
                theme: 'snow',
                placeholder: 'Présentez le produit : caractéristiques, matières, dimensions, utilisation…',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ header: 2 }, { header: 3 }],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        ['link'],
                        ['clean']
                    ]
                },
                formats: ['bold', 'italic', 'underline', 'strike', 'header', 'list', 'link']
            });

            // Synchronisation continue : le formulaire est soumis par du JS
            // (Dropzone appelle form.submit(), qui ne déclenche pas 'submit').
            var sync = function () {
                field.value = quill.getText().trim() === '' ? '' : quill.root.innerHTML;
            };

            quill.on('text-change', sync);
            sync();

            var form = field.closest('form');
            var error = document.getElementById('description-error');

            if (form) {
                form.addEventListener('submit', function (event) {
                    sync();

                    if (field.value === '') {
                        event.preventDefault();
                        event.stopImmediatePropagation();

                        if (error) {
                            error.classList.remove('d-none');
                        }

                        container.scrollIntoView({ behavior: 'smooth', block: 'center' });

                        return;
                    }

                    if (error) {
                        error.classList.add('d-none');
                    }
                }, true); // capture : passe avant le handler Dropzone
            }
        })();
    </script>
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

{{-- Compteur de caractères de la description courte --}}
<script>
    document.querySelectorAll('.js-short-description').forEach(function (field) {
        var counter = field.closest('.mb-4').querySelector('.js-short-count');

        if (!counter) {
            return;
        }

        var sync = function () {
            counter.textContent = field.value.length;
            // 150-300 caractères : plage recommandée, signalée sans bloquer la saisie.
            counter.parentElement.classList.toggle('text-success', field.value.length >= 150 && field.value.length <= 300);
        };

        field.addEventListener('input', sync);
        sync();
    });
</script>
