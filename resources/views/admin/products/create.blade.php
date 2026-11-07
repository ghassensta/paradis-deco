@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card mb-4 shadow-sm">
            <div class="card-header text-white">
                <h4 class="mb-0">{{ isset($product) ? 'Modifier le produit' : 'Ajouter un produit' }}</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('produits.store') }}" method="POST" class="needs-validation" id="productForm"
                    enctype="multipart/form-data" novalidate>
                    @csrf
                    @if (isset($product))
                        @method('PUT')
                    @endif

                    <!-- Cover Image Upload -->
                    <div class="mb-4">
                        <label class="form-label fw-bold" for="image_avant">
                            Image Avant
                            <span class="fw-normal">(image avant de produit)</span>
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="file" name="image_avant" id="image_avant" class="form-control"
                                accept="image/*" {{ isset($product) ? '' : 'required' }}>
                            <div class="invalid-feedback">Veuillez sélectionner une image de couverture.</div>
                        </div>
                        @if (isset($product) && $product->image_avant)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $product->image_avant) }}" loading="lazy" alt="Cover Image"
                                    style="max-width: 150px; height: auto;">
                                <input type="hidden" name="old_image_avant"
                                    value="{{ $product->image_avant }}">
                            </div>
                        @endif
                        @error('image_avant')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Upload des images multiples (Dropzone) -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            Images du produit
                            <span class="fw-normal">(la première image ajoutée sera la miniature du produit)</span>
                        </label>
                        <div class="dropzone needsclick border rounded p-4" id="dropzone-multi">
                            <input type="file" multiple name="mediaimage[]" id="file-input-covert" class="d-none"
                                accept="image/*">
                            <input type="hidden" name="old_media_images" id="file-input-covert-old"
                                value="{{ old('old_media_images', isset($product) ? json_encode($product->images) : '[]') }}">
                            <div class="dz-message needsclick text-center">
                                Déposez vos images ici ou cliquez pour les téléverser<br>
                                <small class="text-muted">(Formats acceptés : JPG, PNG. Max 5 Mo par image)</small>
                            </div>
                        </div>
                        @error('mediaimage.*')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <!-- Nom du produit -->
                        <div class="mb-3 col-md-6">
                            <label class="form-label fw-bold" for="name">Nom du produit <span
                                    class="text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="ti ti-package"></i></span>
                                <input type="text" name="name" class="form-control" id="name"
                                    value="{{ old('name', isset($product) ? $product->name : '') }}"
                                    placeholder="Nom du produit" required>
                                <div class="invalid-feedback">Veuillez entrer un nom de produit.</div>
                            </div>
                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        @include('admin.partials.product-slug')
                        <div class="mb-3 col-md-6">
                            <label class="form-label fw-bold" for="category_ids">
                                Catégories <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-merge">
                                <select name="category_ids[]" id="category_ids" class="select2 form-select"
                                    multiple="multiple" data-placeholder="Sélectionnez au moins deux catégories" required
                                    style="width: 100%;">
                                    @php
                                        $selected = old('category_ids', []);
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

                    <div class="row">
                        <!-- Prix -->
                        <div class="mb-3 col-md-6">
                            <label class="form-label fw-bold" for="price">Prix <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="ti ti-moneybag"></i></span>
                                <input type="number" name="price" class="form-control" id="price" step="0.01"
                                    value="{{ old('price', isset($product) ? $product->price : '') }}" placeholder="0.00"
                                    required>
                                <div class="invalid-feedback">Veuillez entrer un prix valide.</div>
                            </div>
                            @error('price')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>


                        <!-- SKU -->
                        <div class="mb-3 col-md-6">
                            <label class="form-label fw-bold" for="sku">Référence (SKU)</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="ti ti-barcode"></i></span>
                                <input type="text" name="sku" class="form-control" id="sku"
                                    value="{{ old('sku', isset($product) ? $product->sku : '') }}"
                                    placeholder="Référence interne, ex. BOUGIE001">
                            </div>
                            <small class="text-muted">Facultatif, mais doit rester unique s'il est renseigné.</small>
                            @error('sku')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Stock -->
                        <div class="mb-3 col-md-6">
                            <label class="form-label fw-bold" for="stock">Stock <span
                                    class="text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="ti ti-box"></i></span>
                                <input type="number" name="stock" class="form-control" id="stock"
                                    value="{{ old('stock', isset($product) ? $product->stock : '') }}"
                                    placeholder="Quantité en stock" required>
                                <div class="invalid-feedback">Veuillez entrer une quantité valide.</div>
                            </div>
                            @error('stock')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Description courte -->
                    <div class="mb-4">
                        <label class="form-label fw-bold" for="description_courte">Description courte</label>
                        <textarea name="description_courte" id="description_courte" class="form-control js-short-description"
                            rows="3" maxlength="600"
                            placeholder="Ex. : Bougie artisanale aux notes d'eucalyptus et de menthe, 50 h de combustion.">{{ old('description_courte', isset($product) ? $product->description_courte : '') }}</textarea>
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
                            old('description', isset($product) ? $product->editor_description : '')
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

                    @include('admin.partials.variant-editor', ['variants' => collect()])

                    @include('admin.partials.faq-editor', ['faqs' => collect(), 'uid' => 'product-faq'])

                    <!-- SEO Fields -->
                    <div class="mb-4">
                        <h5 class="fw-bold">Optimisation SEO</h5>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label class="form-label" for="meta_title">Titre SEO</label>
                                <input type="text" name="meta_title" class="form-control" id="meta_title"
                                    value="{{ old('meta_title', isset($product) ? $product->meta_title : '') }}"
                                    placeholder="Titre pour les moteurs de recherche">
                                @error('meta_title')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label" for="meta_keywords">Mots-clés SEO</label>
                                <input type="text" name="meta_keywords" class="form-control" id="meta_keywords"
                                    value="{{ old('meta_keywords', isset($product) ? $product->meta_keywords : '') }}"
                                    placeholder="Mots-clés séparés par des virgules">
                                @error('meta_keywords')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="meta_description">Description SEO</label>
                            <textarea name="meta_description" id="meta_description" class="form-control" rows="3"
                                placeholder="Description pour les moteurs de recherche">{{ old('meta_description', isset($product) ? $product->meta_description : '') }}</textarea>
                            @error('meta_description')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Statut -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Statut</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                value="1"
                                {{ old('is_active', isset($product) ? $product->is_active : true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Produit actif (visible sur le site)</label>
                        </div>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="" class="btn btn-secondary">Annuler</a>
                        <button type="submit" class="btn btn-primary" id="boutiqueForm">Enregistrer le produit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

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
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/dropzone/dropzone.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
@endsection

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
        (() => {
            'use strict';

            /* -----------------------------------------------------------------
             *  Variables et helpers
             * ----------------------------------------------------------------- */
            const dropzoneElement = document.getElementById('dropzone-multi');
            const form = document.getElementById('productForm');
            const hiddenOldInput = document.getElementById('file-input-covert-old');
            const hiddenFileInput = document.getElementById('file-input-covert');
            const coverImageInput = document.getElementById('image_avant');

            /* Convertit un tableau de File en FileList */
            const toFileList = (files) => {
                const dt = new DataTransfer();
                files.forEach(f => dt.items.add(f));
                return dt.files;
            };

            /* -----------------------------------------------------------------
             *  Initialisation Dropzone
             * ----------------------------------------------------------------- */
            if (dropzoneElement) {
                /** images existantes (édit) : array de noms */
                let existing = @json(isset($product) ? $product->images ?? [] : []);

                if (!Array.isArray(existing)) existing = [];
                /* filtre sécurité */
                existing = existing.filter(img => typeof img === 'string' && img.trim() !== '');

                const dz = new Dropzone(dropzoneElement, {
                    url: '#', // pas utilisé, on soumet le <form>
                    uploadMultiple: false,
                    paramName: 'mediaimage[]',
                    maxFilesize: 5, // MB
                    acceptedFiles: 'image/*',
                    addRemoveLinks: true,
                    parallelUploads: 10,
                    previewTemplate: `
                        <div class="dz-preview dz-file-preview">
                            <div class="dz-details">
                                <div class="dz-thumbnail">
                                    <img data-dz-thumbnail />
                                    <span class="dz-nopreview">No preview</span>
                                    <div class="dz-success-mark"></div>
                                    <div class="dz-error-mark"></div>
                                    <div class="dz-error-message"><span data-dz-errormessage></span></div>
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-primary" role="progressbar"
                                             aria-valuemin="0" aria-valuemax="100" data-dz-uploadprogress></div>
                                    </div>
                                </div>
                                <div class="dz-filename" data-dz-name></div>
                                <div class="dz-size" data-dz-size></div>
                            </div>
                        </div>`,
                    dictRemoveFile: 'Supprimer',
                    init() {
                        /* ---- 1. Injecter les images existantes ---------------- */
                        existing.forEach(name => {
                            const mock = {
                                name,
                                size: 123456,
                                accepted: true
                            }; // taille fictive
                            this.emit('addedfile', mock);
                            this.emit('thumbnail', mock, "{{ asset('storage') }}/" + name);
                            this.emit('complete', mock);
                            this.files.push(mock);
                        });

                        /* ---- 2. Retirer un fichier pré-existant --------------- */
                        this.on('removedfile', (file) => {
                            /* si l’image retirée était déjà sur le serveur, on l’enlève de la liste old */
                            existing = existing.filter(n => n !== file.name);
                        });

                        /* ---- 3. Soumission du formulaire ---------------------- */
                        form.addEventListener('submit', (e) => {
                            e.preventDefault();

                            /* a) on prépare old_media_images (restants) */
                            hiddenOldInput.value = JSON.stringify(existing);

                            /* b) on attribue les nouveaux fichiers à un input caché */
                            const newFiles = this.getAcceptedFiles().filter(f => f instanceof File);
                            hiddenFileInput.files = toFileList(newFiles);

                            /* c) on soumet le formulaire natif */
                            form.submit();
                        });
                    }
                });
            }

            /* -----------------------------------------------------------------
             *  Validation du formulaire
             * ----------------------------------------------------------------- */
            // Initialise tous les selects .select2
            $('.select2').select2({
                placeholder: function() {
                    return $(this).data('placeholder');
                },
                allowClear: true,
                width: 'resolve'
            });

            // Validation à la soumission
            $('#productForm').on('submit', function(e) {
                let $select = $('#category_ids');
                let values = $select.val() || [];
                if (values.length < 2) {
                    e.preventDefault();
                    // Affiche l’erreur Bootstrap
                    $select.addClass('is-invalid');
                }
            });

            // Supprime l’erreur dès qu’on atteint 2 sélections
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
