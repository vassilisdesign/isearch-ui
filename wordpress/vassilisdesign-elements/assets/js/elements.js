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

	if (reduce || inEditor || !w.gsap) {
		html.classList.remove('vd-intro', 'vd-is-wiping');
		return;
	}

	var gsap = w.gsap;
	if (w.ScrollTrigger) gsap.registerPlugin(w.ScrollTrigger);

	/* ---------- Intros ---------- */
	function homeIntro() {
		var targets = ['.vd-wordmark .vd-ch', '.vd-hero-band', '.vd-hero-photo', '.vd-hero-photo > *', '.vd-hero-cta', '.vd-hero-lead', '.vd-hero-meta', '.vd-hero-vertical', '.vd-hero-corner'];
		return gsap.timeline({ onComplete: function () { endIntro(); gsap.set(targets, { clearProps: 'transform,clipPath,opacity,visibility' }); } })
			.fromTo('.vd-hero-band', { scaleX: 0 }, { scaleX: 1, duration: 1.1, ease: 'expo.inOut' })
			.fromTo('.vd-hero-photo', { clipPath: 'inset(100% 0% 0% 0%)' }, { clipPath: 'inset(0% 0% 0% 0%)', duration: 1.3, ease: 'expo.inOut' }, 0.15)
			.fromTo('.vd-hero-photo > *', { scale: 1.2 }, { scale: 1, duration: 1.8, ease: 'expo.out' }, 0.15)
			.fromTo('.vd-wordmark .vd-ch', { yPercent: 105 }, { yPercent: 0, duration: 1.2, ease: 'expo.out', stagger: 0.04 }, 0.45)
			.fromTo('.vd-hero-cta', { scale: 0 }, { scale: 1, duration: 0.7, ease: 'back.out(1.6)' }, 1.1)
			.fromTo(['.vd-hero-meta', '.vd-hero-vertical', '.vd-hero-lead', '.vd-hero-corner'], { autoAlpha: 0 }, { autoAlpha: 1, duration: 0.8, ease: 'power2.out', stagger: 0.07 }, 1);
	}

	function caseIntro() {
		var targets = ['.vd-case-title .vd-line > span', '.vd-case-poster', '.vd-case-poster > *', '.vd-case-meta > div', '.vd-case-red'];
		var tl = gsap.timeline({ onComplete: function () { endIntro(); gsap.set(targets, { clearProps: 'transform,clipPath,opacity,visibility' }); } })
			.fromTo('.vd-case-title .vd-line > span', { yPercent: 105 }, { yPercent: 0, duration: 1.2, ease: 'expo.out' })
			.fromTo('.vd-case-meta > div', { autoAlpha: 0 }, { autoAlpha: 1, duration: 0.6, stagger: 0.06 }, 0.3);
		if (d.querySelector('.vd-case-red')) tl.fromTo('.vd-case-red', { scaleY: 0 }, { scaleY: 1, duration: 1, ease: 'expo.inOut' }, 0.1);
		if (d.querySelector('.vd-case-poster')) {
			tl.fromTo('.vd-case-poster', { clipPath: 'inset(100% 0% 0% 0%)' }, { clipPath: 'inset(0% 0% 0% 0%)', duration: 1.3, ease: 'expo.inOut' }, 0.25)
				.fromTo('.vd-case-poster > *', { scale: 1.15 }, { scale: 1, duration: 1.8, ease: 'expo.out' }, 0.25);
		}
		return tl;
	}

	/* ---------- Scroll ---------- */
	function initParallax() {
		if (!w.ScrollTrigger) return;
		if (d.querySelector('.vd-hero')) {
			var st = { trigger: '.vd-hero', start: 'top top', end: 'bottom top', scrub: true };
			gsap.to('.vd-hero-photo', { yPercent: 12, ease: 'none', scrollTrigger: st });
			gsap.to('.vd-hero-band', { xPercent: 6, ease: 'none', scrollTrigger: { trigger: '.vd-hero', start: 'top top', end: 'bottom top', scrub: true } });
		}
		$$('.vd-studio-photo > .vd-photo-img').forEach(function (el) {
			gsap.fromTo(el, { yPercent: -5 }, { yPercent: 5, ease: 'none', scrollTrigger: { trigger: el.parentElement, start: 'top bottom', end: 'bottom top', scrub: true } });
		});
	}

	function initTiles() {
		if (!w.ScrollTrigger) return;
		var tiles = $$('.vd-tile-media');
		if (!tiles.length) return;
		gsap.set(tiles, { clipPath: 'inset(100% 0% 0% 0%)' });
		w.ScrollTrigger.batch(tiles, {
			start: 'top 92%',
			once: true,
			onEnter: function (batch) {
				gsap.to(batch, {
					clipPath: 'inset(0% 0% 0% 0%)', duration: 1.1, ease: 'expo.inOut', stagger: 0.08,
					onComplete: function () { gsap.set(batch, { clearProps: 'clipPath' }); }
				});
			}
		});
	}

	/* ---------- VD Hero image: reveal when in view, optional scroll movement ---------- */
	function initHeroImages() {
		$$('.vd-hero-image').forEach(function (el) {
			var band = el.querySelector('.vd-hi-band'), photo = el.querySelector('.vd-hi-photo'), img = photo ? photo.firstElementChild : null, cta = el.querySelector('.vd-hi-cta');
			if (el.getAttribute('data-vd-motion') === 'yes') {
				gsap.set(band, { scaleX: 0 });
				if (photo) gsap.set(photo, { clipPath: 'inset(100% 0% 0% 0%)' });
				if (img) gsap.set(img, { scale: 1.2 });
				if (cta) gsap.set(cta, { scale: 0 });
				var play = function () {
					var tl = gsap.timeline({ onComplete: function () { gsap.set([band, photo, img, cta].filter(Boolean), { clearProps: 'transform,clipPath' }); } })
						.to(band, { scaleX: 1, duration: 1.1, ease: 'expo.inOut' });
					if (photo) tl.to(photo, { clipPath: 'inset(0% 0% 0% 0%)', duration: 1.3, ease: 'expo.inOut' }, 0.15);
					if (img) tl.to(img, { scale: 1, duration: 1.8, ease: 'expo.out' }, 0.15);
					if (cta) tl.to(cta, { scale: 1, duration: 0.7, ease: 'back.out(1.6)' }, 0.9);
				};
				if (w.ScrollTrigger) w.ScrollTrigger.create({ trigger: el, start: 'top 85%', once: true, onEnter: play });
				else play();
			}
			if (el.getAttribute('data-vd-parallax') === 'yes' && w.ScrollTrigger && photo) {
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
		var tl = gsap.timeline({ delay: 0.05 });
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

	// Start once the font is ready (or after 1.2s) so letters never swap mid-animation.
	var ready = d.fonts && d.fonts.ready ? d.fonts.ready : Promise.resolve();
	Promise.race([ready, new Promise(function (r) { setTimeout(r, 1200); })]).then(function () {
		try { start(); } catch (err) { html.classList.remove('vd-intro', 'vd-is-wiping'); }
	});
})();
