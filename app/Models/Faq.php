<?php

namespace App\Models;

use App\Support\RichText;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Faq extends Model
{
    protected $fillable = [
        'question',
        'answer',
        'position',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'position' => 'integer',
    ];

    public function faqable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('position')->orderBy('id');
    }

    /** Réponse re-nettoyée à l'affichage (défense en profondeur). */
    public function getSafeAnswerAttribute(): string
    {
        return RichText::toHtml($this->answer);
    }

    /** Réponse en texte brut : JSON-LD et métadonnées n'acceptent pas de HTML. */
    public function getAnswerTextAttribute(): string
    {
        return RichText::toText($this->answer);
    }
}
