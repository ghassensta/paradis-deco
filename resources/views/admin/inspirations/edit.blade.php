@extends('admin.layouts.app')

@section('title', 'Modifier une Inspiration')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="mb-4">Modifier une Inspiration</h4>
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
                        <form method="POST" action="{{ route('inspirations.update', $inspiration->id) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="image" class="form-label">Image</label>
                                @if ($inspiration->image && Storage::disk('public')->exists($inspiration->image))
                                    <div class="mb-2">
                                        <img src="{{ Storage::url($inspiration->image) }}" alt="{{ $inspiration->title }}" style="max-width: 200px;">
                                    </div>
                                @endif
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            </div>
                            <div class="mb-3">
                                <label for="title" class="form-label">Titre</label>
                                <input type="text" class="form-control" id="title" name="title"
                                       value="{{ old('title', $inspiration->title) }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="resume" class="form-label">Résumé</label>
                                <textarea class="form-control" id="resume" name="resume">{{ old('resume', $inspiration->resume) }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <input type="hidden" name="description" id="editor_content"
                                       value="">
                                <div id="full-editor">
                                     {!! $inspiration->description !!}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="meta_title" class="form-label">Meta Titre</label>
                                <input type="text" class="form-control" id="meta_title" name="meta_title"
                                       value="{{ old('meta_title', $inspiration->meta_title) }}">
                            </div>
                            <div class="mb-3">
                                <label for="meta_description" class="form-label">Meta Description</label>
                                <textarea class="form-control" id="meta_description" name="meta_description">{{ old('meta_description', $inspiration->meta_description) }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label for="is_active" class="form-label">Statut</label>
                                <select class="form-select" id="is_active" name="is_active">
                                    <option value="1" {{ old('is_active', $inspiration->is_active) == 1 ? 'selected' : '' }}>Actif</option>
                                    <option value="0" {{ old('is_active', $inspiration->is_active) == 0 ? 'selected' : '' }}>Inactif</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary" id="submit-btn">Mettre à jour</button>
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
    <script src="{{ asset('assets/vendor/libs/quill/katex.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/quill/quill.js') }}"></script>
    <script src="{{ asset('assets/js/forms-editors.js') }}"></script>
      <script>
        $('#submit-btn').on('click', function() {
            // event.preventDefault();
            var editorContent = $('#full-editor').html();
            $('#editor_content').val(editorContent);
        });
    </script>
@endsection

