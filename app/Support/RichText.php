<?php

namespace App\Support;

use Illuminate\Support\Str;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerAction;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

/**
 * Nettoyage du HTML éditorial produit par l'éditeur Quill.
 *
 * Le contenu saisi en back-office est traité comme non fiable : un formulaire
 * peut être contourné par une requête HTTP directe. La liste blanche ci-dessous
 * est volontairement réduite aux balises utiles au référencement et à la
 * lisibilité — ni script, ni iframe, ni style, ni attribut d'événement.
 */
final class RichText
{
    /** Balises autorisées, avec leurs attributs. */
    private const ALLOWED = [
        'p' => [],
        'br' => [],
        'strong' => [],
        'b' => [],
        'em' => [],
        'i' => [],
        'u' => [],
        's' => [],
        'h2' => [],
        'h3' => [],
        'ul' => [],
        'ol' => [],
        'li' => [],
        'blockquote' => [],
        'a' => ['href', 'target', 'rel'],
    ];

    private static ?HtmlSanitizer $sanitizer = null;

    /** Nettoie du HTML éditorial et retourne une chaîne sûre à afficher. */
    public static function sanitize(?string $html): string
    {
        if (blank($html)) {
            return '';
        }

        $clean = trim(self::sanitizer()->sanitize(self::stripRawTextElements($html)));

        // Quill renvoie « <p><br></p> » pour un contenu vide.
        return self::isEmptyHtml($clean) ? '' : $clean;
    }

    /** Convertit du HTML éditorial en texte brut normalisé (meta, JSON-LD…). */
    public static function toText(?string $html): string
    {
        if (blank($html)) {
            return '';
        }

        // Sanitisation préalable : strip_tags() retirerait la balise <script>
        // mais laisserait son code en texte, qui se retrouverait dans les
        // métadonnées et le JSON-LD.
        if (preg_match('#<[a-z!/]#i', (string) $html)) {
            $html = self::sanitizer()->sanitize(self::stripRawTextElements((string) $html));
        }

        // Les balises de bloc deviennent des espaces pour ne pas coller les mots.
        $text = preg_replace('#<(?:br|/p|/h[23]|/li|/blockquote)\b[^>]*>#i', ' ', (string) $html);

        return trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags((string) $text), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
    }

    /**
     * Normalise un contenu en HTML sûr : le texte brut (saisie historique ou
     * requête directe) devient des paragraphes, le HTML existant est nettoyé.
     * Sert au stockage comme au rechargement dans l'éditeur.
     */
    public static function toHtml(?string $value): string
    {
        if (blank($value)) {
            return '';
        }

        if (self::looksLikeHtml($value)) {
            return self::sanitize($value);
        }

        $paragraphs = preg_split('/\R{2,}/u', trim($value)) ?: [];

        $html = collect($paragraphs)
            ->filter(fn ($block) => trim($block) !== '')
            ->map(fn ($block) => '<p>' . nl2br(e(trim($block)), false) . '</p>')
            ->implode('');

        return self::sanitize($html);
    }

    public static function looksLikeHtml(?string $value): bool
    {
        return filled($value) && (bool) preg_match('#</?(?:p|br|h[23]|ul|ol|li|strong|em|u|s|a|b|i|blockquote)\b#i', $value);
    }

    private static function isEmptyHtml(string $html): bool
    {
        return Str::of($html)->replaceMatches('/<[^>]*>/', '')->replace('&nbsp;', ' ')->trim()->isEmpty();
    }

    /**
     * Éléments dits « raw text » : le navigateur traite leur contenu comme du
     * texte, pas comme du balisage. Symfony ne neutralise ainsi que <script> ;
     * pour les autres, le contenu ressortirait en texte visible. On les retire
     * donc avec leur contenu avant de passer au sanitizer.
     */
    private static function stripRawTextElements(string $html): string
    {
        return (string) preg_replace(
            '#<(script|style|textarea|title|noscript|xmp|template)\b[^>]*>.*?</\1\s*>#is',
            '',
            $html
        );
    }

    private static function sanitizer(): HtmlSanitizer
    {
        if (self::$sanitizer instanceof HtmlSanitizer) {
            return self::$sanitizer;
        }

        $config = new HtmlSanitizerConfig();

        foreach (self::ALLOWED as $element => $attributes) {
            $config = $config->allowElement($element, $attributes);
        }

        $config = $config
            // Une balise non listée est « bloquée » : elle disparaît mais son
            // texte est conservé (un <div> collé depuis Word ne perd rien).
            ->defaultAction(HtmlSanitizerAction::Block)
            // Ces balises-ci sont supprimées AVEC leur contenu : le code d'un
            // <script> ne doit jamais réapparaître en texte visible.
            ->dropElement('script')
            ->dropElement('style')
            ->dropElement('iframe')
            ->dropElement('object')
            ->dropElement('embed')
            ->dropElement('form')
            ->dropElement('svg')
            // javascript:, data: et vbscript: sont ainsi écartés.
            ->allowLinkSchemes(['https', 'http', 'mailto'])
            ->allowRelativeLinks()
            ->forceAttribute('a', 'rel', 'noopener noreferrer')
            ->withMaxInputLength(200_000);

        return self::$sanitizer = new HtmlSanitizer($config);
    }
}
