(() => {
  'use strict';

  const updateButton = document.querySelector('[name="update_cart"]');
  const quantityInputs = document.querySelectorAll('.woocommerce-cart-form input.qty');

  quantityInputs.forEach((input) => {
    input.addEventListener('change', () => {
      if (updateButton) {
        updateButton.removeAttribute('disabled');
        updateButton.classList.add('is-ready');
      }
    });
  });

  const placeOrder = document.querySelector('#place_order');
  if (placeOrder) {
    const mobileBar = document.createElement('div');
    mobileBar.className = 'mobile-checkout-bar';
    mobileBar.innerHTML = '<span>注文内容を確認して確定</span><button type="button">注文する</button>';

    mobileBar.querySelector('button')?.addEventListener('click', () => {
      placeOrder.scrollIntoView({ behavior: 'smooth', block: 'center' });
      window.setTimeout(() => placeOrder.focus(), 400);
    });

    document.body.appendChild(mobileBar);
  }
})();
