<?php

namespace App\Models\Concerns;

use App\Models\Faq;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

/**
 * Partagé par Product et Category : les deux exposent la même FAQ
 * administrable, sans dupliquer la relation ni le rendu.
 */
trait HasFaqs
{
    public function faqs(): MorphMany
    {
        return $this->morphMany(Faq::class, 'faqable')->ordered();
    }

    public function activeFaqs(): MorphMany
    {
        return $this->faqs()->active();
    }

    /**
     * FAQ réellement affichables : une entrée dont la question ou la réponse
     * est vide ne doit ni s'afficher ni produire de données structurées.
     */
    public function displayableFaqs(): Collection
    {
        $faqs = $this->relationLoaded('activeFaqs')
            ? $this->activeFaqs
            : $this->activeFaqs()->get();

        return $faqs->filter(fn (Faq $faq) => filled(trim($faq->question)) && filled($faq->answer_text))->values();
    }
}
