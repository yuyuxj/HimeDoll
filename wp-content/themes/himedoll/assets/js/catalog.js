(() => {
'use strict';
const toggle=document.querySelector('[data-filter-toggle]');
const panel=document.querySelector('[data-filter-panel]');
const close=document.querySelector('[data-filter-close]');
if(!toggle||!panel)return;
const open=()=>{panel.classList.add('is-open');document.body.classList.add('filter-open');};
const shut=()=>{panel.classList.remove('is-open');document.body.classList.remove('filter-open');};
toggle.addEventListener('click',open);
if(close)close.addEventListener('click',shut);
document.addEventListener('keydown',e=>{if(e.key==='Escape')shut();});
})();
