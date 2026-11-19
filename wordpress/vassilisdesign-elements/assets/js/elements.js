/*
 * VASSILISDESIGN motion. One orchestrated intro per page (red band sweeps in, image rises,
 * wordmark sets), gentle image parallax, Work tiles that rise once, and a red wipe between pages.
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
	function endIntro() { html.classList.remove('vd-intro'); }
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

	if (reduce || inEditor || !w.gsap || !w.ScrollTrigger) {
		html.classList.remove('vd-intro', 'vd-motion', 'vd-is-wiping');
		return;
	}

	var gsap = w.gsap;
	gsap.registerPlugin(w.ScrollTrigger);

	/* ---------- Intros ---------- */
	// The wordmark leads: each line rises as one piece straight away, the image and band follow, details fade in last.
	function homeIntro() {
		var targets = ['.vd-wordmark .vd-wm-in', '.vd-hero-band', '.vd-hero-photo', '.vd-hero-photo > *', '.vd-hero-cta', '.vd-hero-lead', '.vd-hero-meta', '.vd-hero-vertical', '.vd-hero-corner'];
		return gsap.timeline({ onComplete: function () { endIntro(); gsap.set(targets, { clearProps: 'transform,clipPath,opacity,visibility,willChange' }); } })
			.fromTo('.vd-wordmark .vd-wm-in', { y: 0, yPercent: 110, willChange: 'transform' }, { yPercent: 0, duration: 1.1, ease: 'expo.out', stagger: 0.12 }, 0)
			.fromTo('.vd-hero-band', { scaleX: 0 }, { scaleX: 1, duration: 1.1, ease: 'expo.inOut' }, 0.1)
			.fromTo('.vd-hero-photo', { clipPath: 'inset(100% 0% 0% 0%)' }, { clipPath: 'inset(0% 0% 0% 0%)', duration: 1.2, ease: 'expo.inOut' }, 0.2)
			.fromTo('.vd-hero-photo > *', { scale: 1.15 }, { scale: 1, duration: 1.6, ease: 'expo.out' }, 0.2)
			.fromTo(['.vd-hero-lead', '.vd-hero-meta', '.vd-hero-vertical', '.vd-hero-corner'], { autoAlpha: 0, y: 12 }, { autoAlpha: 1, y: 0, duration: 0.8, ease: 'power3.out', stagger: 0.06 }, 0.45)
			.fromTo('.vd-hero-cta', { scale: 0 }, { scale: 1, duration: 0.6, ease: 'back.out(1.6)' }, 0.85);
	}

	function caseIntro() {
		var targets = ['.vd-case-title .vd-line > span', '.vd-case-poster', '.vd-case-poster > *', '.vd-case-meta > div', '.vd-case-red'];
		var tl = gsap.timeline({ onComplete: function () { endIntro(); gsap.set(targets, { clearProps: 'transform,clipPath,opacity,visibility' }); } })
			.fromTo('.vd-case-title .vd-line > span', { y: 0, yPercent: 105 }, { yPercent: 0, duration: 1.1, ease: 'expo.out', stagger: 0.1 })
			.fromTo('.vd-case-meta > div', { autoAlpha: 0, y: 10 }, { autoAlpha: 1, y: 0, duration: 0.7, ease: 'power3.out', stagger: 0.06 }, 0.3);
		if (d.querySelector('.vd-case-red')) tl.fromTo('.vd-case-red', { scaleY: 0 }, { scaleY: 1, duration: 1, ease: 'expo.inOut' }, 0.1);
		if (d.querySelector('.vd-case-poster')) {
			tl.fromTo('.vd-case-poster', { clipPath: 'inset(100% 0% 0% 0%)' }, { clipPath: 'inset(0% 0% 0% 0%)', duration: 1.3, ease: 'expo.inOut' }, 0.25)
				.fromTo('.vd-case-poster > *', { scale: 1.15 }, { scale: 1, duration: 1.8, ease: 'expo.out' }, 0.25);
		}
		return tl;
	}

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
		var tl = gsap.timeline();
		var at = 0;
		if (html.classList.contains('vd-is-wiping')) {
			tl.to('.vd-wipe', {
				scaleY: 0, transformOrigin: '50% 0%', duration: 0.7, ease: 'expo.inOut',
				onComplete: function () { html.classList.remove('vd-is-wiping'); gsap.set('.vd-wipe', { clearProps: 'all' }); }
			});
			at = 0.3;
		}
		initHeroImages(); // sets its own starting state before the intro class is removed
		if (d.querySelector('.vd-hero')) tl.add(homeIntro(), at);
		else if (d.querySelector('.vd-case-head')) tl.add(caseIntro(), at);
		else endIntro();

		initParallax();
		initTiles();
		initPageTransitions();
	}

	// Start as soon as the headline's own font is ready (at most 600ms), so letters never swap mid-animation.
	// Waiting only for that one font, not every font on the page, keeps the intro from starting late.
	var ready = Promise.resolve();
	var title = d.querySelector('.vd-wordmark, .vd-case-title');
	if (title && d.fonts && d.fonts.load) {
		var cs = getComputedStyle(title);
		ready = d.fonts.load(cs.fontStyle + ' ' + cs.fontWeight + ' 1em ' + cs.fontFamily).catch(function () {});
	}
	Promise.race([ready, new Promise(function (r) { setTimeout(r, 600); })]).then(function () {
		try { start(); } catch (err) { html.classList.remove('vd-intro', 'vd-motion', 'vd-is-wiping'); }
	});
})();
