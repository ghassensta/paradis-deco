{{--
    Éditeur des variantes d'un produit (type, taille, prix, stock, image).
    $variants : collection de App\Models\ProductVariant (peut être vide)
--}}
@php
    $variants = $variants ?? collect();
    $variantImages = $variants->pluck('image_url', 'id');
    $variantRows = old('variants', $variants->map(fn ($v) => [
        'id' => $v->id,
        'type' => $v->type,
        'size' => $v->size,
        'price' => $v->price,
        'stock' => $v->stock,
        'position' => $v->position,
        'is_active' => $v->is_active ? 1 : 0,
    ])->values()->all());
@endphp

<div class="mb-4 variant-editor">
    <label class="form-label fw-bold d-block">Types et tailles</label>

    <small class="text-muted d-block mb-3">
        Une ligne par combinaison vendue, par exemple « Noir » + « 60 cm ». Toutes les options restent sur
        cette seule fiche produit : le client choisit, et le prix, le stock et l'image changent.
        Renseignez le type, la taille, ou les deux, de la même façon sur chaque ligne.
        <strong>Dès qu'une variante est active, le prix de la fiche devient le prix le plus bas
        et le stock devient le total des variantes.</strong>
    </small>

    <input type="hidden" name="variants_present" value="1">

    <div class="js-variant-rows">
        @foreach ($variantRows as $i => $row)
            @include('admin.partials.variant-row', [
                'index' => $i,
                'row' => $row,
                'imageUrl' => $variantImages[$row['id'] ?? null] ?? null,
            ])
        @endforeach
    </div>

    <button type="button" class="btn btn-sm btn-label-primary js-variant-add">
        <i class="ti ti-plus"></i> Ajouter une variante
    </button>

    @foreach ($errors->get('variants') as $messages)
        <div class="text-danger mt-1">{{ is_array($messages) ? implode(' ', $messages) : $messages }}</div>
    @endforeach

    @foreach ($errors->get('variants.*') as $messages)
        <div class="text-danger mt-1">{{ implode(' ', $messages) }}</div>
    @endforeach

    {{-- Doit rester dans .variant-editor : le script l'y cherche. --}}
    <template class="js-variant-template">
        @include('admin.partials.variant-row', ['index' => '__INDEX__', 'row' => [], 'imageUrl' => null])
    </template>
</div>
