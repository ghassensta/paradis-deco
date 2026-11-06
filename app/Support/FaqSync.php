<?php

namespace App\Support;

use App\Models\Faq;
use Illuminate\Database\Eloquent\Model;

/**
 * Synchronise les FAQ envoyées par un formulaire admin avec la base.
 * Partagé par les produits et les catégories : même format de payload,
 * même nettoyage, même règle de suppression.
 */
final class FaqSync
{
    /** Règles de validation à fusionner dans celles du formulaire appelant. */
    public static function rules(string $key = 'faqs'): array
    {
        return [
            $key => 'nullable|array|max:30',
            $key . '.*.id' => 'nullable|integer',
            $key . '.*.question' => 'nullable|string|max:255',
            $key . '.*.answer' => 'nullable|string|max:5000',
            $key . '.*.position' => 'nullable|integer|min:0|max:9999',
            $key . '.*.is_active' => 'nullable|boolean',
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>|null  $rows
     * @return array{kept: int, deleted: int}
     */
    public static function apply(Model $owner, ?array $rows): array
    {
        $rows ??= [];
        $keptIds = [];
        $position = 0;

        foreach ($rows as $row) {
            $question = trim((string) ($row['question'] ?? ''));
            $answer = RichText::sanitize($row['answer'] ?? null);

            // Une ligne vide (ajoutée puis abandonnée) est simplement ignorée.
            if ($question === '' || $answer === '') {
                continue;
            }

            $attributes = [
                'question' => $question,
                'answer' => $answer,
                'position' => isset($row['position']) && $row['position'] !== ''
                    ? (int) $row['position']
                    : $position,
                'is_active' => (bool) ($row['is_active'] ?? false),
            ];

            $existing = isset($row['id']) && $row['id']
                ? $owner->faqs()->whereKey($row['id'])->first()
                : null;

            if ($existing) {
                $existing->update($attributes);
                $keptIds[] = $existing->id;
            } else {
                $keptIds[] = $owner->faqs()->create($attributes)->id;
            }

            $position++;
        }

        // Les lignes absentes du formulaire ont été supprimées par l'admin.
        $deleted = $owner->faqs()
            ->when($keptIds, fn ($q) => $q->whereNotIn('id', $keptIds))
            ->delete();

        return ['kept' => count($keptIds), 'deleted' => $deleted];
    }
}
