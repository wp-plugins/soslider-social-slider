<?php

abstract class SOAbstractHandler {

	static public function _set_defaults_options( $bo ) {
		$uuid = uniqid();
		if ( null == $bo ) {
			$bo = null;
		}
		foreach ( $bo as $items ) {
			foreach ( $items as $item ) {
				if ( trim( $item->name ) != '' ) {
					$v = get_option( $item->get_name(), $uuid );
					if ( $v == $uuid ) {
						update_option( $item->get_name(), $item->value );
					}
				}
			}
		}
	}

	static public function _menu_register( $modules ) {
		return $modules;
	}

	static public function _handler_init() {

	}

	static public function _active_handler( $val ) {
		return $val;
	}

	static public function _handle_output() {

	}

	static public function _handler_state( $states ) {
		return $states;
	}

	static public function _handler_to_vs( $handlers ) {
		return $handlers;
	}

	static public function _set_left_slider( $imgno, $order, $alignment, $dimensions ) {

	}

	static public function _set_right_slider( $imgno, $order, $alignment, $dimensions ) {

	}

	static public function _set_disabled_slider() {

	}

	static public function _selected_button_height_p( $v, $name, $sizes = array() ) {
		$nb = new SOOption();
		$nb->name = $name . '_use_custom_slider_button';
		$cs = get_option( $nb->get_name(), 0 );
		if ( $cs ) {
			$nb->name = $name . '_cutom_slider_button_dimensions';
			$dim = get_option( $nb->get_name(), null );
			if ( null == $dim ) {
				return 0;
			} else {
				return intval( $dim['height'] );
			}
		} else {
			$nb->name = $name . '_slider_button';
			$v = get_option( $nb->get_name(), 0 );
			if ( isset( $sizes[$v] ) ) {
				return $sizes[$v]['height'];
			} else {
				return 0;
			}
		}
	}

	static public function _set_slider_offset( $offset, $min_height ) {

	}

	static public function _max_height_for_left( $height ) {
		return $height;
	}

	static public function _max_height_for_right( $height ) {
		return $height;
	}

	static public function _get_fields() {
		
	}

}
