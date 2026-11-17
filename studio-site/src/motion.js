// Motion system. One orchestrated intro per page (red band sweeps in, image rises, wordmark sets),
// gentle scroll parallax on images, work tiles that rise once, and a red wipe between pages.
// Everything is transform/opacity based and fully skipped for prefers-reduced-motion.
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import Lenis from 'lenis';

gsap.registerPlugin(ScrollTrigger);

const html = document.documentElement;
const reduce = window.matchMedia('(prefers-reduced-motion: reduce)');
const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)');
const WIPE_KEY = 'vd-wipe';
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

  initParallax();
  initTiles();
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
  const targets = ['.wordmark .ch', '.hero-band', '.hero-photo', '.hero-photo > *', '.hero-cta', '.hero-lead', '.hero-meta', '.hero-vertical', '.hero-since'];
  return gsap.timeline({
    onComplete() { endIntro(); gsap.set(targets, { clearProps: 'transform,clipPath,opacity,visibility' }); }
  })
    .fromTo('.hero-band', { scaleX: 0 }, { scaleX: 1, duration: 1.1, ease: 'expo.inOut' })
    .fromTo('.hero-photo', { clipPath: 'inset(100% 0% 0% 0%)' }, { clipPath: 'inset(0% 0% 0% 0%)', duration: 1.3, ease: 'expo.inOut' }, 0.15)
    .fromTo('.hero-photo > *', { scale: 1.2 }, { scale: 1, duration: 1.8, ease: 'expo.out' }, 0.15)
    .fromTo('.wordmark .ch', { yPercent: 105 }, { yPercent: 0, duration: 1.2, ease: 'expo.out', stagger: 0.055 }, 0.45)
    .fromTo('.hero-cta', { scale: 0 }, { scale: 1, duration: 0.7, ease: 'back.out(1.6)' }, 1.1)
    .fromTo(['.hero-meta', '.hero-vertical', '.hero-lead', '.hero-since'], { autoAlpha: 0 }, { autoAlpha: 1, duration: 0.8, ease: 'power2.out', stagger: 0.07 }, 1);
}

function caseIntro() {
  const targets = ['.case-title .line > span', '.case-poster', '.case-poster > :first-child', '.case-meta > div', '.case-red'];
  return gsap.timeline({
    onComplete() { endIntro(); gsap.set(targets, { clearProps: 'transform,clipPath,opacity,visibility' }); }
  })
    .fromTo('.case-title .line > span', { yPercent: 105 }, { yPercent: 0, duration: 1.2, ease: 'expo.out' })
    .fromTo('.case-red', { scaleY: 0 }, { scaleY: 1, duration: 1, ease: 'expo.inOut' }, 0.1)
    .fromTo('.case-meta > div', { autoAlpha: 0 }, { autoAlpha: 1, duration: 0.6, stagger: 0.06 }, 0.3)
    .fromTo('.case-poster', { clipPath: 'inset(100% 0% 0% 0%)' }, { clipPath: 'inset(0% 0% 0% 0%)', duration: 1.3, ease: 'expo.inOut' }, 0.25)
    .fromTo('.case-poster > :first-child', { scale: 1.15 }, { scale: 1, duration: 1.8, ease: 'expo.out' }, 0.25);
}

/* ---------- Scroll: hero image drifts inside its frame; the band slides with it ---------- */

function initParallax() {
  if (document.querySelector('.hero')) {
    const st = { trigger: '.hero', start: 'top top', end: 'bottom top', scrub: true };
    gsap.to('.hero-photo', { yPercent: 12, ease: 'none', scrollTrigger: st });
    gsap.to('.hero-band', { xPercent: 6, ease: 'none', scrollTrigger: { ...st } });
  }
  gsap.utils.toArray('.case-pair .photo > :first-child, .studio-photo > :first-child').forEach((el) => {
    gsap.fromTo(el, { yPercent: -6, scale: 1.12 }, {
      yPercent: 6, scale: 1.12, ease: 'none',
      scrollTrigger: { trigger: el.parentElement, start: 'top bottom', end: 'bottom top', scrub: true }
    });
  });
}

/* ---------- Work tiles rise once, like bars of a chart ---------- */

function initTiles() {
  const tiles = gsap.utils.toArray('.tile-media');
  if (!tiles.length) return;
  gsap.set(tiles, { clipPath: 'inset(100% 0% 0% 0%)' });
  ScrollTrigger.batch(tiles, {
    start: 'top 90%', once: true,
    onEnter: (batch) => gsap.to(batch, {
      clipPath: 'inset(0% 0% 0% 0%)', duration: 1.1, ease: 'expo.inOut', stagger: 0.08,
      onComplete() { gsap.set(this.targets(), { clearProps: 'clipPath' }); }
    })
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
