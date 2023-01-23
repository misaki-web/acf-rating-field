<?php

/**
 * Plugin Name: ACF Rating Field
 * Description: Rating field for ACF supporting decimal numbers and custom icon (default icon is a star: ★).
 * Text Domain: acf-rating-field
 * Author: Misaki F.
 * Version: 1.0.2
 */

namespace AcfRatingField;

if (!defined('ABSPATH')) {
	return;
}

################################################################################
#
# INCLUSIONS
#
################################################################################

require_once(__DIR__ . '/includes/init.php');
require_once(__DIR__ . '/includes/plugin-update-checker/plugin-update-checker.php');

################################################################################
#
# Update checker
#
################################################################################

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$update_checker = PucFactory::buildUpdateChecker(
	'https://github.com/misaki-web/acf-rating-field',
	__FILE__,
	'acf-rating-field'
);

$update_checker->getVcsApi()->enableReleaseAssets();

################################################################################
#
# ASSETS
#
################################################################################

function enqueue_scripts() {
	$url_dir = plugin_dir_url(__FILE__);

	wp_enqueue_style('acf-rating-field-css', $url_dir . '/assets/css/style.css');
}
add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_scripts');

################################################################################
#
# HELPER FUNCTIONS
#
################################################################################

function is_pos_int($value, $include_zero = true) {
	$min_range = $include_zero ? 0 : 1;
	$options = [
		'options' => ['min_range' => $min_range],
	];

	return !str_starts_with($value, '+') && filter_var($value, FILTER_VALIDATE_INT, $options) !== false;
}

function is_pos_float($value, $include_zero = true) {
	return !str_starts_with($value, '+') && filter_var($value, FILTER_VALIDATE_FLOAT) !== false && $value >= 0 && ($include_zero || $value > 0);
}

################################################################################
#
# SHORTCODES
#
################################################################################

# Render the shortcode "acf_rating_field".
function shortcode($atts = array()) {
	# Default arguments
	###################

	$default_atts = [
		'name' => '',
		'type' => 'post',
		'id' => -1,
	];

	# Get final arguments
	#####################

	$atts = wp_parse_args($atts, $default_atts);
	extract($atts);

	if ($atts['type'] != 'post' && $atts['type'] != 'user') {
		$atts['type'] = 'post';
	}

	if (!is_pos_int($atts['id'], false)) {
		if ($atts['type'] == 'post') {
			$atts['id'] = get_the_ID();
		} else if ($atts['type'] == 'user') {
			$atts['id'] = get_current_user_id();
		}
	}

	if ($atts['type'] == 'user') {
		$atts['id'] = 'user_' . $atts['id'];
	}

	# HTML
	######

	$field = get_field_object($atts['name'], $atts['id']);
	$classes = 'acf-rating-field-container';
	$style = '';
	$label_html = '';
	$data_symbols = '';
	$rating_percent = '';
	$aria_label = '';
	$blank_rating = false;
	
	if (!is_pos_float($field['value'])) {
		$blank_rating = true;
		$aria_label = __('No rating yet', 'acf-rating-field');
		$field['value'] = 0;
	}
	
	if (is_pos_float($field['max_value'], false)) {
		$data_symbols = esc_attr(str_repeat($field['symbol'], $field['max_value']));
		$rating_percent = $field['value'] / $field['max_value'] * 100;
		$rating_percent .= '%';
		
		if (!$blank_rating) {
			$aria_label = sprintf(__('Rating is %s out of %s', 'acf-rating-field'), esc_attr($field['value']), esc_attr($field['max_value']));
		}
	}
	
	$style .= '--acf_rating_field_value: ' . esc_attr($field['value']) . ';' .
		' --acf_rating_field_max_value: ' . esc_attr($field['max_value']) . ';' .
		' --acf_rating_field_percent: ' . esc_attr($rating_percent) . ';' .
		' --acf_rating_field_symbol: ' . esc_attr($field['symbol']) . ';' .
		' --acf_rating_field_symbol_color: ' . esc_attr($field['symbol_color']) . ';' .
		' --acf_rating_field_filled_symbol_color: ' . esc_attr($field['filled_symbol_color']) . ';';

	if ($field['label_text'] !== null && $field['label_text'] !== '') {
		$classes .= ' acf-rating-field-with-label';
		$label_html = '<span class="acf-rating-field-label">' . esc_html($field['label_text']) . '</span>';
	}

	if ($field['add_border']) {
		$classes .= ' acf-rating-field-with-border';
		$style .= ' --acf_rating_field_border_width: ' . esc_attr($field['border_width']) . 'px;' .
			' --acf_rating_field_border_style: ' . esc_attr($field['border_style']) . ';' .
			' --acf_rating_field_border_color: ' . esc_attr($field['border_color']) . ';' .
			' --acf_rating_field_border_radius: ' . esc_attr($field['border_radius']) . 'px;';
	}

	if (is_pos_float($field['symbol_size'], false)) {
		$classes .= ' acf-rating-field-with-size';
		$style .= ' --acf_rating_field_symbol_size: ' . esc_attr($field['symbol_size'] . $field['symbol_size_unit']) . ';';
	}
	
	if (is_pos_int($field['symbol_spacing'])) {
		$classes .= ' acf-rating-field-with-spacing';
		$style .= ' --acf_rating_field_symbol_spacing: ' . esc_attr($field['symbol_spacing']) . 'px;';
	}
	
	if ($blank_rating && $field['blank_rating_msg'] !== null && $field['blank_rating_msg'] !== '') {
		$classes .= ' acf-rating-field-with-blank-rating-msg';
		$style .= ' --acf_rating_field_blank_rating_msg_bg_color: ' . esc_attr($field['blank_rating_msg_bg_color']) . ';';
	}
	
	if ($field['add_padding']) {
		if (is_pos_int($field['top_padding'])) {
			$classes .= ' acf-rating-field-with-top-padding';
			$style .= ' --acf_rating_field_top_padding: ' . esc_attr($field['top_padding']) . 'px;';
		}

		if (is_pos_int($field['right_padding'])) {
			$classes .= ' acf-rating-field-with-right-padding';
			$style .= ' --acf_rating_field_right_padding: ' . esc_attr($field['right_padding']) . 'px;';
		}

		if (is_pos_int($field['bottom_padding'])) {
			$classes .= ' acf-rating-field-with-bottom-padding';
			$style .= ' --acf_rating_field_bottom_padding: ' . esc_attr($field['bottom_padding']) . 'px;';
		}

		if (is_pos_int($field['left_padding'])) {
			$classes .= ' acf-rating-field-with-left-padding';
			$style .= ' --acf_rating_field_left_padding: ' . esc_attr($field['left_padding']) . 'px;';
		}
	}

	return '<div class="' . $classes . '" style="' . $style . '">' .
		$label_html .
		'<span class="acf-rating-field-rating-container" data-blank-rating-msg="' . esc_attr($field['blank_rating_msg']) . '">' .
		'<span class="acf-rating-field-rating" data-symbols="' . $data_symbols . '"' .
		' aria-label="' . $aria_label . '" title="' . $aria_label . '"></span>' .
		'</span>' .
		'</div>';
}
add_shortcode('acf_rating_field', __NAMESPACE__ . '\\shortcode');
