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

	/* ---------- Lightbox: Work cover and gallery images, full size ---------- */
	function initLightbox() {
		var box, img, cap, count, closeBtn, items = [], index = 0, opener = null, startX = null;
		function label(key, fallback) { return (w.vdLightbox && w.vdLightbox[key]) || fallback; }
		function visibleButtons() {
			return Array.prototype.filter.call(box.querySelectorAll('button'), function (b) { return getComputedStyle(b).visibility !== 'hidden'; });
		}
		function build() {
			box = d.createElement('div');
			box.className = 'vd-lb';
			box.hidden = true;
			box.setAttribute('role', 'dialog');
			box.setAttribute('aria-modal', 'true');
			box.setAttribute('aria-label', label('dialog', 'Image viewer'));
			box.innerHTML = '<div class="vd-lb-bar"><span class="vd-lb-count" aria-live="polite"></span><button type="button" class="vd-lb-close"></button></div>' +
				'<div class="vd-lb-stage"><button type="button" class="vd-lb-prev"></button><img class="vd-lb-img" alt=""><button type="button" class="vd-lb-next"></button></div>' +
				'<p class="vd-lb-caption"></p>';
			d.body.appendChild(box);
			img = box.querySelector('.vd-lb-img');
			cap = box.querySelector('.vd-lb-caption');
			count = box.querySelector('.vd-lb-count');
			closeBtn = box.querySelector('.vd-lb-close');
			[['.vd-lb-close', '×', label('close', 'Close')], ['.vd-lb-prev', '←', label('prev', 'Previous image')], ['.vd-lb-next', '→', label('next', 'Next image')]].forEach(function (b) {
				var el = box.querySelector(b[0]);
				el.innerHTML = '<span aria-hidden="true">' + b[1] + '</span>';
				el.setAttribute('aria-label', b[2]);
			});
			closeBtn.addEventListener('click', close);
			box.querySelector('.vd-lb-prev').addEventListener('click', function () { show(index - 1); });
			box.querySelector('.vd-lb-next').addEventListener('click', function () { show(index + 1); });
			// A click on the dark area around the image closes the viewer.
			box.addEventListener('click', function (e) { if (e.target === box || e.target.classList.contains('vd-lb-stage')) close(); });
			img.addEventListener('load', function () { img.classList.remove('vd-lb-loading'); });
			box.addEventListener('touchstart', function (e) { startX = e.touches[0].clientX; }, { passive: true });
			box.addEventListener('touchend', function (e) {
				if (startX === null) return;
				var dx = e.changedTouches[0].clientX - startX;
				startX = null;
				if (Math.abs(dx) > 50) show(index + (dx < 0 ? 1 : -1));
			});
			d.addEventListener('keydown', function (e) {
				if (box.hidden) return;
				if (e.key === 'Escape') { e.preventDefault(); close(); }
				else if (e.key === 'ArrowLeft') show(index - 1);
				else if (e.key === 'ArrowRight') show(index + 1);
				else if (e.key === 'Tab') { // keep focus inside the viewer
					var f = visibleButtons();
					if (!f.length) return;
					var first = f[0], last = f[f.length - 1];
					if (e.shiftKey && d.activeElement === first) { e.preventDefault(); last.focus(); }
					else if (!e.shiftKey && d.activeElement === last) { e.preventDefault(); first.focus(); }
				}
			});
		}
		function show(i) {
			var n = items.length;
			index = (i + n) % n;
			var a = items[index];
			if (img.getAttribute('src') !== a.href) {
				img.classList.add('vd-lb-loading');
				img.src = a.href;
			}
			img.alt = a.getAttribute('data-vd-caption') || '';
			cap.textContent = a.getAttribute('data-vd-caption') || '';
			count.textContent = (index + 1) + ' / ' + n;
			box.classList.toggle('vd-lb-single', n < 2);
			if (n > 1) { // load the images either side, so arrows feel instant
				[index - 1, index + 1].forEach(function (j) { var p = new Image(); p.src = items[(j + n) % n].href; });
			}
		}
		function open(a) {
			if (!box) build();
			var group = a.getAttribute('data-vd-lb');
			items = $$('a[data-vd-lb]').filter(function (x) { return x.getAttribute('data-vd-lb') === group; });
			opener = a;
			show(Math.max(0, items.indexOf(a)));
			box.hidden = false;
			html.classList.add('vd-lb-lock');
			requestAnimationFrame(function () { box.classList.add('vd-lb-open'); });
			closeBtn.focus();
		}
		function close() {
			box.classList.remove('vd-lb-open');
			html.classList.remove('vd-lb-lock');
			box.hidden = true;
			img.removeAttribute('src');
			if (opener) opener.focus();
		}
		d.addEventListener('click', function (e) {
			var a = e.target.closest ? e.target.closest('a[data-vd-lb]') : null;
			if (!a || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
			e.preventDefault();
			e.stopPropagation(); // opens the viewer, not a new page: no red wipe
			open(a);
		}, true);
	}

	/* ---------- VD Navigation menu: menu button and submenus (works in the editor too) ---------- */
	function initNav() {
		function navOf(el) { return el.closest ? el.closest('.vdn') : null; }
		function isFullscreen(nav) { return nav.classList.contains('vdn--fullscreen'); }
		function setText(nav, open) {
			var t = nav.querySelector('.vdn-toggle-text');
			if (t) t.textContent = t.getAttribute(open ? 'data-close' : 'data-open') || t.textContent;
		}
		function place(nav) { // the panel starts at the bottom of the header (or of the button)
			var box = nav.closest('.elementor-location-header, header, .e-con.e-parent, .elementor-section') || nav;
			var bottom = Math.max(0, Math.round(box.getBoundingClientRect().bottom));
			nav.style.setProperty('--vdn-top', bottom + 'px');
		}
		function open(nav) {
			var btn = nav.querySelector('.vdn-toggle');
			nav.classList.add('is-collapsed', 'is-open');
			place(nav);
			if (btn) btn.setAttribute('aria-expanded', 'true');
			setText(nav, true);
			if (isFullscreen(nav)) html.classList.add('vdn-lock');
		}
		function close(nav, focusButton) {
			var btn = nav.querySelector('.vdn-toggle');
			nav.classList.remove('is-open');
			if (btn) btn.setAttribute('aria-expanded', 'false');
			setText(nav, false);
			Array.prototype.forEach.call(nav.querySelectorAll('.vdn-has-sub.is-open'), closeSub);
			if (!d.querySelector('.vdn--fullscreen.is-open')) html.classList.remove('vdn-lock');
			if (focusButton && btn) btn.focus();
		}
		function closeSub(li) {
			li.classList.remove('is-open');
			var b = li.querySelector(':scope > .vdn-sub-toggle');
			if (b) b.setAttribute('aria-expanded', 'false');
		}
		function buttonShown(nav) {
			var btn = nav.querySelector('.vdn-toggle');
			return !!btn && getComputedStyle(btn).display !== 'none';
		}
		d.addEventListener('click', function (e) {
			var t = e.target;
			var toggle = t.closest ? t.closest('.vdn-toggle') : null;
			if (toggle) {
				var nav = navOf(toggle);
				if (nav.classList.contains('is-open')) close(nav); else open(nav);
				return;
			}
			var sub = t.closest ? t.closest('.vdn-sub-toggle') : null;
			if (sub) {
				var li = sub.parentNode, wasOpen = li.classList.contains('is-open');
				Array.prototype.forEach.call(li.parentNode.querySelectorAll(':scope > .vdn-has-sub.is-open'), closeSub); // one open at a time per level
				if (!wasOpen) { li.classList.add('is-open'); sub.setAttribute('aria-expanded', 'true'); }
				return;
			}
			// A link in the open menu (for example #contact on the same page) closes it.
			var link = t.closest ? t.closest('.vdn.is-open .vdn-link') : null;
			if (link) { close(navOf(link)); return; }
			// A click outside closes open menus and submenus.
			Array.prototype.forEach.call(d.querySelectorAll('.vdn'), function (nav) {
				if (nav.contains(t)) return;
				if (nav.classList.contains('is-open')) close(nav);
				Array.prototype.forEach.call(nav.querySelectorAll('.vdn-has-sub.is-open'), closeSub);
			});
		});
		d.addEventListener('keydown', function (e) {
			if (e.key !== 'Escape') return;
			var openSub = d.activeElement && d.activeElement.closest ? d.activeElement.closest('.vdn-has-sub.is-open') : null;
			if (openSub) { closeSub(openSub); var b = openSub.querySelector(':scope > .vdn-sub-toggle'); if (b) b.focus(); return; }
			Array.prototype.forEach.call(d.querySelectorAll('.vdn.is-open'), function (nav) { close(nav, true); });
		});
		// Submenus near the right edge of the screen open towards the left.
		function fit(e) {
			var li = e.target.closest ? e.target.closest('.vdn-has-sub') : null;
			if (!li || li.closest('.vdn.is-collapsed, .vdn--vertical')) return;
			var sub = li.querySelector(':scope > .vdn-sub');
			if (!sub) return;
			li.classList.remove('vdn-flip');
			var r = li.getBoundingClientRect(), wide = sub.offsetWidth;
			var nested = !!li.parentNode.closest('.vdn-sub');
			if ((nested ? r.right + wide : r.left + wide) > d.documentElement.clientWidth - 8) li.classList.add('vdn-flip');
		}
		d.addEventListener('mouseover', fit);
		d.addEventListener('focusin', fit);
		var timer;
		w.addEventListener('resize', function () { // back to the full menu when the screen gets wider
			clearTimeout(timer);
			timer = setTimeout(function () {
				Array.prototype.forEach.call(d.querySelectorAll('.vdn.is-collapsed'), function (nav) {
					if (!buttonShown(nav)) { close(nav); nav.classList.remove('is-collapsed'); }
					else if (nav.classList.contains('is-open')) place(nav);
				});
			}, 120);
		});
	}

	initClock();
	initNav();
	if (!inEditor) initLightbox();
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
