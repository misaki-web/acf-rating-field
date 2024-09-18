jQuery(document).ready(function ($) {
	const $radio_labels = $('.acf-rating-field-edit-label');

	function update_symbols(index) {
		$radio_labels.each(function (other_index) {
			const $symbol = $(this).find('.acf-rating-field-edit-symbol');

			if (other_index <= index) {
				$symbol.addClass('acf-rating-field-edit-symbol-filled');
			} else {
				$symbol.removeClass('acf-rating-field-edit-symbol-filled');
			}
		});
	}

	function initialize_symbols() {
		$radio_labels.each(function (index) {
			const $radio_input = $(this).find('input[type="radio"]');

			if ($radio_input.is(':checked')) {
				update_symbols(index);
			}

			$radio_input.on('input', function () {
				update_symbols(index);
			});
		});
	}

	initialize_symbols();
});
