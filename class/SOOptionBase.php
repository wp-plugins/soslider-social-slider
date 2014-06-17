<?php

class SOOptionBase {

	static public function is_used_visual_designer() {
		$ap = new SOOption();
		$ap->name = 'soslider_use_visual_designer_positioner';
		$apVal    = (get_option( $ap->get_name(), 'on' ) == 'on');
		return ! $apVal;
	}

	static public function base_options( $prefix ) {
		$options = array();
		for ( $i = 0; $i < 25; $i++ ) {
			$options[$i] = array();
		}

		$apVal = self::is_used_visual_designer();

		$bo = new SOOption();
		$bo->type     = SOOption::CHECKBOX;
		$bo->title    = __( 'Active', 'soslider' );
		$bo->name     = $prefix . '_active';
		$bo->value    = '';
		$options[0][] = $bo;

		$bo = new SOOption();
		$bo->type     = SOOption::RADIO;
		$bo->title    = __( 'Position', 'soslider' );
		$bo->name     = $prefix . '_position';
		$bo->value    = 'left';
		$bo->choices  = array(
			array( 'left', __( 'Left', 'soslider' ), ),
			array( 'right', __( 'Right', 'soslider' ), ),
		);
		$bo->value    = 'left';
		$options[0][] = $bo;

		$bo = new SOOption();
		$bo->type     = SOOption::RADIO;
		$bo->title    = __( 'Button position', 'soslider' );
		$bo->name     = $prefix . '_position_vertical';
		$bo->choices  = array(
			array( 'top', __( 'Top', 'soslider' ) ),
			array( 'middle', __( 'Middle', 'soslider' ) ),
			array( 'bottom', __( 'Bottom', 'soslider' ) ),
		);
		$bo->value    = 'middle';
		$bo->visible  = $apVal;
		$options[0][] = $bo;

		$bo = new SOOption();
		$bo->type         = SOOption::TEXT;
		$bo->title        = __( 'Width', 'soslider' );
		$bo->name         = $prefix . '_width';
		$bo->suffix       = 'px';
		$bo->value        = '200';
		$bo->value_filter = 'intval';
		$bo->visible      = $apVal;
		$options[5][]     = $bo;

		$bo = new SOOption();
		$bo->type         = SOOption::TEXT;
		$bo->title        = __( 'Height', 'soslider' );
		$bo->name         = $prefix . '_height';
		$bo->suffix       = 'px';
		$bo->value        = '500';
		$bo->value_filter = 'intval';
		$bo->visible      = $apVal;
		$options[5][]     = $bo;

		$bo = new SOOption();
		$bo->type         = SOOption::TEXT;
		$bo->title        = __( 'Border', 'soslider' );
		$bo->name         = $prefix . '_border';
		$bo->suffix       = 'px';
		$bo->value        = '5';
		$bo->value_filter = 'intval';
		$options[10][]    = $bo;

		$bo = new SOOption();
		$bo->type      = SOOption::COLOR;
		$bo->title     = __( 'Border color', 'soslider' );
		$bo->name      = $prefix . '_border_color';
		$options[10][] = $bo;

		$bo = new SOOption();
		$bo->type      = SOOption::COLOR;
		$bo->title     = __( 'Background color', 'soslider' );
		$bo->name      = $prefix . '_background_color';
		$options[10][] = $bo;

		$bo = new SOOption();
		$bo->type         = SOOption::TEXT;
		$bo->title        = __( 'CSS z-index', 'soslider' );
		$bo->name         = $prefix . '_z_index';
		$bo->value_filter = 'intval';
		$bo->value        = 1000;
		$options[11][]    = $bo;

		$bo = new SOOption();
		$bo->type = SOOption::MULTIPLE;

		$sub1 = new SOOption();
		$sub1->title        = __( 'Left top', 'soslider' );
		$sub1->type         = SOOption::TEXT;
		$sub1->suffix       = 'px';
		$sub1->name         = $prefix . '_round_left_top';
		$sub1->value_filter = 'intval';
		$sub1->value        = 0;
		$bo->choices[]      = $sub1;

		$sub1 = new SOOption();
		$sub1->title        = __( 'Left bottom', 'soslider' );
		$sub1->type         = SOOption::TEXT;
		$sub1->suffix       = 'px';
		$sub1->name         = $prefix . '_round_left_bottom';
		$sub1->value_filter = 'intval';
		$sub1->value        = 0;
		$bo->choices[]      = $sub1;

		$sub1 = new SOOption();
		$sub1->title        = __( 'Right top', 'soslider' );
		$sub1->type         = SOOption::TEXT;
		$sub1->suffix       = 'px';
		$sub1->name         = $prefix . '_round_right_top';
		$sub1->value_filter = 'intval';
		$sub1->value        = 0;
		$bo->choices[]      = $sub1;

		$sub1 = new SOOption();
		$sub1->title   = __( 'Right bottom', 'soslider' );
		$sub1->type    = SOOption::TEXT;
		$sub1->suffix  = 'px';
		$sub1->name    = $prefix . '_round_right_bottom';
		$bo->choices[] = $sub1;
		
		$bo->title          = __( 'Rounded corners', 'soslider' );
		$sub1->value_filter = 'intval';
		$sub1->value        = 0;
		$options[11][]      = $bo;

		return $options;
	}

}
