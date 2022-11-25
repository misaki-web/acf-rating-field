<?php

/**
 * Defines the ACF Rating Field class.
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * acf_rating_field class.
 */
class acf_rating_field extends \acf_field {
	/**
	 * Controls field type visibilty in REST requests.
	 *
	 * @var bool
	 */
	public $show_in_rest = true;

	/**
	 * Environment values relating to the plugin.
	 *
	 * @var array $env Plugin context such as 'url' and 'version'.
	 */
	private $env;

	/**
	 * Constructor.
	 */
	public function __construct() {
		/**
		 * Field type reference used in PHP code.
		 *
		 * No spaces. Underscores allowed.
		 */
		$this->name = 'rating';

		/**
		 * Field type label.
		 *
		 * For public-facing UI. May contain spaces.
		 */
		$this->label = __('Rating', 'acf-rating-field');

		/**
		 * The category the field appears within in the field type picker.
		 */
		$this->category = 'basic'; // basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME

		parent::__construct();
	}

	/**
	 * Settings to display when users configure a field of this type.
	 *
	 * These settings appear on the ACF “Edit Field Group” admin page when
	 * setting up the field.
	 *
	 * @param array $field
	 * @return void
	 */
	public function render_field_settings($field) {
		acf_render_field_setting(
			$field,
			[
				'name'         => 'label_text',
				'label'        => __('Label', 'acf-rating-field'),
				'type'         => 'text',
				'default_value' => 'Rating:',
				'hint'         => __('Enter the label displayed before the rating symbols (leave empty to not display a label).', 'acf-rating-field'),
			],
		);
		acf_render_field_setting(
			$field,
			[
				'name'         => 'max_value',
				'label'        => __('Max value', 'acf-rating-field'),
				'type'         => 'number',
				'min'          => 1,
				'step'         => 1,
				'required'     => true,
				'default_value' => 5,
				'hint'         => __('Enter the max value (it must be a positive integer).', 'acf-rating-field'),
			],
		);
		acf_render_field_setting(
			$field,
			[
				'name'         => 'step_size',
				'label'        => __('Step size', 'acf-rating-field'),
				'type'         => 'select',
				'choices'      => [
					'0.01' => '0.01',
					'0.1'  => '0.1',
					'1'    => '1',
				],
				'required'     => true,
				'default_value' => '0.1',
				'hint'         => __('Enter the step size. If the step is 0.01, ratings like 3.75 will be possible. If the step is 0.1, it\'ll be ratings like 3.8. If the step is 1, ratings will be integers (like 4).', 'acf-rating-field'),
			],
		);
		acf_render_field_setting(
			$field,
			[
				'name'         => 'symbol',
				'label'        => __('Symbol', 'acf-rating-field'),
				'type'         => 'custom_terms',
				'choices'      => [
					'★' => '★',
					'✭' => '✭',
					'✮' => '✮',
					'✯' => '✯',
					'✬' => '✬',
					'✪' => '✪',
					'⍟' => '⍟',
					'✫' => '✫',
					'✰' => '✰',
					'☆' => '☆',
					'✩' => '✩',
					'⚝' => '⚝',
					'⛤' => '⛤',
					'♥' => '♥',
					'☀' => '☀',
					'🌲' => '🌲',
					'🌿' => '🌿',
					'☘️' => '☘️',
					'🍒' => '🍒',
					'🔥' => '🔥',
					'🎥' => '🎥',
				],
				'required'     => true,
				'default_value' => '★',
				'hint'         => __('Choose a symbol from those listed or enter a new one.', 'acf-rating-field'),
			],
		);
		acf_render_field_setting(
			$field,
			[
				'name'         => 'symbol_size',
				'label'        => __('Symbol size', 'acf-rating-field'),
				'type'         => 'number',
				'min'          => 0.01,
				'step'         => 0.01,
				'default_value' => 2.50,
				'hint'         => __('Enter the symbol size (leave empty to disable size declaration).', 'acf-rating-field'),
			],
		);
		acf_render_field_setting(
			$field,
			[
				'name'         => 'symbol_size_unit',
				'label'        => __('Symbol size unit', 'acf-rating-field'),
				'type'         => 'select',
				'choices'      => [
					'px'  => 'px',
					'em'  => 'em',
					'rem' => 'rem',
				],
				'conditions'   => [
					'field'    => 'symbol_size',
					'operator' => '!=',
					'value'    => '',
				],
				'required'     => true,
				'default_value' => 'em',
				'hint'         => __('Choose the symbol size unit.', 'acf-rating-field'),
			],
		);
		acf_render_field_setting(
			$field,
			[
				'name'         => 'symbol_spacing',
				'label'        => __('Symbol spacing', 'acf-rating-field'),
				'type'         => 'number',
				'min'          => 0,
				'step'         => 1,
				'append'       => 'px',
				'default_value' => 2,
				'hint'         => __('Enter the horizontal spacing between rating symbols (leave empty to disable spacing declaration).', 'acf-rating-field'),
			],
		);
		acf_render_field_setting(
			$field,
			[
				'name'         => 'symbol_color',
				'label'        => __('Symbol color', 'acf-rating-field'),
				'type'         => 'color_picker',
				'required'     => true,
				'default_value' => '#B3B3B3',
				'hint'         => __('Choose the default symbol color.', 'acf-rating-field'),
			],
		);
		acf_render_field_setting(
			$field,
			[
				'name'         => 'filled_symbol_color',
				'label'        => __('Filled symbol color', 'acf-rating-field'),
				'type'         => 'color_picker',
				'required'     => true,
				'default_value' => '#FED617',
				'hint'         => __('Choose the filled symbol color.', 'acf-rating-field'),
			],
		);
		acf_render_field_setting(
			$field,
			[
				'name'         => 'add_border',
				'label'        => __('Add border', 'acf-rating-field'),
				'type'         => 'true_false',
				'default_value' => true,
				'hint'         => __('Add a border around the rating container.', 'acf-rating-field'),
			],
		);
		acf_render_field_setting(
			$field,
			[
				'name'         => 'border_width',
				'label'        => __('Border width', 'acf-rating-field'),
				'type'         => 'number',
				'min'          => 0,
				'step'         => 1,
				'append'       => 'px',
				'conditions'   => [
					'field'    => 'add_border',
					'operator' => '==',
					'value'    => 1,
				],
				'required'     => true,
				'default_value' => 2,
				'hint'         => __('Enter the border width (px).', 'acf-rating-field'),
			],
		);
		acf_render_field_setting(
			$field,
			[
				'name'         => 'border_style',
				'label'        => __('Border style', 'acf-rating-field'),
				'type'         => 'select',
				'choices'      => [
					'none'   => 'none',
					'dashed' => 'dashed',
					'dotted' => 'dotted',
					'double' => 'double',
					'groove' => 'groove',
					'hidden' => 'hidden',
					'inset'  => 'inset',
					'outset' => 'outset',
					'ridge'  => 'ridge',
					'solid'  => 'solid',
				],
				'conditions'   => [
					'field'    => 'add_border',
					'operator' => '==',
					'value'    => 1,
				],
				'required'     => true,
				'default_value' => 'solid',
				'hint'         => __('Choose the border style.', 'acf-rating-field'),
			],
		);
		acf_render_field_setting(
			$field,
			[
				'name'         => 'border_color',
				'label'        => __('Border color', 'acf-rating-field'),
				'type'         => 'color_picker',
				'conditions'   => [
					'field'    => 'add_border',
					'operator' => '==',
					'value'    => 1,
				],
				'required'     => true,
				'default_value' => '#B3B3B3',
				'hint'         => __('Choose the border color.', 'acf-rating-field'),
			],
		);
		acf_render_field_setting(
			$field,
			[
				'name'         => 'border_radius',
				'label'        => __('Border radius', 'acf-rating-field'),
				'type'         => 'number',
				'min'          => 0,
				'step'         => 1,
				'append'       => 'px',
				'conditions'   => [
					'field'    => 'add_border',
					'operator' => '==',
					'value'    => 1,
				],
				'required'     => true,
				'default_value' => 4,
				'hint'         => __('Enter the border radius.', 'acf-rating-field'),
			],
		);
		acf_render_field_setting(
			$field,
			[
				'name'         => 'add_padding',
				'label'        => __('Add padding', 'acf-rating-field'),
				'type'         => 'true_false',
				'default_value' => true,
				'hint'         => __('Add padding around the rating container.', 'acf-rating-field'),
			],
		);
		acf_render_field_setting(
			$field,
			[
				'name'         => 'top_padding',
				'label'        => __('Top padding', 'acf-rating-field'),
				'type'         => 'number',
				'min'          => 0,
				'step'         => 1,
				'append'       => 'px',
				'conditions'   => [
					'field'    => 'add_padding',
					'operator' => '==',
					'value'    => 1,
				],
				'default_value' => 0,
				'hint' => __('Enter the top padding (leave empty to disable top padding declaration).', 'acf-rating-field'),
			],
		);
		acf_render_field_setting(
			$field,
			[
				'name'         => 'right_padding',
				'label'        => __('Right padding', 'acf-rating-field'),
				'type'         => 'number',
				'min'          => 0,
				'step'         => 1,
				'append'       => 'px',
				'conditions'   => [
					'field'    => 'add_padding',
					'operator' => '==',
					'value'    => 1,
				],
				'default_value' => 5,
				'hint' => __('Enter the right padding (leave empty to disable right padding declaration).', 'acf-rating-field'),
			],
		);
		acf_render_field_setting(
			$field,
			[
				'name'         => 'bottom_padding',
				'label'        => __('Bottom padding', 'acf-rating-field'),
				'type'         => 'number',
				'min'          => 0,
				'step'         => 1,
				'append'       => 'px',
				'conditions'   => [
					'field'    => 'add_padding',
					'operator' => '==',
					'value'    => 1,
				],
				'default_value' => 0,
				'hint' => __('Enter the bottom padding (leave empty to disable bottom padding declaration).', 'acf-rating-field'),
			],
		);
		acf_render_field_setting(
			$field,
			[
				'name'         => 'left_padding',
				'label'        => __('Left padding', 'acf-rating-field'),
				'type'         => 'number',
				'min'          => 0,
				'step'         => 1,
				'append'       => 'px',
				'conditions'   => [
					'field'    => 'add_padding',
					'operator' => '==',
					'value'    => 1,
				],
				'default_value' => 5,
				'hint' => __('Enter the left padding (leave empty to disable left padding declaration).', 'acf-rating-field'),
			],
		);
	}

	/**
	 * HTML content to show when the field is edited.
	 *
	 * @param array $field The field settings and values.
	 * @return void
	 */
	public function render_field($field) {
		echo '<input class="acf-rating-field-input" ' .
			'name="' . esc_attr($field['name']) . '" ' .
			'type="number" min="0" max="' . esc_attr($field['max_value']) . '" ' .
			'step="' . esc_attr($field['step_size']) . '" ' .
			'value="' . esc_attr($field['value']) . '" />';
	}
}
