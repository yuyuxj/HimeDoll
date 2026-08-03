(() => {
  'use strict';

  const toggle = document.querySelector('[data-menu-toggle]');
  const menu = document.querySelector('[data-mobile-menu]');

  if (!toggle || !menu) return;

  toggle.addEventListener('click', () => {
    const expanded = toggle.getAttribute('aria-expanded') === 'true';
    toggle.setAttribute('aria-expanded', String(!expanded));
    menu.hidden = expanded;
    document.body.classList.toggle('menu-open', !expanded);
  });

  window.addEventListener('resize', () => {
    if (window.innerWidth >= 1024) {
      toggle.setAttribute('aria-expanded', 'false');
      menu.hidden = true;
      document.body.classList.remove('menu-open');
    }
  });
})();
