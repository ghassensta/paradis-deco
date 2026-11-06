{{-- Une ligne de l'éditeur de variantes. $index peut valoir __INDEX__ (gabarit). --}}
<div class="card mb-2 variant-row">
    <div class="card-body py-3">
        <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $row['id'] ?? '' }}">
        <input type="hidden" name="variants[{{ $index }}][position]" class="js-variant-position" value="{{ $row['position'] ?? '' }}">

        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label mb-1">Type</label>
                <input type="text" class="form-control" maxlength="100" name="variants[{{ $index }}][type]"
                       value="{{ $row['type'] ?? '' }}" placeholder="Ex. : Noir, Bois">
            </div>

            <div class="col-md-2">
                <label class="form-label mb-1">Taille</label>
                <input type="text" class="form-control" maxlength="100" name="variants[{{ $index }}][size]"
                       value="{{ $row['size'] ?? '' }}" placeholder="Ex. : 60 cm, L">
            </div>

            <div class="col-md-2">
                <label class="form-label mb-1">Prix (DT)</label>
                <input type="number" class="form-control" min="0" step="0.001" name="variants[{{ $index }}][price]"
                       value="{{ $row['price'] ?? '' }}" placeholder="Prix de la fiche">
            </div>

            <div class="col-md-1">
                <label class="form-label mb-1">Stock</label>
                <input type="number" class="form-control" min="0" step="1" name="variants[{{ $index }}][stock]"
                       value="{{ $row['stock'] ?? 0 }}">
            </div>

            <div class="col-md-4">
                <label class="form-label mb-1">Image</label>
                <div class="d-flex align-items-center gap-2">
                    @if ($imageUrl)
                        <img src="{{ $imageUrl }}" alt="" width="38" height="38" class="rounded" style="object-fit: cover">
                    @endif
                    <input type="file" class="form-control" accept="image/*" name="variants[{{ $index }}][image]">
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-between mt-2">
            <div class="form-check">
                <input type="hidden" name="variants[{{ $index }}][is_active]" value="0">
                <input class="form-check-input" type="checkbox" value="1" id="variant-active-{{ $index }}"
                       name="variants[{{ $index }}][is_active]" {{ ($row['is_active'] ?? 1) ? 'checked' : '' }}>
                <label class="form-check-label" for="variant-active-{{ $index }}">Active</label>
            </div>

            <button type="button" class="btn btn-sm btn-label-danger js-variant-remove">
                <i class="ti ti-trash"></i> Supprimer
            </button>
        </div>
    </div>
</div>
