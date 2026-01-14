<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Paradis Déco | Boutique déco Tunisie')</title>

    <!-- Fonts / icône -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/cover-image-removebg-preview.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('meta')
    <script src="https://cdn.tailwindcss.com"></script>
    @yield('css')
<meta name="google-site-verification" content="XvcyV0f3IMnjIMN1zqCcpVUKjYOShFpheWikB40yCgg" />

@php
    $shippingCost      = (float) ($config->shipping_cost ?? 0);
    $shippingCostJson  = json_encode($shippingCost, JSON_NUMERIC_CHECK);
    $freeShippingLimit = 150;
    $freeShippingJson  = json_encode($freeShippingLimit, JSON_NUMERIC_CHECK);
@endphp

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-404BKDSJRD"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-404BKDSJRD');
</script>
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

        <a href="/checkout"
           class="block w-full bg-[#dfb54e] hover:bg-[#cba640] text-white text-center py-3 rounded-lg transition-colors duration-300 font-semibold shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-[#dfb54e]"
           aria-label="Passer à la caisse">
           Passer la commande
        </a>

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
    function openCartOffcanvas() {
    const cartOffcanvas = document.getElementById('cartOffcanvas');
    const cartOverlay = document.getElementById('cartOverlay');

    cartOffcanvas.classList.remove('translate-x-full'); // Slide the offcanvas into view
    cartOverlay.classList.remove('hidden'); // Show the overlay
    document.body.classList.add('overflow-hidden'); // Prevent body scrolling
    renderCart(); // Update cart contents
}
/* ------------------ 1) Configuration ------------------ */
const SHIPPING_FEE      = {{ $shippingCostJson }};
const FREE_SHIPPING_MIN = {{ $freeShippingJson }} ;
console.log("SHIPPING_FEE",SHIPPING_FEE);
console.log("FREE_SHIPPING_MIN",FREE_SHIPPING_MIN);
/* ------------------ 2) Helpers + Storage -------------- */
const STORAGE_KEY = 'cart';
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
  const cartCountB    = document.getElementById('cartCount');
  const loadingBar    = document.getElementById('loadingBar');
  const miniCart      = document.getElementById('miniCart');
  const miniCartItems = document.getElementById('miniCartItems');

  /* Off-canvas open / close */
  document.querySelectorAll('[aria-label="Panier"],[href="/panier"]')
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
  const cartBtn = document.querySelector('.cart-button');
  if (cartBtn && miniCart) {
    cartBtn.addEventListener('mouseenter',()=>{ miniCart.classList.remove('hidden'); renderMiniCart(); });
    miniCart.addEventListener('mouseleave',()=> miniCart.classList.add('hidden'));
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
        quantity:1
      };
      if (sanitizeCart([p]).length===0) throw new Error('Données produit invalides');
      if (p.stock===0) throw new Error('Produit épuisé');

      const cart = getCart();
      const found = cart.find(i=>i.id===p.id);
      found ? (found.quantity < found.stock ? found.quantity++ : showNotification('Stock max','error'))
            : cart.push(p);

      saveCart(cart); renderCart(); updateCount();

    } catch(err){ showNotification(err.message,'error'); console.error(err);
    } finally {

      btn.disabled=false; btn.innerHTML=htmlBackup;
      loadingBar?.classList.remove('scale-x-100'); loadingBar?.classList.add('scale-x-0');
      setTimeout(()=>loadingBar?.classList.add('hidden'),500);
    openCartOffcanvas();
    }
  };

  /* Update / Remove */
  window.updateQuantity = (id,d)=>{
    const cart=getCart(); const idx=cart.findIndex(i=>i.id===id); if(idx===-1) return;
    cart[idx].quantity+=d;
    cart[idx].quantity<=0 ? cart.splice(idx,1) :
    cart[idx].quantity>cart[idx].stock && (cart[idx].quantity=cart[idx].stock, showNotification('Stock max','error'));
    saveCart(cart); renderCart();
  };
  window.removeFromCart = id => { saveCart(getCart().filter(i=>i.id!==id)); renderCart(); showNotification('Produit retiré.'); };

  /* Render cart */
 function renderCart() {
    const cart = getCart();
    /* Empty cart */
    if (cart.length === 0) {
        cartItemsBox.innerHTML = '<div class="text-center py-8 text-gray-500">Votre panier est vide</div>';
        cartSubtotalT.textContent = '0 DT';
        shippingCostT.textContent = `${SHIPPING_FEE.toFixed(2)} DT`; // Always display SHIPPING_FEE
        cartTotalT.textContent = '0 DT';
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
            <img src="${i.image}" alt="${i.name}" class="w-16 h-16 object-cover rounded-lg">
            <div class="ml-4 flex-1">
                <h4 class="font-medium">${i.name}</h4>
                <p class="text-sm text-gray-500">${i.price.toFixed(2)} DT × ${i.quantity}</p>
                <p class="font-semibold text-gray-600">${line.toFixed(2)} DT</p>
            </div>
            <div class="flex items-center space-x-2">
                <button onclick="removeFromCart(${i.id})" class="text-gray-500 hover:text-red-600"><i class="fa-solid fa-trash"></i></button>
                <div class="flex border rounded-lg">
                    <button onclick="updateQuantity(${i.id},-1)" class="px-2 text-gray-500 hover:text-[#dfb54e]">−</button>
                    <span class="px-2">${i.quantity}</span>
                    <button onclick="updateQuantity(${i.id},1)" class="px-2 text-gray-500 hover:text-[#dfb54e]">+</button>
                </div>
            </div>
        </div>`;
    }).join('');
    const ship = SHIPPING_FEE; // Always use SHIPPING_FEE
    const total = sub + ship;

    cartSubtotalT.textContent = `${sub.toFixed(2)} DT`;
    shippingCostT.textContent = `${ship.toFixed(2)} DT`; // Always display SHIPPING_FEE
    cartTotalT.textContent = `${total.toFixed(2)} DT`;

    updateCount();
    renderMiniCart();
}

  /* Mini cart */
  function renderMiniCart(){
    if(!miniCartItems) return;
    const cart=getCart();
    miniCartItems.innerHTML = cart.length===0
      ? '<div class="py-4 text-center text-sm text-gray-500">Votre panier est vide</div>'
      : cart.map(i=>`
          <div class="flex items-center space-x-2">
            <img src="${i.image}" alt="${i.name}" class="w-12 h-12 object-cover rounded">
            <div class="flex-1">
              <h4 class="text-sm font-medium">${i.name}</h4>
              <p class="text-xs text-gray-500">${i.quantity} × ${i.price.toFixed(2)} DT</p>
            </div>
          </div>`).join('');
  }

  /* Badge qty */
  const updateCount = ()=>{
    if(cartCountB) cartCountB.textContent = getCart().reduce((t,i)=>t+i.quantity,0);
  };

  /* Init */
  renderCart(); renderMiniCart(); updateCount();
});
</script>
</body>
</html>
