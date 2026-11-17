// Language switching. English is pre-rendered; Greek (or English again) is applied in place.
import { strings } from './data.js';
import { renderWorkList, renderProject, pageTitle, pageDescription, LANGS } from './render.js';

const KEY = 'vd-lang';

export function initialLang() {
  const param = new URLSearchParams(location.search).get('lang');
  if (LANGS.includes(param)) return param;
  try {
    const saved = localStorage.getItem(KEY);
    if (LANGS.includes(saved)) return saved;
  } catch (e) { /* storage blocked */ }
  return (navigator.language || '').toLowerCase().startsWith('el') ? 'el' : 'en';
}

export let currentLang = 'en';

export function applyLang(lang, { initial = false } = {}) {
  const dict = strings[lang];
  const html = document.documentElement;
  const changed = lang !== currentLang;
  currentLang = lang;
  html.lang = lang;
  try { localStorage.setItem(KEY, lang); } catch (e) { /* storage blocked */ }

  // On first load in English the pre-rendered markup is already correct.
  if (initial && lang === 'en') return;

  document.querySelectorAll('[data-i18n]').forEach((el) => {
    const v = dict[el.dataset.i18n];
    if (v != null) el.textContent = v;
  });
  document.querySelectorAll('[data-i18n-aria-label]').forEach((el) => {
    const v = dict[el.dataset.i18nAriaLabel];
    if (v != null) el.setAttribute('aria-label', v);
  });

  const slug = document.body.dataset.slug || '';
  const list = document.querySelector('[data-render="worklist"]');
  if (list) list.innerHTML = renderWorkList(lang);

  const caseEl = document.querySelector('[data-project]');
  if (caseEl) {
    const tmp = document.createElement('div');
    tmp.innerHTML = renderProject(slug, lang, '../');
    const fresh = tmp.firstElementChild;
    // Keep the intro state of the title and poster if they are mid-animation
    caseEl.replaceWith(fresh);
  }

  document.title = pageTitle(slug, lang);
  const meta = document.querySelector('meta[name="description"]');
  if (meta) meta.setAttribute('content', pageDescription(slug, lang));

  if (changed || initial) document.dispatchEvent(new CustomEvent('langchange', { detail: { lang, initial } }));
}

export function initI18n() {
  applyLang(initialLang(), { initial: true });
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-lang-toggle]');
    if (!btn) return;
    applyLang(currentLang === 'en' ? 'el' : 'en');
  });
}
