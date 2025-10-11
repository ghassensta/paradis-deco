<form id="editAvisForm" action="{{ route('avis.update', $avis->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label for="product_id" class="form-label">Produit</label>
        <select name="product_id" id="product_id" class="form-select select2" required>
            @foreach ($products as $product)
                <option value="{{ $product->id }}" {{ $avis->product_id == $product->id ? 'selected' : '' }}>
                    {{ $product->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label for="rating" class="form-label">Note</label>
        <select name="rating" id="rating" class="form-select" required>
            @for ($i = 1; $i <= 5; $i++)
                <option value="{{ $i }}" {{ $avis->rating == $i ? 'selected' : '' }}>{{ $i }} étoile{{ $i > 1 ? 's' : '' }}</option>
            @endfor
        </select>
    </div>
    <div class="mb-3">
        <label for="comment" class="form-label">Commentaire</label>
        <textarea name="comment" id="comment" class="form-control" rows="4" required>{{ $avis->comment }}</textarea>
    </div>
    <div class="mb-3">
        <label for="name" class="form-label">Nom du client</label>
        <input type="text" name="name" id="name" class="form-control" value="{{ $avis->name }}" required>
    </div>
    <div class="mb-3">
        <label for="approved" class="form-label">Statut</label>
        <select name="approved" id="approved" class="form-select">
            <option value="1" {{ $avis->approved ? 'selected' : '' }}>Approuvé</option>
            <option value="0" {{ !$avis->approved ? 'selected' : '' }}>En attente</option>
        </select>
    </div>
    <div id="editValidationErrors" class="alert alert-danger" style="display:none;"></div>
    <button type="submit" class="btn btn-primary">Enregistrer</button>
</form>

<script>
    $(function() {
        $('#editAvisForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
                type: "POST",
                dataType: "json",
                data: $(this).serialize(),
                success: function(response) {
                    $('#editAvisModal').modal('hide');
                    $('.datatables-avis').DataTable().ajax.reload();
                    Swal.fire({
                        icon: 'success',
                        title: 'Succès',
                        text: response.message,
                        customClass: { confirmButton: 'btn btn-success' }
                    });
                },
                error: function(xhr) {
                    $('#editValidationErrors').empty().show();
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        $.each(xhr.responseJSON.errors, function(key, value) {
                            $('#editValidationErrors').append('<div>' + value + '</div>');
                        });
                    } else {
                        $('#editValidationErrors').append('<div>Une erreur est survenue.</div>');
                    }
                }
            });
        });
    });
</script>
