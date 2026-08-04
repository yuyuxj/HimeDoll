(() => {
  'use strict';

  const tabRoot = document.querySelector('[data-product-tabs]');
  if (tabRoot) {
    const tabs = [...tabRoot.querySelectorAll('[data-tab]')];
    const panels = [...tabRoot.querySelectorAll('[data-panel]')];

    tabs.forEach((tab) => {
      tab.addEventListener('click', () => {
        const target = tab.dataset.tab;
        tabs.forEach((item) => item.classList.toggle('is-active', item === tab));
        panels.forEach((panel) => {
          const active = panel.dataset.panel === target;
          panel.classList.toggle('is-active', active);
          panel.hidden = !active;
        });
      });
    });
  }

  const buyNow = document.querySelector('[data-buy-now]');
  const cartButton = document.querySelector('.single_add_to_cart_button');

  if (buyNow && cartButton) {
    buyNow.addEventListener('click', () => {
      let marker = document.querySelector('input[name="hd_buy_now"]');

      if (!marker) {
        marker = document.createElement('input');
        marker.type = 'hidden';
        marker.name = 'hd_buy_now';
        marker.value = '1';
        cartButton.closest('form.cart')?.appendChild(marker);
      }

      cartButton.click();
    });
  }

  const wishlist = document.querySelector('[data-wishlist]');
  if (wishlist) {
    wishlist.addEventListener('click', async () => {
      const productId = wishlist.dataset.productId;
      const nonce = wishlist.dataset.nonce;

      if (!productId || !nonce) return;

      const body = new URLSearchParams({
        action: 'hd_toggle_wishlist',
        product_id: productId,
        nonce
      });

      try {
        const response = await fetch(window.ajaxurl || '/wp-admin/admin-ajax.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/x-www-form-urlencoded'},
          body
        });

        if (response.status === 401) {
          window.location.href = wishlist.dataset.loginUrl || '/my-account/';
          return;
        }

        const data = await response.json();
        if (data.success) {
          wishlist.setAttribute('aria-pressed', String(data.data.active));
          wishlist.textContent = data.data.active ? '♥ お気に入り済み' : '♡ お気に入り';
        }
      } catch (error) {
        console.error('Wishlist request failed', error);
      }
    });
  }
})();
