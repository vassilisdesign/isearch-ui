/* Work gallery picker (edit screen). Stores attachment IDs, in order, in #vd_gallery. */
(function ($) {
	var frame;
	var $input = $('#vd_gallery');
	var $preview = $('.vd-gallery-preview');

	function ids() {
		return ($input.val() || '').split(',').filter(Boolean);
	}

	function render(attachments) {
		$preview.empty();
		attachments.forEach(function (a) {
			var src = a.sizes && a.sizes.thumbnail ? a.sizes.thumbnail.url : a.url;
			$('<li>').attr('data-id', a.id).append($('<img>').attr({ src: src, alt: '' })).appendTo($preview);
		});
	}

	$('.vd-gallery-choose').on('click', function () {
		var selection = ids().map(function (id) { return wp.media.attachment(id); });
		frame = wp.media({
			title: vdWorks.title,
			button: { text: vdWorks.button },
			library: { type: 'image' },
			multiple: 'add'
		});
		frame.on('open', function () {
			var sel = frame.state().get('selection');
			selection.forEach(function (a) { a.fetch(); sel.add(a); });
		});
		frame.on('select', function () {
			var chosen = frame.state().get('selection').toJSON();
			$input.val(chosen.map(function (a) { return a.id; }).join(','));
			render(chosen);
		});
		frame.open();
	});

	$('.vd-gallery-clear').on('click', function () {
		$input.val('');
		$preview.empty();
	});
})(jQuery);
