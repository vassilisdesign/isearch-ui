// Motion system. One orchestrated intro per page, a scroll-velocity "wind" on the wordmark,
// list rules that draw in, a cursor-following poster on fine pointers, and a wipe between pages.
// Everything is transform/opacity based and fully skipped for prefers-reduced-motion.
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import Lenis from 'lenis';
import { projects } from './data.js';
import { poster } from './posters.js';

gsap.registerPlugin(ScrollTrigger);

const html = document.documentElement;
const reduce = window.matchMedia('(prefers-reduced-motion: reduce)');
const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)');
const WIPE_KEY = 'ostria-wipe';
let lenis = null;

const clearWipeFlag = () => { try { sessionStorage.removeItem(WIPE_KEY); } catch (e) { /* blocked */ } };
const endIntro = () => html.classList.remove('intro');

export function initMotion() {
  clearWipeFlag();
  initAnchors();
  if (reduce.matches) {
    html.classList.remove('intro', 'is-wiping');
    return;
  }

  if (finePointer.matches) initSmoothScroll();

  const tl = gsap.timeline({ delay: 0.05 });
  let introAt = 0;
  if (html.classList.contains('is-wiping')) {
    tl.to('.wipe', {
      scaleY: 0, transformOrigin: '50% 0%', duration: 0.75, ease: 'expo.inOut',
      onComplete: () => { html.classList.remove('is-wiping'); gsap.set('.wipe', { clearProps: 'all' }); }
    });
    introAt = 0.3;
  }
  if (document.querySelector('.hero')) tl.add(homeIntro(), introAt);
  else if (document.querySelector('.case')) tl.add(caseIntro(), introAt);
  else endIntro();

  initWind();
  initRows();
  initPreview();
  initPageTransitions();

  document.addEventListener('langchange', () => ScrollTrigger.refresh());
  window.addEventListener('pageshow', (e) => {
    if (!e.persisted) return; // returning via back/forward cache: never show a covered page
    gsap.set('.wipe', { clearProps: 'all' });
    html.classList.remove('is-wiping', 'intro');
  });
}

/* ---------- Intros ---------- */

function homeIntro() {
  const targets = ['.grid-lines span', '.wordmark .ch', '.hero-meta > *'];
  return gsap.timeline({
    onComplete() { endIntro(); gsap.set(targets, { clearProps: 'transform,opacity,visibility' }); }
  })
    .fromTo('.grid-lines span', { scaleY: 0 }, { scaleY: 1, duration: 1.2, ease: 'expo.inOut', stagger: 0.035 })
    .fromTo('.wordmark .ch', { yPercent: 105 }, { yPercent: 0, duration: 1.3, ease: 'expo.out', stagger: 0.065 }, 0.35)
    .fromTo('.wordmark-word', { '--wght': 120 }, { '--wght': 500, duration: 1.9, ease: 'power3.out' }, 0.35)
    .fromTo('.hero-meta > *', { autoAlpha: 0, y: 14 }, { autoAlpha: 1, y: 0, duration: 0.9, ease: 'power3.out', stagger: 0.08 }, 1);
}

function caseIntro() {
  return gsap.timeline({
    onComplete() {
      endIntro();
      gsap.set(['.case-title .line > span', '.case-poster', '.case-poster .poster', '.case-meta > div'], { clearProps: 'transform,clipPath,opacity,visibility' });
    }
  })
    .fromTo('.case-title .line > span', { yPercent: 105 }, { yPercent: 0, duration: 1.2, ease: 'expo.out' })
    .fromTo('.case-meta > div', { autoAlpha: 0 }, { autoAlpha: 1, duration: 0.6, stagger: 0.06 }, 0.4)
    .fromTo('.case-poster', { clipPath: 'inset(100% 0% 0% 0%)' }, { clipPath: 'inset(0% 0% 0% 0%)', duration: 1.3, ease: 'expo.inOut' }, 0.2)
    .fromTo('.case-poster .poster', { scale: 1.15 }, { scale: 1, duration: 1.8, ease: 'expo.out' }, 0.2);
}

/* ---------- Wordmark wind: shear follows scroll speed, then settles ---------- */

function initWind() {
  const wm = document.querySelector('.wordmark');
  if (!wm) return;
  const skew = gsap.quickTo(wm, 'skewX', { duration: 0.7, ease: 'power3.out' });
  let settle;
  ScrollTrigger.create({
    trigger: '.hero', start: 'top top', end: 'bottom top',
    onUpdate(self) {
      skew(gsap.utils.clamp(-14, 14, self.getVelocity() / -220));
      settle && settle.kill();
      settle = gsap.delayedCall(0.12, () => skew(0));
    }
  });
  gsap.to('.wordmark-mask', {
    xPercent: -7, ease: 'none',
    scrollTrigger: { trigger: '.hero', start: 'top top', end: 'bottom top', scrub: true }
  });
}

/* ---------- Work list rules draw in once ---------- */

function initRows() {
  const rows = gsap.utils.toArray('.work-row');
  if (!rows.length) return;
  gsap.set(rows, { '--s': 0 });
  ScrollTrigger.batch(rows, {
    start: 'top 92%', once: true,
    onEnter: (batch) => gsap.to(batch, { '--s': 1, duration: 1.1, ease: 'expo.inOut', stagger: 0.08 })
  });
}

/* ---------- Poster follows the cursor over the work list ---------- */

function initPreview() {
  const list = document.querySelector('[data-render="worklist"]');
  if (!list || !finePointer.matches) return;

  const el = document.createElement('div');
  el.className = 'preview';
  el.setAttribute('aria-hidden', 'true');
  document.body.appendChild(el);
  gsap.set(el, { xPercent: -50, yPercent: -50, scale: 0.8, autoAlpha: 0 });

  const xTo = gsap.quickTo(el, 'x', { duration: 0.6, ease: 'power3' });
  const yTo = gsap.quickTo(el, 'y', { duration: 0.6, ease: 'power3' });
  const rTo = gsap.quickTo(el, 'rotation', { duration: 0.9, ease: 'power3' });
  const cache = {};
  let current = null;
  let lastX = 0;
  let settle;

  const show = (slug) => {
    if (slug === current) return;
    current = slug;
    const p = projects.find((x) => x.slug === slug);
    el.innerHTML = cache[slug] || (cache[slug] = poster(p, { w: 400, h: 500 }));
    gsap.fromTo(el.firstElementChild, { scale: 1.18 }, { scale: 1, duration: 0.7, ease: 'power3.out' });
  };

  list.addEventListener('pointerenter', (e) => {
    gsap.set(el, { x: e.clientX, y: e.clientY });
    lastX = e.clientX;
    gsap.to(el, { autoAlpha: 1, scale: 1, duration: 0.45, ease: 'power3.out' });
  });
  list.addEventListener('pointermove', (e) => {
    xTo(e.clientX); yTo(e.clientY);
    rTo(gsap.utils.clamp(-7, 7, (e.clientX - lastX) * 0.35));
    lastX = e.clientX;
    settle && settle.kill();
    settle = gsap.delayedCall(0.1, () => rTo(0));
    const link = e.target.closest('[data-slug]');
    if (link) show(link.dataset.slug);
  });
  list.addEventListener('pointerleave', () => {
    current = null;
    gsap.to(el, { autoAlpha: 0, scale: 0.8, duration: 0.3, ease: 'power2.in' });
  });
}

/* ---------- Smooth scroll (mouse/trackpad only) ---------- */

function initSmoothScroll() {
  lenis = new Lenis({ lerp: 0.1 });
  lenis.on('scroll', ScrollTrigger.update);
  gsap.ticker.add((t) => lenis.raf(t * 1000));
  gsap.ticker.lagSmoothing(0);
}

/* ---------- Links ---------- */

const normPath = (p) => p.replace(/index\.html$/, '');

function initAnchors() {
  document.addEventListener('click', (e) => {
    const a = e.target.closest('a[href*="#"]');
    if (!a || e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
    const url = new URL(a.href, location.href);
    if (url.origin !== location.origin || normPath(url.pathname) !== normPath(location.pathname) || !url.hash) return;
    const target = document.getElementById(decodeURIComponent(url.hash.slice(1)));
    if (!target) return;
    e.preventDefault();
    if (lenis) lenis.scrollTo(target, { duration: 1.4 });
    else target.scrollIntoView({ behavior: reduce.matches ? 'auto' : 'smooth' });
    history.replaceState(null, '', url.hash === '#top' ? location.pathname : url.hash);
    if (!target.hasAttribute('tabindex')) target.setAttribute('tabindex', '-1');
    target.focus({ preventScroll: true });
  });
}

function initPageTransitions() {
  document.addEventListener('click', (e) => {
    const a = e.target.closest('a[href]');
    if (!a || e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
    if ((a.target && a.target !== '_self') || a.hasAttribute('download')) return;
    const url = new URL(a.href, location.href);
    if (url.origin !== location.origin || !/^https?:$/.test(url.protocol)) return;
    if (normPath(url.pathname) === normPath(location.pathname)) return; // same page: handled by anchors
    e.preventDefault();
    try { sessionStorage.setItem(WIPE_KEY, '1'); } catch (err) { /* blocked: page still navigates */ }
    gsap.fromTo('.wipe', { scaleY: 0, transformOrigin: '50% 100%' }, {
      scaleY: 1, duration: 0.6, ease: 'expo.inOut',
      onComplete: () => { location.href = url.href; }
    });
  });
}
