@extends('admin.layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="mb-4">Ajouter une Inspiration</h4>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if (session('message'))
                            <div class="alert alert-success">
                                {{ session('message') }}
                            </div>
                        @endif
                        <form method="POST" id="form-log" action="{{ route('inspirations.store') }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="image" class="form-label">Image</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            </div>
                            <div class="mb-3">
                                <label for="title" class="form-label">Titre</label>
                                <input type="text" class="form-control" id="title" name="title"
                                    value="{{ old('title') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="resume" class="form-label">Résumé</label>
                                <textarea class="form-control" id="resume" name="resume">{{ old('resume') }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label for="description-editor" class="form-label">Description</label>
                                @php
                                    $descriptionHtml = \App\Support\RichText::toHtml(old('description', ''));
                                @endphp
                                <div id="description-editor" class="bg-white">{!! $descriptionHtml !!}</div>
                                {{-- Valeur réellement postée, tenue à jour par Quill --}}
                                <textarea name="description" id="description" hidden aria-hidden="true">{{ $descriptionHtml }}</textarea>
                                <small class="text-muted d-block mt-1">
                                    Présentez l'ambiance, les matières et les pièces utilisées.
                                    Utilisez des titres et des listes pour aérer la lecture.
                                    <strong>N'inventez aucune information.</strong>
                                </small>
                            </div>
                            <div class="mb-3">
                                <label for="meta_title" class="form-label">Meta Titre</label>
                                <input type="text" class="form-control" id="meta_title" name="meta_title"
                                    value="{{ old('meta_title') }}">
                            </div>
                            <div class="mb-3">
                                <label for="meta_description" class="form-label">Meta Description</label>
                                <textarea class="form-control" id="meta_description" name="meta_description">{{ old('meta_description') }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="is_active" class="form-label">Statut</label>
                                <select class="form-select" id="is_active" name="is_active">
                                    <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>Actif</option>
                                    <option value="0" {{ old('is_active', 1) == 0 ? 'selected' : '' }}>Inactif</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary" id="submit-btn">Créer</button>
                                <a href="{{ route('inspirations.index') }}" class="btn btn-label-secondary">Annuler</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/typography.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/katex.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/editor.css') }}" />
@endsection

@section('js')
    <script src="{{ asset('assets/vendor/libs/quill/quill.js') }}"></script>
    <script>
        (function () {
            var container = document.getElementById('description-editor');
            var field = document.getElementById('description');

            if (!container || !field || typeof Quill === 'undefined') {
                return;
            }

            // Même configuration que les fiches produits et catégories.
            var quill = new Quill(container, {
                theme: 'snow',
                placeholder: "Décrivez l'ambiance, les matières, les pièces…",
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

            // Synchronisation continue : quill.root.innerHTML et non le
            // conteneur entier, qui embarquerait la barre d'outils.
            var sync = function () {
                field.value = quill.getText().trim() === '' ? '' : quill.root.innerHTML;
            };

            quill.on('text-change', sync);
            sync();

            var form = field.closest('form');

            if (form) {
                form.addEventListener('submit', sync, true);
            }
        })();
    </script>

    <script src="{{ asset('assets/vendor/libs/quill/katex.js') }}"></script>
@endsection
