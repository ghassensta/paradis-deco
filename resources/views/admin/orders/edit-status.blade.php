{{-- ====== Formulaire : mise à jour du statut ====== --}}
<form id="updateStatusForm"
      action="{{ route('commandes.update-status', $order->id) }}"
      method="POST"
      class="row g-4">

    @csrf
    @method('PUT')

    {{-- ───── Récapitulatif fixe de la commande ───── --}}
    <div class="col-12">
        <div class="alert alert-info d-flex flex-wrap gap-3 align-items-center mb-0">
            <span class="fw-semibold">
                <i class="ti ti-receipt me-1"></i> Commande&nbsp;#{{ $order->numero_commande }}
            </span>
            <span class="vr"></span>
            <span><i class="ti ti-calendar-event me-1"></i> {{ $order->created_at->format('d/m/Y') }}</span>
            <span class="vr"></span>
            <span><i class="ti ti-user me-1"></i> {{ $order->client->name }}</span>
            <span class="vr"></span>
            <span><i class="ti ti-currency-dollar me-1"></i> {{ number_format($order->subtotal_ht + $order->shipping_cost, 2, ',', ' ') }}&nbsp;DT</span>
        </div>
    </div>

    {{-- ───── Sélection du nouveau statut ───── --}}
    <div class="col-12">
        <label for="modalEditStatus" class="form-label fw-semibold">Nouveau statut</label>
        <select id="modalEditStatus" name="statut_id"
                class="select2 form-select" required>
            @foreach ($statuts as $statut)
                <option value="{{ $statut->id }}"
                        @selected($order->statut_id === $statut->id)>
                    {{ $statut->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- ───── Zone d’erreurs AJAX ───── --}}
    <div class="col-12">
        <div id="StatutaddValidationErrors"
             class="alert alert-danger d-none"></div>
    </div>

    {{-- ───── Boutons ───── --}}
    <div class="col-12 text-center mt-2">
        <button type="submit" class="btn btn-primary me-sm-2">
            <i class="ti ti-device-floppy me-1"></i> Enregistrer
        </button>
        <button type="button" class="btn btn-label-secondary"
                data-bs-dismiss="modal">
            Annuler
        </button>
    </div>
</form>
