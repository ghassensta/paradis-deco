<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Paradis Déco | Boutique déco Tunisie')</title>

    <!-- Fonts / icône -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    {{-- Préconnexion Google uniquement si la mesure d'audience est active :
         inutile de résoudre un domaine qui ne sera jamais appelé. --}}
    @if (\App\Support\TrackingService::fromConfig($config ?? null)->enabled('ga4'))
        <link rel="preconnect" href="https://www.googletagmanager.com" crossorigin>
        <link rel="dns-prefetch" href="//www.googletagmanager.com">
    @endif

    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    </noscript>
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/cover-image-removebg-preview.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $siteName       = $config->site_name ?? 'Paradis Déco';
        $siteUrl        = config('app.url', url('/'));
        $siteDesc       = $config->meta_description ?? 'Boutique déco en ligne en Tunisie : meubles, luminaires, tapis et accessoires artisanaux pour un intérieur moderne.';
        $defaultOgImage = asset('assets/img/cover-image-removebg-preview.png');
        $supportEmail   = $config->support_email ?? 'contact@paradisdeco.tn';
        $supportPhone   = $config->support_phone ?? '+216 73 000 000';
        $addressText    = $config->address ?? "Rue Habib Thameur, M'saken 4070, Sousse, Tunisie";
    @endphp

    @if(!empty($config->homepage_banner))
        @php
            $bannerPath = $config->homepage_banner; // e.g. banners/hero-banner.jpg
            $bannerDir  = trim(dirname($bannerPath), '/.');
            $bannerFile = pathinfo($bannerPath, PATHINFO_FILENAME); // hero-banner
            $bannerBase = ($bannerDir && $bannerDir !== '.') ? $bannerDir . '/' . $bannerFile : $bannerFile;
        @endphp
        <link rel="preload"
              as="image"
              fetchpriority="high"
              href="{{ asset('storage/' . $bannerBase . '-640.jpg') }}"
              imagesrcset="{{ asset('storage/' . $bannerBase . '-320.webp') }} 320w,
                           {{ asset('storage/' . $bannerBase . '-640.webp') }} 640w,
                           {{ asset('storage/' . $bannerBase . '-960.webp') }} 960w"
              imagesizes="(max-width: 768px) 100vw,
                           (max-width: 1280px) 100vw,
                           100vw">
    @endif

    <meta name="description" content="{!! trim($__env->yieldContent('description')) ?: e($siteDesc) !!}">

    <meta property="og:type" content="website">
    <meta property="og:locale" content="fr_TN">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{!! trim($__env->yieldContent('title')) ?: e($siteName . ' | Boutique déco Tunisie') !!}">
    <meta property="og:description" content="{!! trim($__env->yieldContent('description')) ?: e($siteDesc) !!}">
    <meta property="og:url" content="{!! trim($__env->yieldContent('canonical')) ?: e(url()->current()) !!}">
    <meta property="og:image" content="{!! trim($__env->yieldContent('og_image')) ?: e($defaultOgImage) !!}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{!! trim($__env->yieldContent('title')) ?: e($siteName . ' | Boutique déco Tunisie') !!}">
    <meta name="twitter:description" content="{!! trim($__env->yieldContent('description')) ?: e($siteDesc) !!}">
    <meta name="twitter:image" content="{!! trim($__env->yieldContent('og_image')) ?: e($defaultOgImage) !!}">

    @yield('meta')

    @include('front-office.partials.tracking')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Les visiteurs qui limitent les animations ne subissent ni zoom
           d'image, ni transition, ni défilement animé. */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }
    </style>

    @yield('css')
<meta name="google-site-verification" content="XvcyV0f3IMnjIMN1zqCcpVUKjYOShFpheWikB40yCgg" />

@php
    $shippingCost      = (float) ($config->shipping_cost ?? 0);
    $shippingCostJson  = json_encode($shippingCost, JSON_NUMERIC_CHECK);
    // Seuil piloté par l'administration : une valeur en dur ici rendait
    // le réglage « livraison gratuite » sans effet sur le site.
    $freeShippingLimit = (float) ($config->free_shipping_threshold ?? 0);
    $freeShippingJson  = json_encode($freeShippingLimit, JSON_NUMERIC_CHECK);
@endphp

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "{{ $siteName }}",
  "url": "{{ $siteUrl }}",
  "logo": "{{ $defaultOgImage }}",
  "email": "{{ $supportEmail }}",
  "telephone": "{{ $supportPhone }}"
}
</script>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "LocalBusiness",
  "name": "{{ $siteName }}",
  "image": "{{ $defaultOgImage }}",
  "url": "{{ $siteUrl }}",
  "telephone": "{{ $supportPhone }}",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "{{ $addressText }}",
    "addressLocality": "M'saken",
    "addressRegion": "Sousse",
    "postalCode": "4070",
    "addressCountry": "TN"
  }
}
</script>

<!-- Google tag (gtag.js) -->
    {{-- Google Analytics est désormais piloté par la configuration :
         voir front-office/partials/tracking.blade.php --}}
</head>

<body class="bg-gray-100 font-sans text-gray-800">
<!-- Loading bar -->
<div id="loadingBar"
     class="fixed top-0 left-0 w-full h-1 bg-[#dfb54e] transform scale-x-0 origin-left transition-transform duration-500 ease-in-out z-50 hidden">
</div>

<!-- ============================ OFFCANVAS PANIER ============================ -->
<div id="cartOffcanvas"
     class="fixed inset-y-0 right-0 w-full sm:w-96 bg-white shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out z-50 overflow-y-auto">
  <div class="flex flex-col h-full">

    <!-- Header -->
    <div class="flex justify-between items-center p-6 border-b border-gray-100 bg-gradient-to-r from-[#fffaf0] to-white">
        <span class="text-xl font-bold">Votre Panier</span>
        <button id="closeCartOffcanvas"
                class="p-2 rounded-full text-gray-600 hover:text-[#dfb54e] hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-[#dfb54e]"
                aria-label="Fermer le panier">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>
    </div>

    <!-- Contenu -->
    <div class="flex-1 p-6 overflow-y-auto">
        <div id="cartItems" class="space-y-4" aria-live="polite">
            <div class="text-center py-10 text-gray-500 text-lg">Votre panier est vide</div>
        </div>
    </div>

    <!-- Footer -->
    <div class="p-6 border-t border-gray-100 bg-gray-50">
        <div class="flex justify-between items-center mb-2">
            <span class="text-gray-700">Sous-total&nbsp;:</span>
            <span id="cartSubtotal" class="font-medium text-gray-700">0 DT</span>
        </div>
        {{-- Affichée uniquement si un seuil est réellement configuré. --}}
        @if (($config->free_shipping_threshold ?? 0) > 0)
            <div id="freeShippingBlock" class="mb-5 hidden">
                <p id="freeShippingText" class="text-sm text-gray-600 mb-2" aria-live="polite"></p>
                <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div id="freeShippingBar" class="h-full bg-[#dfb54e] rounded-full transition-all duration-300" style="width:0%"></div>
                </div>
            </div>
        @endif

        <div class="flex justify-between items-center mb-6">
            <span class="text-gray-700">Frais de livraison&nbsp;:</span>
            <span id="shippingCost" class="font-medium text-gray-700">
                {{ number_format($shippingCost, 2, '.', ' ') }} DT
            </span>
        </div>
        <div class="flex justify-between items-center mb-6">
            <span class="text-lg font-semibold text-gray-900">Total&nbsp;:</span>
            <span id="cartTotal" class="text-xl font-bold text-[#dfb54e]">0 DT</span>
        </div>

        <a href="/checkout" id="checkoutCta"
           class="block w-full bg-[#dfb54e] hover:bg-[#cba640] text-white text-center py-3 rounded-lg transition-colors duration-300 font-semibold shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-[#dfb54e]"
           aria-label="Passer à la caisse">
           Passer la commande
        </a>

        <ul class="mt-5 space-y-2 text-sm text-gray-600">
            @if ($config->city ?? null)
                <li><i class="fa-solid fa-truck text-[#dfb54e] mr-2" aria-hidden="true"></i>Livraison depuis {{ $config->city }}</li>
            @endif
            @if (($config->delivery_estimate_days ?? 0) > 0)
                <li><i class="fa-solid fa-clock text-[#dfb54e] mr-2" aria-hidden="true"></i>Livraison estimée sous {{ $config->delivery_estimate_days }} jour(s)</li>
            @endif
            @if ($config->support_phone ?? null)
                <li><i class="fa-solid fa-headset text-[#dfb54e] mr-2" aria-hidden="true"></i>Assistance : {{ $config->support_phone }}</li>
            @endif
        </ul>

        <button id="continueShoppingBtn"
                class="w-full mt-3 text-[#dfb54e] hover:text-[#cba640] py-2 rounded-lg transition-colors duration-300 font-medium hover:bg-[#fdf5e6] focus:outline-none focus:ring-2 focus:ring-[#dfb54e]">
            Continuer mes achats
        </button>
    </div>
  </div>
</div>

<!-- Overlay -->
<div id="cartOverlay" class="fixed inset-0 bg-[#000000a2] z-40 hidden"></div>

@include('front-office.layouts.header')
@yield('content')
@include('front-office.layouts.footer')

@yield('js')

<script type="module">
/* ------------------ 1) Configuration ------------------ */
const SHIPPING_FEE      = {{ $shippingCostJson }};
const FREE_SHIPPING_MIN = {{ $freeShippingJson }} ;
console.log("SHIPPING_FEE",SHIPPING_FEE);
console.log("FREE_SHIPPING_MIN",FREE_SHIPPING_MIN);
/* ------------------ 2) Helpers + Storage -------------- */
const STORAGE_KEY = 'cart';
const TRACKING_CURRENCY = @json($config->currency ?: 'TND');
// Une même fiche peut occuper plusieurs lignes : une par variante choisie.
const lineKey = i => i.variant_id ? `${i.id}-${i.variant_id}` : String(i.id);
const sanitizeCart = c => c.filter(i => i.id && i.name && !isNaN(i.price) && i.image && !isNaN(i.stock));

const getCart  = () => {
  const raw = JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];
  const clean = sanitizeCart(raw);
  if (clean.length !== raw.length) localStorage.setItem(STORAGE_KEY, JSON.stringify(clean));
  return clean;
};
const saveCart = c => localStorage.setItem(STORAGE_KEY, JSON.stringify(c));

/* Toast */
const showNotification = (msg, type='success') => {
  const n = document.createElement('div');
  n.className = `fixed top-10 right-4 z-50 px-4 py-2 rounded-lg shadow-lg
                 ${type==='success' ? 'bg-green-500':'bg-red-500'} text-white`;
  n.textContent = msg;
  document.body.appendChild(n);
  setTimeout(()=>n.remove(),3000);
};

/* ------------------ 3) DOM Ready ---------------------- */
document.addEventListener('DOMContentLoaded', () => {

  /* Sélecteurs */
  const cartOffcanvas = document.getElementById('cartOffcanvas');
  const cartOverlay   = document.getElementById('cartOverlay');
  const cartItemsBox  = document.getElementById('cartItems');
  const cartSubtotalT = document.getElementById('cartSubtotal');
  const shippingCostT = document.getElementById('shippingCost');
  const cartTotalT    = document.getElementById('cartTotal');
  const cartCounters  = document.querySelectorAll('[data-cart-count]');
  const loadingBar    = document.getElementById('loadingBar');
  const miniCart      = document.getElementById('miniCart');
  const miniCartItems = document.getElementById('miniCartItems');

  /* Off-canvas open / close */
  document.querySelectorAll('.cart-button,[href="/panier"]')
    .forEach(btn=>btn.addEventListener('click',e=>{
      e.preventDefault();
      cartOffcanvas.classList.remove('translate-x-full');
      cartOverlay.classList.remove('hidden');
      document.body.classList.add('overflow-hidden');
      renderCart();
    }));
  const closeCart = ()=>{
    cartOffcanvas.classList.add('translate-x-full');
    cartOverlay.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
  };
  document.getElementById('closeCartOffcanvas').onclick = closeCart;
  document.getElementById('continueShoppingBtn').onclick = closeCart;
  cartOverlay.onclick = closeCart;
  document.addEventListener('keydown',e=>e.key==='Escape' && closeCart());

  /* Mini-cart hover */
  const cartBtns = document.querySelectorAll('.cart-button');
  if (cartBtns.length && miniCart) {
    cartBtns.forEach(btn => btn.addEventListener('mouseenter',()=>{ miniCart.classList.remove('hidden'); renderMiniCart(); }));
    miniCart.addEventListener('mouseleave',()=> miniCart.classList.add('hidden'));
  }

  /* Ouverture du panier après ajout.
     Cette fonction vivait hors du DOMContentLoaded et appelait renderCart(),
     défini à l'intérieur : chaque ajout levait une ReferenceError non
     capturée (l'appel se fait dans un bloc finally). */
  function openCartOffcanvas() {
    cartOffcanvas.classList.remove('translate-x-full');
    cartOverlay.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    renderCart();
  }

  /* --------- Add to cart (exposé) --------- */
  window.addToCart = btn => {
    loadingBar?.classList.remove('hidden','scale-x-0');
    loadingBar?.classList.add('scale-x-100');
    btn.disabled = true;
    const htmlBackup = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

    try {
      const p = {
        id:Number(btn.dataset.id),
        name:btn.dataset.name,
        price:Number(btn.dataset.price),
        image:btn.dataset.image,
        stock:Number(btn.dataset.stock),
        quantity:Math.max(1, Number(btn.dataset.quantity) || 1),
        variant_id:Number(btn.dataset.variantId) || null,
        variant_label:btn.dataset.variantLabel || ''
      };
      if (sanitizeCart([p]).length===0) throw new Error('Données produit invalides');
      if (p.stock===0) throw new Error('Produit épuisé');

      const cart = getCart();
      const found = cart.find(i=>lineKey(i)===lineKey(p));
      const line = found ?? p;
      if ((found ? found.quantity : 0) + p.quantity > line.stock) showNotification('Stock max','error');
      line.quantity = Math.min((found ? found.quantity : 0) + p.quantity, line.stock);
      if (!found) cart.push(p);

      saveCart(cart); renderCart(); updateCount();

      // Évènement émis après l'ajout réel, avec les données du produit.
      window.paradisTrack('AddToCart', {
        content_ids: [String(p.id)],
        content_name: p.name,
        content_type: 'product',
        value: p.price,
        currency: TRACKING_CURRENCY
      });

    } catch(err){ showNotification(err.message,'error'); console.error(err);
    } finally {

      btn.disabled=false; btn.innerHTML=htmlBackup;
      loadingBar?.classList.remove('scale-x-100'); loadingBar?.classList.add('scale-x-0');
      setTimeout(()=>loadingBar?.classList.add('hidden'),500);
    openCartOffcanvas();
    }
  };

  /* Update / Remove */
  window.updateQuantity = (key,d)=>{
    const cart=getCart(); const idx=cart.findIndex(i=>lineKey(i)===key); if(idx===-1) return;
    cart[idx].quantity+=d;
    cart[idx].quantity<=0 ? cart.splice(idx,1) :
    cart[idx].quantity>cart[idx].stock && (cart[idx].quantity=cart[idx].stock, showNotification('Stock max','error'));
    saveCart(cart); renderCart();
  };
  window.removeFromCart = key => { saveCart(getCart().filter(i=>lineKey(i)!==key)); renderCart(); showNotification('Produit retiré.'); };

  /* Render cart */
  /* Progression vers la livraison gratuite (masquée si aucun seuil configuré) */
  function renderFreeShippingProgress(subtotal) {
    const block = document.getElementById('freeShippingBlock');

    if (!block || !(FREE_SHIPPING_MIN > 0)) {
      return;
    }

    const text = document.getElementById('freeShippingText');
    const bar  = document.getElementById('freeShippingBar');
    const missing = FREE_SHIPPING_MIN - subtotal;

    block.classList.toggle('hidden', subtotal <= 0);

    if (missing <= 0) {
      text.textContent = 'Livraison gratuite atteinte \u2713';
      bar.style.width = '100%';
    } else {
      text.textContent = `Plus que ${missing.toFixed(2)} DT pour bénéficier de la livraison gratuite.`;
      bar.style.width = Math.max(0, Math.min(100, (subtotal / FREE_SHIPPING_MIN) * 100)).toFixed(1) + '%';
    }
  }

 function renderCart() {
    const cart = getCart();
    /* Empty cart */
    if (cart.length === 0) {
        cartItemsBox.innerHTML = '<div class="text-center py-8 text-gray-500">Votre panier est vide</div>';
        cartSubtotalT.textContent = '0 DT';
        shippingCostT.textContent = '—';
        cartTotalT.textContent = '0 DT';
        // Sans cet appel, la barre gardait l'état du panier précédent :
        // « Livraison gratuite atteinte » restait affiché sur un panier vide.
        renderFreeShippingProgress(0);
        updateCount();
        renderMiniCart();
        return;
    }
    /* Cart items */
    let sub = 0;
    cartItemsBox.innerHTML = cart.map(i => {
        const line = i.price * i.quantity;
        sub += line;
        return `
        <div class="flex items-center border-b pb-4 p-2 hover:bg-gray-50 rounded-lg">
            <img src="${i.image}" alt="${i.name}" loading="lazy" class="w-16 h-16 object-cover rounded-lg">
            <div class="ml-4 flex-1">
                <span class="font-medium">${i.name}</span>
                ${i.variant_label ? `<p class="text-xs text-gray-500">${i.variant_label}</p>` : ''}
                <p class="text-sm text-gray-500">${i.price.toFixed(2)} DT × ${i.quantity}</p>
                <p class="font-semibold text-gray-600">${line.toFixed(2)} DT</p>
            </div>
            <div class="flex items-center space-x-2">
                <button onclick="removeFromCart('${lineKey(i)}')" class="text-gray-500 hover:text-red-600"><i class="fa-solid fa-trash"></i></button>
                <div class="flex border rounded-lg">
                    <button onclick="updateQuantity('${lineKey(i)}',-1)" class="px-2 text-gray-500 hover:text-[#dfb54e]">−</button>
                    <span class="px-2">${i.quantity}</span>
                    <button onclick="updateQuantity('${lineKey(i)}',1)" class="px-2 text-gray-500 hover:text-[#dfb54e]">+</button>
                </div>
            </div>
        </div>`;
    }).join('');
    // Règle identique au serveur (CheckoutController::storeOrder) : afficher
    // des frais que la commande ne facture pas trompait le client.
    // Même règle que le serveur : sans seuil configuré, les frais s'appliquent.
    const ship = FREE_SHIPPING_MIN > 0 && sub >= FREE_SHIPPING_MIN ? 0 : SHIPPING_FEE;
    const total = sub + ship;

    cartSubtotalT.textContent = `${sub.toFixed(2)} DT`;
    shippingCostT.textContent = ship === 0 ? 'Offerte' : `${ship.toFixed(2)} DT`;
    cartTotalT.textContent = `${total.toFixed(2)} DT`;

    renderFreeShippingProgress(sub);
    updateCount();
    renderMiniCart();
}

  /* InitiateCheckout : au départ réel vers le tunnel de commande */
  document.getElementById('checkoutCta')?.addEventListener('click', () => {
    const cart = getCart();
    const value = cart.reduce((sum, i) => sum + i.price * i.quantity, 0);

    if (cart.length > 0) {
      window.paradisTrack('InitiateCheckout', {
        content_ids: cart.map(i => String(i.id)),
        num_items: cart.reduce((n, i) => n + i.quantity, 0),
        value: Number(value.toFixed(3)),
        currency: TRACKING_CURRENCY
      });
    }
  });

  /* Mini cart */
  function renderMiniCart(){
    if(!miniCartItems) return;
    const cart=getCart();
    miniCartItems.innerHTML = cart.length===0
      ? '<div class="py-4 text-center text-sm text-gray-500">Votre panier est vide</div>'
      : cart.map(i=>`
          <div class="flex items-center space-x-2">
            <img src="${i.image}" loading="lazy" alt="${i.name}" class="w-12 h-12 object-cover rounded">
            <div class="flex-1">
              <span class="text-sm font-medium">${i.name}</span>
              ${i.variant_label ? `<p class="text-xs text-gray-500">${i.variant_label}</p>` : ''}
              <p class="text-xs text-gray-500">${i.quantity} × ${i.price.toFixed(2)} DT</p>
            </div>
          </div>`).join('');
  }

  /* Badge qty */
  const updateCount = ()=>{
    const total = getCart().reduce((t,i)=>t+i.quantity,0);
    cartCounters.forEach(el => el.textContent = total);
  };

  /* Init */
  renderCart(); renderMiniCart(); updateCount();
});
</script>
</body>
</html>
