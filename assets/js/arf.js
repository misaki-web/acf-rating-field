jQuery(document).ready(function ($) {
	const $container = $('.acf-rating-field-edit-input-container');
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
		let checked_index = -1;

		$radio_labels.each(function (index) {
			const $radio_input = $(this).find('input[type="radio"]');

			if ($radio_input.is(':checked')) {
				checked_index = index;

				update_symbols(index);
			}

			$radio_input.on('input', function () {
				checked_index = index;

				update_symbols(index);
			});
		});

		$radio_labels.each(function (index) {
			$(this).on('mouseenter', function () {
				update_symbols(index);
			});
		});

		$container.on('mouseleave', function () {
			update_symbols(checked_index);
		});
	}

	initialize_symbols();
});
