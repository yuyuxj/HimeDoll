(() => {
  'use strict';

  const ageGate = document.querySelector('[data-age-gate]');
  const ageAccept = document.querySelector('[data-age-accept]');

  if (ageGate && localStorage.getItem('hd_age_confirmed') !== '1') {
    ageGate.hidden = false;
    document.body.classList.add('modal-open');
  }

  if (ageAccept) {
    ageAccept.addEventListener('click', () => {
      localStorage.setItem('hd_age_confirmed', '1');
      ageGate.hidden = true;
      document.body.classList.remove('modal-open');
    });
  }

  const cookieBar = document.querySelector('[data-cookie-bar]');
  const cookieAccept = document.querySelector('[data-cookie-accept]');

  if (cookieBar && localStorage.getItem('hd_cookie_consent') !== '1') {
    cookieBar.hidden = false;
  }

  if (cookieAccept) {
    cookieAccept.addEventListener('click', () => {
      localStorage.setItem('hd_cookie_consent', '1');
      cookieBar.hidden = true;
    });
  }
})();
