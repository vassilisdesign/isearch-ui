/*
 * Rank Math content analysis on Works: also count what the Work page shows besides the editor text,
 * i.e. the Excerpt ("About the project"), the Scope (red box) and the gallery images with their alt text.
 */
(function (w, d) {
	'use strict';
	if (!w.wp || !w.wp.hooks) return;

	function excerpt() {
		try {
			if (w.wp.data && w.wp.data.select('core/editor')) {
				return w.wp.data.select('core/editor').getEditedPostAttribute('excerpt') || '';
			}
		} catch (e) { /* classic editor */ }
		var el = d.getElementById('excerpt');
		return el ? el.value : '';
	}
	function esc(s) {
		var div = d.createElement('div');
		div.textContent = s;
		return div.innerHTML;
	}
	function extra() {
		var out = '';
		var ex = excerpt();
		if (ex) out += '<p>' + esc(ex) + '</p>';
		var scope = d.getElementById('vd_scope');
		if (scope && scope.value.trim()) out += '<p>' + esc(scope.value.trim()) + '</p>';
		Array.prototype.forEach.call(d.querySelectorAll('.vd-gallery-preview img'), function (img) {
			out += '<img src="' + esc(img.getAttribute('src') || '') + '" alt="' + esc(img.getAttribute('alt') || '') + '">';
		});
		return out;
	}

	w.wp.hooks.addFilter('rank_math_content', 'vassilisdesign/work-fields', function (content) {
		return content + extra();
	});

	// Re-run the analysis when these fields change.
	var timer;
	function refresh() {
		clearTimeout(timer);
		timer = setTimeout(function () {
			if (w.rankMathEditor && w.rankMathEditor.refresh) w.rankMathEditor.refresh('content');
		}, 400);
	}
	d.addEventListener('input', function (e) {
		if (e.target && (e.target.id === 'vd_scope' || e.target.id === 'excerpt' || (e.target.closest && e.target.closest('.editor-post-excerpt')))) refresh();
	});
	if (w.MutationObserver) {
		d.addEventListener('DOMContentLoaded', function () {
			var g = d.querySelector('.vd-gallery-preview');
			if (g) new MutationObserver(refresh).observe(g, { childList: true });
		});
	}
}(window, document));
