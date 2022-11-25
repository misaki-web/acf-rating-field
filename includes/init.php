<?php

/**
 * Registration logic for the new ACF field type.
 */

if (!defined('ABSPATH')) {
	exit;
}

add_action('init', 'include_acf_rating_field');
/**
 * Registers the ACF field type.
 */
function include_acf_rating_field() {
	if (!function_exists('acf_register_field_type')) {
		return;
	}

	require_once __DIR__ . '/class-acf-rating-field.php';

	acf_register_field_type('acf_rating_field');
}
