<?php

namespace App\Support;

use App\Models\Configuration;

/**
 * Point unique de décision pour les scripts de mesure d'audience.
 *
 * Le layout public interroge ce service ; aucune vue ne teste un identifiant
 * de pixel. Un tracker n'est actif que si son interrupteur est coché ET son
 * identifiant renseigné : une case cochée sans identifiant n'injecte rien.
 */
final class TrackingService
{
    /** @var array<string, array{id: string, enabled: string}> */
    private const TRACKERS = [
        'meta' => ['id' => 'meta_pixel_id', 'enabled' => 'meta_pixel_enabled'],
        'tiktok' => ['id' => 'tiktok_pixel_id', 'enabled' => 'tiktok_pixel_enabled'],
        'ga4' => ['id' => 'ga4_measurement_id', 'enabled' => 'ga4_enabled'],
    ];

    public function __construct(private ?Configuration $config = null)
    {
    }

    public static function fromConfig(?Configuration $config): self
    {
        return new self($config);
    }

    /** Identifiant du tracker s'il est réellement exploitable, sinon null. */
    public function idFor(string $tracker): ?string
    {
        $map = self::TRACKERS[$tracker] ?? null;

        if (! $map || ! $this->config) {
            return null;
        }

        if (! (bool) ($this->config->{$map['enabled']} ?? false)) {
            return null;
        }

        $id = trim((string) ($this->config->{$map['id']} ?? ''));

        return $id !== '' ? $id : null;
    }

    public function enabled(string $tracker): bool
    {
        return $this->idFor($tracker) !== null;
    }

    /** Vrai dès qu'au moins un tracker est actif. */
    public function hasAny(): bool
    {
        foreach (array_keys(self::TRACKERS) as $tracker) {
            if ($this->enabled($tracker)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Charge utile d'un évènement e-commerce, à partir des vraies données.
     * Rien n'est inventé : les montants proviennent du panier ou de la commande.
     *
     * @param  array<string, mixed>  $payload
     */
    public function event(string $name, array $payload = []): array
    {
        return array_filter(
            ['event' => $name] + $payload,
            fn ($value) => $value !== null && $value !== ''
        );
    }

    public function currency(): string
    {
        return $this->config?->currency ?: 'TND';
    }
}
