<?php

/**
 * CMB select field type
 *
 * @since     2.2.2
 *
 * @category  WordPress_Plugin
 * @package   CMB2
 * @author    CMB2 team
 * @license   GPL-2.0+
 * @link      https://cmb2.io
 */
class CMB2_Type_Select extends CMB2_Type_Multi_Base
{
	
	public function render()
	{
		$a = $this->parse_args('select', [
			'class'   => 'cmb2_select',
			'name'    => $this->_name(),
			'id'      => $this->_id(),
			'desc'    => $this->_desc(true),
			'options' => $this->concat_items(),
		]);
		
		if (isset($a['multiple'])
			&& $a['multiple']) {
			
			$a['name'] = $a['name'] . '[]';
		}
		
		if ((!isset($a['data-value'])
				|| !$a['data-value'])
			&& isset($this->types->field->value)) {
			
			$a['data-value'] = $this->types->field->value;
		}
		$attrs = $this->concat_attrs($a, ['desc', 'options']);
		
		return $this->rendered(
			sprintf('<select%s>%s</select>%s', $attrs, $a['options'], $a['desc'])
		);
	}
}
