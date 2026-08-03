(() => {
  'use strict';

  const body = document.body;
  const menuToggle = document.querySelector('[data-menu-toggle]');
  const mobileMenu = document.querySelector('[data-mobile-menu]');
  const searchDrawer = document.querySelector('[data-search-drawer]');
  const searchOpen = document.querySelector('[data-search-open]');
  const searchCloseButtons = document.querySelectorAll('[data-search-close]');

  const closeMobileMenu = () => {
    if (!menuToggle || !mobileMenu) return;
    menuToggle.setAttribute('aria-expanded', 'false');
    mobileMenu.hidden = true;
    body.classList.remove('menu-open');
  };

  if (menuToggle && mobileMenu) {
    menuToggle.addEventListener('click', () => {
      const expanded = menuToggle.getAttribute('aria-expanded') === 'true';
      menuToggle.setAttribute('aria-expanded', String(!expanded));
      mobileMenu.hidden = expanded;
      body.classList.toggle('menu-open', !expanded);
    });
  }

  const openSearch = () => {
    if (!searchDrawer) return;
    searchDrawer.hidden = false;
    body.classList.add('search-open');
    window.setTimeout(() => {
      const input = searchDrawer.querySelector('input[type="search"]');
      if (input) input.focus();
    }, 50);
  };

  const closeSearch = () => {
    if (!searchDrawer) return;
    searchDrawer.hidden = true;
    body.classList.remove('search-open');
  };

  if (searchOpen) searchOpen.addEventListener('click', openSearch);
  searchCloseButtons.forEach((button) => button.addEventListener('click', closeSearch));

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      closeSearch();
      closeMobileMenu();
    }
  });

  window.addEventListener('resize', () => {
    if (window.innerWidth >= 1024) closeMobileMenu();
  });
})();
