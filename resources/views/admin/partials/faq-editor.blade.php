{{--
    Éditeur de FAQ réutilisable (produits et catégories).
    $faqs : collection de App\Models\Faq (peut être vide)
--}}
@php
    $uid = $uid ?? 'faq';
    $faqRows = old('faqs', ($faqs ?? collect())->map(fn ($f) => [
        'id' => $f->id,
        'question' => $f->question,
        'answer' => $f->answer,
        'position' => $f->position,
        'is_active' => $f->is_active ? 1 : 0,
    ])->values()->all());
@endphp

<div class="mb-4 faq-editor" id="{{ $uid }}-section">
    <label class="form-label fw-bold d-block">Questions fréquentes</label>

    <small class="text-muted d-block mb-3">
        Ajoutez uniquement des questions réellement utiles aux clients. Les réponses doivent être
        précises, courtes et basées sur les informations disponibles.
        <strong>N'inventez aucune information.</strong>
        Sans question active, aucune FAQ n'est affichée sur le site.
    </small>

    <div class="js-faq-rows">
        @foreach ($faqRows as $i => $row)
            <div class="card mb-2 faq-row">
                <div class="card-body py-3">
                    <input type="hidden" name="faqs[{{ $i }}][id]" value="{{ $row['id'] ?? '' }}">

                    <div class="mb-2">
                        <label class="form-label mb-1">Question</label>
                        <input type="text" class="form-control" maxlength="255"
                               name="faqs[{{ $i }}][question]"
                               value="{{ $row['question'] ?? '' }}"
                               placeholder="Ex. : Quelles sont les dimensions ?">
                    </div>

                    <div class="mb-2">
                        <label class="form-label mb-1">Réponse</label>
                        <textarea class="form-control" rows="3" maxlength="5000"
                                  name="faqs[{{ $i }}][answer]"
                                  placeholder="Réponse courte et factuelle.">{{ $row['answer'] ?? '' }}</textarea>
                    </div>

                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <div>
                            <label class="form-label mb-1">Ordre</label>
                            <input type="number" class="form-control form-control-sm" style="width: 6rem"
                                   min="0" name="faqs[{{ $i }}][position]" value="{{ $row['position'] ?? $i }}">
                        </div>

                        <div class="form-check mt-4">
                            <input type="hidden" name="faqs[{{ $i }}][is_active]" value="0">
                            <input class="form-check-input" type="checkbox" value="1"
                                   id="{{ $uid }}-active-{{ $i }}"
                                   name="faqs[{{ $i }}][is_active]"
                                   {{ ($row['is_active'] ?? 1) ? 'checked' : '' }}>
                            <label class="form-check-label" for="{{ $uid }}-active-{{ $i }}">Active</label>
                        </div>

                        <button type="button" class="btn btn-sm btn-label-danger mt-4 ms-auto js-faq-remove">
                            <i class="ti ti-trash"></i> Supprimer
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <button type="button" class="btn btn-sm btn-label-primary js-faq-add">
        <i class="ti ti-plus"></i> Ajouter une question
    </button>

    @error('faqs')
        <div class="text-danger mt-1">{{ $message }}</div>
    @enderror

    {{-- Doit rester dans .faq-editor : le script l'y cherche. Placé à côté,
         tous les boutons restaient inertes. --}}
    <template class="js-faq-template">
        <div class="card mb-2 faq-row">
            <div class="card-body py-3">
                <input type="hidden" name="faqs[__INDEX__][id]" value="">

                <div class="mb-2">
                    <label class="form-label mb-1">Question</label>
                    <input type="text" class="form-control" maxlength="255"
                           name="faqs[__INDEX__][question]"
                           placeholder="Ex. : Quelles sont les dimensions ?">
                </div>

                <div class="mb-2">
                    <label class="form-label mb-1">Réponse</label>
                    <textarea class="form-control" rows="3" maxlength="5000"
                              name="faqs[__INDEX__][answer]"
                              placeholder="Réponse courte et factuelle."></textarea>
                </div>

                <div class="d-flex flex-wrap align-items-center gap-3">
                    <div>
                        <label class="form-label mb-1">Ordre</label>
                        <input type="number" class="form-control form-control-sm" style="width: 6rem"
                               min="0" name="faqs[__INDEX__][position]" value="__INDEX__">
                    </div>

                    <div class="form-check mt-4">
                        <input type="hidden" name="faqs[__INDEX__][is_active]" value="0">
                        <input class="form-check-input" type="checkbox" value="1"
                               name="faqs[__INDEX__][is_active]" checked>
                        <label class="form-check-label">Active</label>
                    </div>

                    <button type="button" class="btn btn-sm btn-label-danger mt-4 ms-auto js-faq-remove">
                        <i class="ti ti-trash"></i> Supprimer
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
