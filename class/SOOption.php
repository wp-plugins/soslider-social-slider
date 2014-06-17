<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SOOption
 *
 * @author SoSoft
 */
class SOOption {

	/**
	 *
	 * @var string
	 */
	public $title;

	/**
	 *
	 * @var mixed
	 */
	public $value;

	/**
	 *
	 * @var mixed
	 */
	public $default;

	/**
	 *
	 * @var type
	 */
	public $type;

	/**
	 *
	 * @var string
	 */
	public $name;

	/**
	 *
	 * @var string
	 */
	public $suffix;

	/**
	 *
	 * @var string
	 */
	public $prefix;

	/**
	 *
	 * @var mixed
	 */
	public $value_filter;

	/**
	 *
	 * @var array
	 */
	public $choices;

	/**
	 *
	 * @var bool
	 */
	public $multiline;

	/**
	 *
	 * @var array
	 */
	public $css_classes;

	/**
	 *
	 * @var bool
	 */
	public $visible;

	const CHECKBOX   = 'checkbox';
	const TEXT       = 'text';
	const RADIO      = 'radio';
	const COLOR      = 'color';
	const MULTIPLE   = 'multiple';
	const SELECT     = 'select';
	const MULTIMEDIA = 'multimedia';
	const EDITOR     = 'editor';

	public function __construct() {
		$this->multiline   = false;
		$this->css_classes = array();
		$this->visible     = true;
	}

	public function get_classes() {
		if ( count( $this->css_classes ) > 0 ) {
			return sprintf( " class='%s'", implode( ' ', $this->css_classes ) );
		} else {
			return null;
		}
	}

	public function get_title() {
		return $this->title;
	}

	public function get_name() {
		return 'sos_option_' . $this->name;
	}

	public function get_value() {
		return $this->value;
	}

	public function render_input() {
		if ( $this->value_filter ) {
			$this->value = call_user_func( $this->value_filter, $this->value );
		}
		$r = '';

		if ( $this->prefix ) {
			$r .= $this->prefix . '&nbsp;';
		}

		if ( $this->type == self::CHECKBOX ) {
			$r .= '<input type="checkbox" name="' . $this->get_name() . '" id="' . $this->get_name() . '"' . ( ( $this->value ) ? ' checked' : '' ) . '>';
		}

		if ( $this->type == self::TEXT ) {
			$r .= '<input type="text" name="' . $this->get_name() . '" id="' . $this->get_name() . '" value="' . $this->value . '">';
		}

		if ( $this->type == self::MULTIMEDIA ) {
			$r .= '<input type="text" name="' . $this->get_name() . '" id="' . $this->get_name() . '" value="' . $this->value . '" class="soslider-multimedia" >&nbsp;<button class="button" onclick="return SOSlider_RBTE_MediaSelect(\'' . $this->get_name() . '\');">' . 
				__( 'Choose image', 'soslider' ) . '</button>';
		}

		if ( $this->type == self::RADIO ) {
			if ( is_array( $this->choices ) ) {
				$ix = 0;
				foreach ( $this->choices as $choice ) {
					$checked = ($this->value == $choice[0]) ? ' checked' : '';
					$r .= '<input type="radio" name="' . $this->get_name() . '" value="' . $choice[0] . '" id="' . $this->get_name() . $ix . '"' . $checked . '><label for="' . $this->get_name() . $ix . '">' . $choice[1] . '</label>&nbsp;&nbsp;';
					if ( $this->multiline ) {
						$r .= '<br/>';
					}
					$ix++;
				}
			}
		}

		if ( $this->type == self::COLOR ) {
			$r .= '<input type="text" name="' . $this->get_name() . '" id="' . $this->get_name() . '" value="' . $this->value . '" class="so-color-picker">';
		}

		if ( $this->type == self::MULTIPLE ) {
			if ( is_array( $this->choices ) ) {
				$ix = 0;
				foreach ( $this->choices as $choice ) {
					$choice->value = get_option( $choice->get_name(), $choice->value );
					$r .= $choice->get_title() . '&nbsp;' . $choice->render_input() . '<br/>';
				}
			}
		}

		if ( $this->type == self::SELECT ) {
			$r .= '<select name="' . $this->get_name() . '" id="' . $this->get_name() . '">';
			foreach ( $this->choices as $i ) {
				$selected = ($i[0] == $this->value) ? ' selected' : '';
				$r .= '<option value="' . $i[0] . '"' . $selected . '>' . $i[1] . '</option>';
			}

			$r .= '</select>';
		}

		if ( $this->type == self::EDITOR ) {
			//$r .= '<textarea style="display: none;" name="' . $this->get_name() . '" id="' . $this->get_name() . '" value="' . $this->value . '">' . $this->value . '</textarea>';
			$settings = array( 'media_buttons' => true );
			wp_editor( $this->value, $this->get_name(), $settings );
		}

		if ( $this->suffix != '' ) {
			$r .= '&nbsp;' . $this->suffix;
		}
		return $r;
	}

}
