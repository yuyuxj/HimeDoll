(() => {
  'use strict';

  const key = 'hd_compare_products';
  const getItems = () => {
    try { return JSON.parse(localStorage.getItem(key) || '[]'); }
    catch { return []; }
  };
  const saveItems = (items) => localStorage.setItem(key, JSON.stringify(items.slice(0, 3)));

  const renderDrawer = () => {
    const drawer = document.querySelector('[data-compare-drawer]');
    if (!drawer) return;

    const items = getItems();
    drawer.hidden = items.length === 0;

    const count = drawer.querySelector('[data-compare-count]');
    const box = drawer.querySelector('[data-compare-items]');

    if (count) count.textContent = `${items.length}/3`;
    if (box) {
      box.innerHTML = items.map(item => `<span>${item.name}</span>`).join('');
    }
  };

  document.querySelectorAll('[data-compare-product]').forEach(button => {
    button.addEventListener('click', () => {
      const id = Number(button.dataset.productId);
      const name = button.dataset.productName || '';
      let items = getItems();

      if (items.some(item => item.id === id)) {
        items = items.filter(item => item.id !== id);
        button.textContent = '商品を比較に追加';
      } else {
        if (items.length >= 3) {
          alert('比較できる商品は最大3件です。');
          return;
        }
        items.push({id, name});
        button.textContent = '比較から外す';
      }

      saveItems(items);
      renderDrawer();
    });
  });

  const table = document.querySelector('[data-compare-table]');
  if (table) {
    const items = getItems();
    table.innerHTML = items.length
      ? items.map(item => `<article><h2>${item.name}</h2><p>商品ID: ${item.id}</p><a href="/?p=${item.id}">商品を見る</a></article>`).join('')
      : '<p>比較商品がありません。</p>';
  }

  renderDrawer();

  const deadline = document.querySelector('[data-promo-deadline]');
  if (deadline) {
    const end = new Date(deadline.dataset.promoDeadline).getTime();
    const output = deadline.querySelector('[data-promo-countdown]');

    const tick = () => {
      const diff = end - Date.now();
      if (diff <= 0) {
        output.textContent = '終了しました';
        return;
      }
      const days = Math.floor(diff / 86400000);
      const hours = Math.floor((diff % 86400000) / 3600000);
      const mins = Math.floor((diff % 3600000) / 60000);
      output.textContent = `${days}日 ${hours}時間 ${mins}分`;
      setTimeout(tick, 60000);
    };
    tick();
  }
})();
