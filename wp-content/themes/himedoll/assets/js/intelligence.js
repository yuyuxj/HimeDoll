(() => {
  'use strict';

  document.querySelectorAll('[data-coupon]').forEach((button) => {
    button.addEventListener('click', async () => {
      const code = button.dataset.coupon || '';
      if (!code) return;

      try {
        await navigator.clipboard.writeText(code);
        const original = button.textContent;
        button.textContent = 'コピーしました';
        window.setTimeout(() => button.textContent = original, 1600);
      } catch {
        window.prompt('クーポンコードをコピーしてください', code);
      }
    });
  });
})();
