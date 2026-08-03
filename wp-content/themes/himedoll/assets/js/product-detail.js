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

  const scrollButton = document.querySelector('[data-scroll-to-cart]');
  const cartForm = document.querySelector('form.cart');

  if (scrollButton && cartForm) {
    scrollButton.addEventListener('click', () => {
      cartForm.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });
  }

  const wishlist = document.querySelector('[data-wishlist]');
  if (wishlist) {
    wishlist.addEventListener('click', () => {
      const active = wishlist.getAttribute('aria-pressed') === 'true';
      wishlist.setAttribute('aria-pressed', String(!active));
      wishlist.textContent = active ? '♡ お気に入り' : '♥ お気に入り済み';
    });
  }
})();
