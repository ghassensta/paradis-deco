{{--
    Scripts de mesure d'audience.

    Injecté uniquement par le layout PUBLIC (jamais dans l'admin) et uniquement
    pour les trackers dont l'identifiant est renseigné ET l'activation cochée.

    Consentement : le projet n'a pas de gestionnaire de consentement. Les
    scripts sont donc posés derrière window.paradisConsent — une fonction que
    le futur bandeau pourra appeler. Par défaut, `paradisConsentGranted` est
    vrai : brancher un bandeau consiste à le passer à faux avant ce script,
    puis à appeler window.paradisConsent.grant() après acceptation.
--}}
@php $tracking = \App\Support\TrackingService::fromConfig($config ?? null); @endphp

@if ($tracking->hasAny())
    <script>
        (function () {
            window.paradisConsentGranted = window.paradisConsentGranted !== false;

            var queue = [];

            window.paradisConsent = {
                granted: function () { return window.paradisConsentGranted === true; },
                // Les scripts marketing passent par ici : tant que le
                // consentement n'est pas accordé, ils sont mis en file.
                run: function (fn) {
                    if (this.granted()) { fn(); } else { queue.push(fn); }
                },
                grant: function () {
                    window.paradisConsentGranted = true;
                    queue.splice(0).forEach(function (fn) { fn(); });
                }
            };

            // File d'évènements e-commerce, alimentée par le front.
            // eventId : identifiant stable de l'évènement (ex. la commande), qui
            // permet à Meta et TikTok de dédupliquer un envoi serveur ultérieur.
            window.paradisTrack = function (name, payload, eventId) {
                window.paradisConsent.run(function () {
                    if (typeof fbq === 'function') { fbq('track', name, payload || {}, eventId ? { eventID: eventId } : undefined); }
                    if (typeof ttq === 'object' && ttq.track) { ttq.track(name, payload || {}, eventId ? { event_id: eventId } : undefined); }
                    if (typeof gtag === 'function') { gtag('event', name, payload || {}); }
                });
            };
        })();
    </script>

    @if ($metaPixelId = $tracking->idFor('meta'))
        {{-- Meta Pixel --}}
        <script>
            window.paradisConsent.run(function () {
                !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
                n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
                n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
                t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
                document,'script','https://connect.facebook.net/en_US/fbevents.js');

                fbq('init', @json($metaPixelId));
                fbq('track', 'PageView');
            });
        </script>
        <noscript>
            <img height="1" width="1" style="display:none" alt=""
                 src="https://www.facebook.com/tr?id={{ urlencode($metaPixelId) }}&ev=PageView&noscript=1">
        </noscript>
    @endif

    @if ($tiktokPixelId = $tracking->idFor('tiktok'))
        {{-- TikTok Pixel --}}
        <script>
            window.paradisConsent.run(function () {
                !function (w, d, t) {
                    w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=['page','track','identify','instances','debug','on','off','once','ready','alias','group','enableCookie','disableCookie'];
                    ttq.setAndDefer=function(e,n){e[n]=function(){e.push([n].concat(Array.prototype.slice.call(arguments,0)))}};
                    for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);
                    ttq.instance=function(e){for(var n=ttq._i[e]||[],i=0;i<ttq.methods.length;i++)ttq.setAndDefer(n,ttq.methods[i]);return n};
                    ttq.load=function(e,n){var i='https://analytics.tiktok.com/i18n/pixel/events.js';ttq._i=ttq._i||{};ttq._i[e]=[];ttq._i[e]._u=i;ttq._t=ttq._t||{};ttq._t[e]=+new Date;ttq._o=ttq._o||{};ttq._o[e]=n||{};
                    var o=d.createElement('script');o.type='text/javascript';o.async=!0;o.src=i+'?sdkid='+e+'&lib='+t;var a=d.getElementsByTagName('script')[0];a.parentNode.insertBefore(o,a)};
                    ttq.load(@json($tiktokPixelId));
                    ttq.page();
                }(window, document, 'ttq');
            });
        </script>
    @endif

    @if ($ga4Id = $tracking->idFor('ga4'))
        {{-- Google Analytics 4 --}}
        <script>
            window.paradisConsent.run(function () {
                var s = document.createElement('script');
                s.async = true;
                s.src = 'https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(@json($ga4Id));
                document.head.appendChild(s);

                window.dataLayer = window.dataLayer || [];
                window.gtag = function () { dataLayer.push(arguments); };
                gtag('js', new Date());
                gtag('config', @json($ga4Id));
            });
        </script>
    @endif
@else
    {{-- Aucun tracker actif : window.paradisTrack reste défini pour que le
         front puisse appeler les évènements sans test conditionnel. --}}
    <script>window.paradisTrack = function () {};</script>
@endif
