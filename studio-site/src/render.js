// Shared HTML rendering. Runs at build time (Vite plugin) to pre-render English pages,
// and in the browser to re-render when the language changes. No DOM access here.
import { strings, projects, studio } from './data.js';
import { poster } from './posters.js';

export const LANGS = ['en', 'el'];

export const esc = (s) =>
  String(s).replace(/[&<>"]/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' })[c]);

export const str = (key, lang = 'en') => strings[lang][key] ?? strings.en[key] ?? key;

/** Text node that the browser can swap: <tag data-i18n="key">text</tag> */
const tx = (key, lang, tag = 'span', attrs = '') =>
  `<${tag} data-i18n="${key}"${attrs ? ' ' + attrs : ''}>${esc(str(key, lang))}</${tag}>`;

export const findProject = (slug) => projects.find((p) => p.slug === slug);

/* ---------- Chrome ---------- */

export function renderHeader(root = '', lang = 'en') {
  return `
<a class="skip" href="#main" data-i18n="a11y.skip">${esc(str('a11y.skip', lang))}</a>
<header class="site-header">
  <div class="container header-inner">
    <a class="brand" href="${root}index.html" data-i18n-aria-label="a11y.home" aria-label="${esc(str('a11y.home', lang))}">Ostria</a>
    <nav class="nav" data-i18n-aria-label="nav.label" aria-label="${esc(str('nav.label', lang))}">
      <a href="${root}index.html#work">${tx('nav.work', lang)}</a>
      <a href="${root}index.html#studio">${tx('nav.studio', lang)}</a>
      <a href="${root}index.html#contact">${tx('nav.contact', lang)}</a>
    </nav>
    <p class="clock">${tx('clock.label', lang)} <time data-clock>--:--</time></p>
    <button class="lang" type="button" data-lang-toggle data-i18n-aria-label="lang.switch" aria-label="${esc(str('lang.switch', lang))}">
      <span data-i18n="lang.short">${esc(str('lang.short', lang))}</span>
    </button>
  </div>
</header>`;
}

export function renderFooter(root = '', lang = 'en') {
  return `
<footer class="site-footer">
  <div class="container grid">
    <a class="footer-mark" href="${root}index.html" data-i18n-aria-label="a11y.home" aria-label="${esc(str('a11y.home', lang))}">Ostria</a>
    <p class="footer-address" data-i18n="footer.address">${esc(str('footer.address', lang))}</p>
    <p class="footer-contact">
      <a href="mailto:${studio.email}">${studio.email}</a><br>
      <a href="tel:${studio.phone.replace(/\s/g, '')}">${studio.phone}</a>
    </p>
    <p class="footer-social">
      <a href="${studio.instagram}" rel="noopener">Instagram</a><br>
      <a href="${studio.linkedin}" rel="noopener">LinkedIn</a>
    </p>
    <p class="footer-rights">${tx('footer.rights', lang)}</p>
    <a class="footer-top" href="#top" data-i18n="footer.top">${esc(str('footer.top', lang))}</a>
  </div>
</footer>
<div class="wipe" aria-hidden="true"></div>`;
}

/* ---------- Home ---------- */

export function renderWorkList(lang = 'en', root = '') {
  const rows = projects
    .map((p) => {
      const c = p[lang];
      return `
    <li class="work-row">
      <a class="work-link" href="${root}work/${p.slug}.html" data-slug="${p.slug}">
        <span class="work-thumb">${poster(p, { w: 160, h: 200 })}</span>
        <span class="work-client">${esc(p.client)}</span>
        <span class="work-services">${esc(c.services)}</span>
        <span class="work-sector">${esc(c.sector)}</span>
        <span class="work-year">${p.year}</span>
      </a>
    </li>`;
    })
    .join('');
  return `<ul class="work-list" role="list">${rows}</ul>`;
}

export function renderHome(lang = 'en') {
  const grid = Array.from({ length: 12 }, () => '<span></span>').join('');
  const steps = [1, 2, 3, 4]
    .map(
      (n) => `
        <li class="step">
          <span class="step-n" aria-hidden="true">${n}</span>
          ${tx(`approach.${n}.title`, lang, 'h4', 'class="step-title"')}
          ${tx(`approach.${n}.text`, lang, 'p', 'class="step-text"')}
        </li>`
    )
    .join('');
  const budgets = [0, 1, 2, 3, 4]
    .map((n) => `<option value="${esc(str(`form.budget.${n}`, 'en'))}" data-i18n="form.budget.${n}">${esc(str(`form.budget.${n}`, lang))}</option>`)
    .join('');

  return `
<section class="hero" aria-labelledby="wordmark">
  <div class="grid-lines container" aria-hidden="true"><div class="grid">${grid}</div></div>
  <div class="container hero-inner">
    <h1 class="wordmark" id="wordmark" data-i18n-aria-label="a11y.wordmark" aria-label="${esc(str('a11y.wordmark', lang))}">
      <span class="wordmark-mask" aria-hidden="true"><span class="wordmark-word">${'Ostria'.split('').map((ch) => `<span class="ch">${ch}</span>`).join('')}</span></span>
    </h1>
    <div class="grid hero-meta">
      ${tx('hero.lead', lang, 'p', 'class="hero-lead"')}
      ${tx('hero.services', lang, 'p', 'class="hero-services"')}
      ${tx('hero.place', lang, 'p', 'class="hero-place"')}
    </div>
  </div>
</section>

<section class="section work" id="work" aria-labelledby="work-heading">
  <div class="container">
    <div class="grid section-head">
      ${tx('work.heading', lang, 'h2', 'class="section-title" id="work-heading"')}
    </div>
    <div class="work-cols" aria-hidden="true">
      ${tx('work.client', lang)}${tx('work.services', lang)}${tx('work.sector', lang)}${tx('work.year', lang)}
    </div>
    <div data-render="worklist">${renderWorkList(lang)}</div>
  </div>
</section>

<section class="section studio" id="studio" aria-labelledby="studio-heading">
  <div class="container grid">
    ${tx('studio.heading', lang, 'h2', 'class="section-title" id="studio-heading"')}
    <div class="studio-copy">
      ${tx('studio.p1', lang, 'p', 'class="studio-big"')}
      ${tx('studio.p2', lang, 'p')}
      ${tx('studio.clients', lang, 'p')}
    </div>
  </div>
  <div class="container grid approach">
    ${tx('approach.heading', lang, 'h3', 'class="approach-title"')}
    <ol class="steps" role="list">${steps}</ol>
  </div>
</section>

<section class="section contact" id="contact" aria-labelledby="contact-heading">
  <div class="container grid">
    <div class="contact-intro">
      ${tx('contact.heading', lang, 'h2', 'class="contact-title" id="contact-heading"')}
      ${tx('contact.lead', lang, 'p', 'class="contact-lead"')}
      <p class="contact-direct">${tx('contact.or', lang)}<br><a class="contact-email" href="mailto:${studio.email}">${studio.email}</a></p>
    </div>
    <form class="form" data-contact novalidate>
      <div class="field">
        <label for="f-name" data-i18n="form.name">${esc(str('form.name', lang))}</label>
        <input id="f-name" name="name" type="text" autocomplete="name" required aria-describedby="f-name-err">
        <p class="field-error" id="f-name-err" data-error-for="name"></p>
      </div>
      <div class="field">
        <label for="f-email" data-i18n="form.email">${esc(str('form.email', lang))}</label>
        <input id="f-email" name="email" type="email" autocomplete="email" inputmode="email" required aria-describedby="f-email-err">
        <p class="field-error" id="f-email-err" data-error-for="email"></p>
      </div>
      <div class="field">
        <label for="f-company" data-i18n="form.company">${esc(str('form.company', lang))}</label>
        <input id="f-company" name="company" type="text" autocomplete="organization">
      </div>
      <div class="field">
        <label for="f-budget" data-i18n="form.budget">${esc(str('form.budget', lang))}</label>
        <div class="select"><select id="f-budget" name="budget">${budgets}</select></div>
      </div>
      <div class="field field-wide">
        <label for="f-message" data-i18n="form.message">${esc(str('form.message', lang))}</label>
        <p class="field-hint" id="f-message-hint" data-i18n="form.message.hint">${esc(str('form.message.hint', lang))}</p>
        <textarea id="f-message" name="message" rows="5" required aria-describedby="f-message-hint f-message-err"></textarea>
        <p class="field-error" id="f-message-err" data-error-for="message"></p>
      </div>
      <div class="hp" aria-hidden="true"><label for="f-gotcha">Leave empty</label><input id="f-gotcha" name="_gotcha" tabindex="-1" autocomplete="off"></div>
      <div class="form-foot field-wide">
        <button class="button" type="submit"><span data-i18n="form.submit">${esc(str('form.submit', lang))}</span></button>
        <p class="form-status" role="status" aria-live="polite" data-form-status></p>
      </div>
    </form>
  </div>
</section>`;
}

/* ---------- Case study ---------- */

export function renderProject(slug, lang = 'en', root = '../') {
  const p = findProject(slug);
  const c = p[lang];
  const i = projects.indexOf(p);
  const next = projects[(i + 1) % projects.length];
  const L = (k) => esc(str(k, lang));

  const block = (key, text) => `
  <section class="case-block container grid">
    <h2 class="case-label">${L(key)}</h2>
    <p class="case-text">${esc(text)}</p>
  </section>`;

  return `
<article class="case" data-project="${p.slug}">
  <header class="case-head container grid">
    <a class="case-back" href="${root}index.html#work">${L('case.all')}</a>
    <p class="case-client">${esc(p.client)}</p>
    <h1 class="case-title"><span class="line"><span>${esc(c.title)}</span></span></h1>
    <dl class="case-meta">
      <div><dt>${L('case.client')}</dt><dd>${esc(p.client)}</dd></div>
      <div><dt>${L('case.services')}</dt><dd>${esc(c.services)}</dd></div>
      <div><dt>${L('case.sector')}</dt><dd>${esc(c.sector)}</dd></div>
      <div><dt>${L('case.year')}</dt><dd>${p.year}</dd></div>
    </dl>
  </header>

  <figure class="case-poster container">${poster(p, { w: 1600, h: 900, label: `${p.client}: ${c.title}` })}</figure>

  ${block('case.brief', c.brief)}

  <div class="case-pair container">
    <figure>${poster(p, { w: 800, h: 1000, variant: 1 })}</figure>
    <figure>${poster(p, { w: 800, h: 1000, variant: 2 })}</figure>
  </div>

  ${block('case.work', c.work)}

  <section class="case-block container grid">
    <h2 class="case-label">${L('case.result')}</h2>
    <div class="case-text">
      <p>${esc(c.result)}</p>
      <ul class="case-facts" role="list">${c.facts.map((f) => `<li>${esc(f)}</li>`).join('')}</ul>
    </div>
  </section>

  <nav class="case-next container" aria-label="${L('case.next')}">
    <a href="${root}work/${next.slug}.html" class="case-next-link">
      <span class="case-next-label">${L('case.next')}</span>
      <span class="case-next-client">${esc(next.client)}</span>
      <span class="case-next-thumb">${poster(next, { w: 400, h: 500 })}</span>
    </a>
  </nav>
</article>`;
}

export function pageTitle(slug, lang = 'en') {
  if (!slug) return str('meta.title', lang);
  const p = findProject(slug);
  return `${p.client}: ${p[lang].title} — Ostria`;
}

export function pageDescription(slug, lang = 'en') {
  if (!slug) return str('meta.description', lang);
  return findProject(slug)[lang].brief;
}
