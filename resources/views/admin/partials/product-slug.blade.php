{{--
    Slug (adresse) du produit. $product : App\Models\Product, absent à la création.
--}}
@php $currentSlug = isset($product) ? $product->slug : null; @endphp

<div class="mb-3 col-md-6">
    <label class="form-label fw-bold" for="slug">Slug (adresse de la page)</label>

    <div class="input-group">
        <span class="input-group-text">{{ url('/article') }}/</span>
        <input id="slug" name="slug" class="form-control js-product-slug" type="text" maxlength="255"
               value="{{ old('slug', $currentSlug) }}" placeholder="genere-depuis-le-nom"
               data-auto="{{ $currentSlug ? '0' : '1' }}">
    </div>

    <small class="text-muted d-block mt-1">
        @if ($currentSlug)
            Modifier le slug change l'adresse de la page : l'ancienne adresse redirige
            automatiquement vers la nouvelle. Ne le changez que si nécessaire.
        @else
            Laissez vide pour le générer depuis le nom. Lettres minuscules, chiffres et tirets.
        @endif
    </small>

    @error('slug')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

<script>
    (function () {
        var slugField = document.querySelector('.js-product-slug');
        var nameField = document.getElementById('name');

        if (!slugField || !nameField) {
            return;
        }

        // Même règle que Str::slug côté serveur, qui reste la référence.
        var slugify = function (value) {
            return value.normalize('NFD').replace(/[̀-ͯ]/g, '')
                .toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
        };

        // À la création, le slug suit le nom tant que l'admin ne l'a pas saisi.
        nameField.addEventListener('input', function () {
            if (slugField.dataset.auto === '1') {
                slugField.value = slugify(nameField.value);
            }
        });

        slugField.addEventListener('input', function () {
            slugField.dataset.auto = slugField.value === '' ? '1' : '0';
        });

        slugField.addEventListener('change', function () {
            slugField.value = slugify(slugField.value);
        });
    })();
</script>
