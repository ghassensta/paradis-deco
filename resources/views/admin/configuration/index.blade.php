@extends('admin.layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Paramètres de Configuration</h4>
        </div>
        <div class="row g-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <form action="{{ route('configurations.update',$settings->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            @if (session('message'))
                                <div class="alert alert-success alert-dismissible" role="alert">
                                    {{ session('message') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger" role="alert">
                                    <strong>Vérifiez les champs suivants :</strong>
                                    <ul class="mb-0 mt-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <ul class="nav nav-pills flex-column flex-md-row mb-4" role="tablist">
                                @foreach ([
                                    'general' => 'Général',
                                    'contact' => 'Contact',
                                    'social' => 'Réseaux sociaux',
                                    'tracking' => 'Tracking',
                                ] as $tab => $label)
                                    <li class="nav-item">
                                        <button type="button" class="nav-link {{ $loop->first ? 'active' : '' }}"
                                                data-bs-toggle="tab" data-bs-target="#tab-{{ $tab }}"
                                                role="tab" aria-controls="tab-{{ $tab }}"
                                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">{{ $label }}</button>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="tab-content p-0">
                            <div class="tab-pane fade show active" id="tab-general" role="tabpanel">

                            <!-- Site Name -->
                            <div class="mb-3">
                                <label for="site_name" class="form-label fw-semibold">Nom du site</label>
                                <input type="text" class="form-control" id="site_name" name="site_name" value="{{ old('site_name', $settings->site_name ?? null) }}" required>
                                @error('site_name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Site Logo -->
                            <div class="mb-3">
                                <label for="site_logo" class="form-label fw-semibold">Logo du site</label>
                                <input type="file" class="form-control" id="site_logo" name="site_logo" accept="image/*">
                                @if($settings->site_logo ?? null)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $settings->site_logo) }}" loading="lazy" alt="Logo" style="max-width: 150px;">
                                    </div>
                                @endif
                                @error('site_logo')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Support Email -->
                            <div class="mb-3">
                                <label for="support_email" class="form-label fw-semibold">Email de support</label>
                                <input type="email" class="form-control" id="support_email" name="support_email" value="{{ old('support_email', $settings->support_email ?? null) }}" required>
                                @error('support_email')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Default Language -->
                            <div class="mb-3">
                                <label for="default_language" class="form-label fw-semibold">Langue par défaut</label>
                                <select class="form-select" id="default_language" name="default_language" required>
                                    <option value="fr" {{ old('default_language', $settings->default_language ?? null) == 'fr' ? 'selected' : '' }}>Français</option>
                                    <option value="en" {{ old('default_language', $settings->default_language ?? null) == 'en' ? 'selected' : '' }}>Anglais</option>
                                    <!-- Add more languages as needed -->
                                </select>
                                @error('default_language')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Currency -->
                            <div class="mb-3">
                                <label for="currency" class="form-label fw-semibold">Devise</label>
                                <select class="form-select" id="currency" name="currency" required>
                                    <option value="EUR" {{ old('currency', $settings->currency ?? null) == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                                    <option value="USD" {{ old('currency', $settings->currency ?? null) == 'USD' ? 'selected' : '' }}>USD ($)</option>
                                    <!-- Add more currencies as needed -->
                                </select>
                                @error('currency')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Meta Title -->
                            <div class="mb-3">
                                <label for="meta_title" class="form-label fw-semibold">Titre Meta</label>
                                <input type="text" class="form-control" id="meta_title" name="meta_title" value="{{ old('meta_title', $settings->meta_title ?? null) }}">
                                @error('meta_title')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Meta Description -->
                            <div class="mb-3">
                                <label for="meta_description" class="form-label fw-semibold">Description Meta</label>
                                <textarea class="form-control" id="meta_description" name="meta_description" rows="4">{{ old('meta_description', $settings->meta_description ?? null) }}</textarea>
                                @error('meta_description')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Shipping Cost -->
                            <div class="mb-3">
                                <label for="shipping_cost" class="form-label fw-semibold">Frais de livraison</label>
                                <input type="number" step="0.01" class="form-control" id="shipping_cost" name="shipping_cost" value="{{ old('shipping_cost', $settings->shipping_cost ?? null) }}" required>
                                @error('shipping_cost')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Free Shipping Threshold -->
                            <div class="mb-3">
                                <label for="free_shipping_threshold" class="form-label fw-semibold">Seuil de livraison gratuite</label>
                                <input type="number" step="0.01" class="form-control" id="free_shipping_threshold" name="free_shipping_threshold" value="{{ old('free_shipping_threshold', $settings->free_shipping_threshold ?? null) }}">
                                @error('free_shipping_threshold')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Delivery Estimate Days -->
                            <div class="mb-3">
                                <label for="delivery_estimate_days" class="form-label fw-semibold">Estimation des jours de livraison</label>
                                <input type="number" class="form-control" id="delivery_estimate_days" name="delivery_estimate_days" value="{{ old('delivery_estimate_days', $settings->delivery_estimate_days ?? null) }}" required>
                                @error('delivery_estimate_days')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Maintenance Mode -->
                            <div class="mb-3">
                                <label for="maintenance_mode" class="form-label fw-semibold">Mode maintenance</label>
                                <select class="form-select" id="maintenance_mode" name="maintenance_mode" required>
                                    <option value="0" {{ old('maintenance_mode', $settings->maintenance_mode ?? null) == 0 ? 'selected' : '' }}>Désactivé</option>
                                    <option value="1" {{ old('maintenance_mode', $settings->maintenance_mode ?? null) == 1 ? 'selected' : '' }}>Activé</option>
                                </select>
                                @error('maintenance_mode')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Homepage Banner -->
                            <div class="mb-3">
                                <label for="homepage_banner" class="form-label fw-semibold">Bannière de la page d'accueil</label>
                                <input type="file" class="form-control" id="homepage_banner" name="homepage_banner" accept="image/*">
                                @if($settings->homepage_banner ?? null)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $settings->homepage_banner) }}" loading="lazy" alt="Banner" style="max-width: 150px;">
                                    </div>
                                @endif
                                @error('homepage_banner')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            </div>

                            <div class="tab-pane fade" id="tab-contact" role="tabpanel">
                                <div class="row g-3">
                                    @foreach ([
                                        ['support_phone', 'Téléphone', 'tel', 'Ex. : +216 20 123 456'],
                                        ['address', 'Adresse', 'text', 'Rue, numéro'],
                                        ['city', 'Ville / Gouvernorat', 'text', 'Ex. : Sousse'],
                                        ['working_hours', 'Horaires', 'text', 'Ex. : Lun-Sam 9h-18h'],
                                    ] as [$field, $label, $type, $placeholder])
                                        <div class="col-12 col-md-6">
                                            <label class="form-label" for="{{ $field }}">{{ $label }}</label>
                                            <input type="{{ $type }}" class="form-control" id="{{ $field }}" name="{{ $field }}"
                                                   value="{{ old($field, $settings->$field ?? null) }}" placeholder="{{ $placeholder }}">
                                            <small class="text-muted">Affiché dans le pied de page et la page contact.</small>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="tab-pane fade" id="tab-social" role="tabpanel">
                                <div class="row g-3">
                                    @foreach ([
                                        ['facebook_url', 'Facebook', 'url', 'https://facebook.com/...'],
                                        ['instagram_url', 'Instagram', 'url', 'https://instagram.com/...'],
                                        ['tiktok_url', 'TikTok', 'url', 'https://tiktok.com/@...'],
                                        ['whatsapp_number', 'WhatsApp', 'tel', '+216 20 123 456'],
                                    ] as [$field, $label, $type, $placeholder])
                                        <div class="col-12 col-md-6">
                                            <label class="form-label" for="{{ $field }}">{{ $label }}</label>
                                            <input type="{{ $type }}" class="form-control" id="{{ $field }}" name="{{ $field }}"
                                                   value="{{ old($field, $settings->$field ?? null) }}" placeholder="{{ $placeholder }}">
                                        </div>
                                    @endforeach

                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="og_image">Image de partage (Open Graph)</label>
                                        <input type="file" class="form-control" id="og_image" name="og_image" accept="image/*">
                                        <small class="text-muted">Utilisée lors du partage sur les réseaux. 1200 × 630 recommandé.</small>
                                        @if ($settings->og_image ?? null)
                                            <img src="{{ asset('storage/' . $settings->og_image) }}" alt="Image Open Graph actuelle"
                                                 class="mt-2 rounded" style="max-height: 90px">
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="tab-tracking" role="tabpanel">
                                <div class="alert alert-info" role="alert">
                                    Un traceur n'est chargé que si son identifiant est renseigné <strong>et</strong> la case cochée.
                                    Aucun script n'est injecté dans l'administration.
                                </div>

                                <div class="row g-4">
                                    @foreach ([
                                        ['meta', 'Meta Pixel (Facebook / Instagram)', 'meta_pixel_id', 'meta_pixel_enabled', 'Ex. : 123456789012345'],
                                        ['tiktok', 'TikTok Pixel', 'tiktok_pixel_id', 'tiktok_pixel_enabled', 'Ex. : C4A1B2C3D4E5'],
                                        ['ga4', 'Google Analytics 4', 'ga4_measurement_id', 'ga4_enabled', 'Ex. : G-XXXXXXXXXX'],
                                    ] as [$key, $label, $idField, $flagField, $placeholder])
                                        <div class="col-12 col-lg-4">
                                            <div class="card h-100">
                                                <div class="card-body">
                                                    <h6 class="mb-3">{{ $label }}</h6>

                                                    <label class="form-label" for="{{ $idField }}">Identifiant</label>
                                                    <input type="text" class="form-control" id="{{ $idField }}" name="{{ $idField }}"
                                                           value="{{ old($idField, $settings->$idField ?? null) }}" placeholder="{{ $placeholder }}">

                                                    <div class="form-check mt-3">
                                                        <input type="hidden" name="{{ $flagField }}" value="0">
                                                        <input class="form-check-input" type="checkbox" value="1"
                                                               id="{{ $flagField }}" name="{{ $flagField }}"
                                                               {{ old($flagField, $settings->$flagField ?? false) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="{{ $flagField }}">Activer</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                                <a href="" class="btn btn-secondary">Annuler</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
