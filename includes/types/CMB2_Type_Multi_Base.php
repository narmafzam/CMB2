<?php

/**
 * CMB Multi base field type
 *
 * @since  2.2.2
 *
 * @category  WordPress_Plugin
 * @package   CMB2
 * @author    CMB2 team
 * @license   GPL-2.0+
 * @link      https://cmb2.io
 */
abstract class CMB2_Type_Multi_Base extends CMB2_Type_Base
{

	/**
	 * Generates html for an option element
	 *
	 * @param array $args Arguments array containing value, label, and checked boolean
	 *
	 * @return string       Generated option element html
	 * @since  1.1.0
	 */
	public function select_option($args = [])
	{
		return sprintf("\t" . '<option value="%s" %s>%s</option>', $args['value'], selected(isset($args['checked']) && $args['checked'], true, false), $args['label']) . "\n";
	}

	/**
	 * @param       $label
	 * @param array $attrs
	 * @param array $items
	 *
	 * @return string
	 */
	public function select_optgroup($label, $attrs = [], $items = [])
	{
		return sprintf("\t" . '<optgroup label="%s" %s>%s</optgroup>', $label, $this->concat_attrs($attrs), $items) . "\n";
	}

	/**
	 * Generates html for list item with input
	 *
	 * @param array $args Override arguments
	 * @param int   $i Iterator value
	 *
	 * @return string       Gnerated list item html
	 * @since  1.1.0
	 */
	public function list_input($args = [], $i)
	{
		$a = $this->parse_args('list_input', [
			'type'  => 'radio',
			'class' => 'cmb2-option',
			'name'  => $this->_name(),
			'id'    => $this->_id($i),
			'value' => $this->field->escaped_value(),
			'label' => '',
		], $args);

		return sprintf("\t" . '<li><input%s/> <label for="%s">%s</label></li>' . "\n", $this->concat_attrs($a, ['label']), $a['id'], $a['label']);
	}

	/**
	 * Generates html for list item with checkbox input
	 *
	 * @param array $args Override arguments
	 * @param int   $i Iterator value
	 *
	 * @return string       Gnerated list item html
	 * @since  1.1.0
	 */
	public function list_input_checkbox($args, $i)
	{
		$saved_value = $this->field->escaped_value();
		if (is_array($saved_value) && in_array($args['value'], $saved_value)) {
			$args['checked'] = 'checked';
		}
		$args['type'] = 'checkbox';
		return $this->list_input($args, $i);
	}

	/**
	 * Generates html for concatenated items
	 *
	 * @param array $args Optional arguments
	 *
	 * @return string        Concatenated html items
	 * @since  1.1.0
	 */
	public function concat_items($args = [])
	{
		$field = $this->field;

		$method = isset($args['method']) ? $args['method'] : 'select_option';
		unset($args['method']);

		$value = null !== $field->escaped_value()
			? $field->escaped_value()
			: $field->get_default();

		$value = CMB2_Utils::normalize_if_numeric($value);

		$concatenated_items = '';
		$i                  = 1;

		$options = [];
		if ($option_none = $field->args('show_option_none')) {
			$options[''] = $option_none;
		}
		$options = $options + (array)$field->options();
		foreach ($options as $opt_value => $opt) {

			if (is_array($opt)) {
				$group_options = $opt;
				if (isset($opt['options'])) {

					$group_options = $opt['options'];
				}
				if (is_array($group_options)) {

					$items = '';
					foreach ($group_options as $key => $item) {
						$items .= $this->generate_option(
							$value,
							$method,
							$args,
							$item,
							$key,
							$i++
						);
					}
					$label = isset($opt['label']) ? $opt['label'] : $opt_value;

					$item = $this->select_optgroup($label, [], $items);
				}
			} else {

				$item = $this->generate_option(
					$value,
					$method,
					$args,
					$opt,
					$opt_value,
					$i++
				);
			}

			$concatenated_items .= $item;
		}

		return $concatenated_items;
	}

	/**
	 * @param $value
	 * @param $method
	 * @param $args
	 * @param $option
	 * @param $opt_value
	 * @param $index
	 *
	 * @return mixed
	 */
	public function generate_option($value, $method, $args, $option, $opt_value, $index)
	{
		$args['value'] = $opt_value;
		if (is_array($option)) {

			foreach ($option as $attr => $attr_value) {

				$args[$attr] = $attr_value;
			}
		} else {

			$args['label'] = $option;
		}

		// Check if this option is the value of the input
		if ($value === CMB2_Utils::normalize_if_numeric($opt_value)) {
			$args['checked'] = 'checked';
		}

		return $this->$method($args, $index);
	}
}
