/*
 * VASSILISDESIGN motion: gentle image parallax, Work tiles and VD Hero image that reveal once in view,
 * and a red wipe between pages. The page intro runs in CSS (see the inline script in <head>).
 * Transform/opacity/clip-path only; skipped entirely for prefers-reduced-motion and in the Elementor editor.
 */
(function () {
	'use strict';

	var w = window;
	var d = document;
	var html = d.documentElement;
	w.__vd = true; // tells the inline failsafe that scripts are running

	var WIPE_KEY = 'vd-wipe';
	var reduce = w.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var inEditor = d.body.classList.contains('elementor-editor-active') || /elementor-preview/.test(location.search);

	function clearWipeFlag() {
		try { sessionStorage.removeItem(WIPE_KEY); } catch (e) { /* storage blocked */ }
	}
	function $$(sel) { return Array.prototype.slice.call(d.querySelectorAll(sel)); }

	/* ---------- Clock ---------- */
	function initClock() {
		var els = $$('[data-vd-clock]');
		if (!els.length || !w.Intl) return;
		function tick() {
			var now = new Date();
			els.forEach(function (el) {
				try {
					el.textContent = new Intl.DateTimeFormat(html.lang || 'en', { timeZone: el.getAttribute('data-tz') || 'Europe/Athens', hour: '2-digit', minute: '2-digit', hour12: false }).format(now);
				} catch (e) { el.textContent = ''; }
				el.setAttribute('datetime', now.toISOString());
			});
		}
		tick();
		setTimeout(function () { tick(); setInterval(tick, 60000); }, (60 - new Date().getSeconds()) * 1000);
	}

	initClock();
	clearWipeFlag();

	// The page intro itself runs in CSS (started by the inline script in <head>); this file adds the
	// scroll effects, the reveals and the page wipe. Without the motion libraries, just show everything.
	if (reduce || inEditor || !w.gsap || !w.ScrollTrigger) {
		html.classList.remove('vd-motion');
		return;
	}

	var gsap = w.gsap;
	gsap.registerPlugin(w.ScrollTrigger);

	/* ---------- Scroll ---------- */
	function initParallax() {
		if (d.querySelector('.vd-hero')) {
			var st = { trigger: '.vd-hero', start: 'top top', end: 'bottom top', scrub: true };
			gsap.to('.vd-hero-photo', { yPercent: 12, ease: 'none', scrollTrigger: st });
			gsap.to('.vd-hero-band', { xPercent: 6, ease: 'none', scrollTrigger: { trigger: '.vd-hero', start: 'top top', end: 'bottom top', scrub: true } });
		}
		$$('.vd-studio-photo > .vd-photo-img, .vd-studio-photo > .vd-photo-bg').forEach(function (el) {
			gsap.fromTo(el, { yPercent: -5 }, { yPercent: 5, ease: 'none', scrollTrigger: { trigger: el.parentElement, start: 'top bottom', end: 'bottom top', scrub: true } });
		});
	}

	// Tiles are hidden by CSS from the first paint (html.vd-motion), so nothing flashes before they rise.
	function initTiles() {
		var tiles = $$('.vd-tile-media');
		if (!tiles.length) return;
		w.ScrollTrigger.batch(tiles, {
			start: 'top 92%',
			once: true,
			onEnter: function (batch) {
				gsap.fromTo(batch, { clipPath: 'inset(100% 0% 0% 0%)' }, {
					clipPath: 'inset(0% 0% 0% 0%)', duration: 1, ease: 'expo.inOut', stagger: 0.07,
					onComplete: function () { batch.forEach(function (t) { t.classList.add('vd-in'); }); gsap.set(batch, { clearProps: 'clipPath' }); }
				});
			}
		});
	}

	/* ---------- VD Hero image: reveal when in view, optional scroll movement ---------- */
	function initHeroImages() {
		$$('.vd-hero-image').forEach(function (el) {
			var band = el.querySelector('.vd-hi-band'), photo = el.querySelector('.vd-hi-photo'), img = photo ? photo.firstElementChild : null, cta = el.querySelector('.vd-hi-cta');
			// The starting state comes from CSS (html.vd-motion), so the reveal never flashes.
			if (el.getAttribute('data-vd-motion') === 'yes') {
				var play = function () {
					var tl = gsap.timeline({ onComplete: function () { el.classList.add('vd-in'); gsap.set([band, photo, img, cta].filter(Boolean), { clearProps: 'transform,clipPath' }); } })
						.fromTo(band, { scaleX: 0 }, { scaleX: 1, duration: 1.1, ease: 'expo.inOut' });
					if (photo) tl.fromTo(photo, { clipPath: 'inset(100% 0% 0% 0%)' }, { clipPath: 'inset(0% 0% 0% 0%)', duration: 1.2, ease: 'expo.inOut' }, 0.12);
					if (img) tl.fromTo(img, { scale: 1.15 }, { scale: 1, duration: 1.6, ease: 'expo.out' }, 0.12);
					if (cta) tl.fromTo(cta, { scale: 0 }, { scale: 1, duration: 0.6, ease: 'back.out(1.6)' }, 0.8);
				};
				w.ScrollTrigger.create({ trigger: el, start: 'top 88%', once: true, onEnter: play });
			} else {
				el.classList.add('vd-in');
			}
			if (el.getAttribute('data-vd-parallax') === 'yes' && photo) {
				var st = { trigger: el, start: 'top bottom', end: 'bottom top', scrub: true };
				gsap.fromTo(photo, { yPercent: -6 }, { yPercent: 6, ease: 'none', scrollTrigger: st });
				gsap.fromTo(band, { xPercent: -3 }, { xPercent: 3, ease: 'none', scrollTrigger: { trigger: el, start: 'top bottom', end: 'bottom top', scrub: true } });
			}
		});
	}

	/* ---------- Page wipe between internal pages ---------- */
	function samePage(url) {
		return url.pathname.replace(/index\.php$/, '') === location.pathname.replace(/index\.php$/, '') && url.search === location.search;
	}

	function initPageTransitions() {
		d.addEventListener('click', function (e) {
			var a = e.target.closest ? e.target.closest('a[href]') : null;
			if (!a || e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
			if ((a.target && a.target !== '_self') || a.hasAttribute('download') || a.closest('#wpadminbar, .cmplz-cookiebanner')) return;
			var url;
			try { url = new URL(a.href, location.href); } catch (err) { return; }
			if (url.origin !== location.origin || !/^https?:$/.test(url.protocol)) return;
			if (/\/wp-(admin|login)|\.(pdf|zip|jpe?g|png|webp|svg)$/i.test(url.pathname)) return;
			if (samePage(url)) return; // in-page anchors scroll natively
			e.preventDefault();
			try { sessionStorage.setItem(WIPE_KEY, '1'); } catch (err) { /* navigation still happens */ }
			gsap.fromTo('.vd-wipe', { scaleY: 0, transformOrigin: '50% 100%' }, {
				scaleY: 1, duration: 0.55, ease: 'expo.inOut',
				onComplete: function () { location.href = url.href; }
			});
		});
		w.addEventListener('pageshow', function (e) {
			if (!e.persisted) return;
			gsap.set('.vd-wipe', { clearProps: 'all' });
			html.classList.remove('vd-is-wiping', 'vd-intro');
		});
	}

	/* ---------- Start ---------- */
	function start() {
		initHeroImages();
		initParallax();
		initTiles();
		initPageTransitions();
	}

	try { start(); } catch (err) { html.classList.remove('vd-motion'); }
})();
