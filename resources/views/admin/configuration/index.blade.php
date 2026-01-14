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
