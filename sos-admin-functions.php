<?php
add_action( 'soslider_activation_hook', '__save_defaults' );

function __soslider_main_options() {
	?><div class="wrap">
		<h3><?php _e( 'SoSlider Main Settings', 'soslider' ); ?></h3>
		<?php include 'version/full.php'; ?>
	</div><?php
	if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] == 'true' ) {
		echo '<h4 class="so-set-upd">' . __( 'Settings updated', 'soslider' ) . '</h4>';
		$custom_handler = apply_filters( 'soslider_custom_handler', false );
		if ( $custom_handler ) {
			SOCustomSlider::_after_options_save();
		}
	}
	$opts = array();
	$opt = new SOOption();
	$opt->name  = 'soslider_sliders_active';
	$opt->title = __( 'Sliders are active on webpage', 'soslider' );
	$opt->value = 'on';
	$opt->type  = SOOption::CHECKBOX;
	array_push( $opts, $opt );

	$opt = new SOOption();
	$opt->name  = 'soslider_sliders_mobile_active';
	$opt->title = __( 'Sliders are active for mobile devices', 'soslider' );
	$opt->value = 'on';
	$opt->type  = SOOption::CHECKBOX;
	array_push( $opts, $opt );

	$opt  = new SOOption();
	$opt->name    = 'soslider_slider_behaviour';
	$opt->title   = __( 'Slider event', 'soslider' );
	$opt->value   = 'mouseover';
	$opt->type    = SOOption::RADIO;
	$opt->choices = array(
		array( 'mouseover', __( 'mouseover', 'soslider' ) ),
		array( 'click', __( 'click', 'soslider' ) ),
	);
	array_push( $opts, $opt );

	$opt = new SOOption();
	$opt->name  = 'soslider_slider_speed';
	$opt->title = __( 'Slider speed', 'soslider' );
	$opt->value = 500;
	$opt->value_filter = 'intval';
	$opt->type  = SOOption::TEXT;
	array_push( $opts, $opt );

	$opt = new SOOption();
	$opt->name  = 'soslider_use_visual_designer_positioner';
	$opt->title = __( 'Use auto positioning icon', 'soslider' );
	$opt->value = 'on';
	$opt->type  = SOOption::CHECKBOX;
	array_push( $opts, $opt );

	$opt = new SOOption();
	$opt->name   = 'soslider_get_disable_parameter';
	$opt->title  = __( 'Request GET parameter to disable sliders', 'soslider' );
	$opt->suffix = __( 'If this parameter will occur in URL sliders will not be visible', 'soslider' );
	$opt->value  = '';
	$opt->type   = SOOption::TEXT;
	array_push( $opts, $opt );

	print '<form method="post" action="options.php"> ';
	settings_fields( 'soslider_base' );
	do_settings_fields( 'soslider_base', '' );
	?>
	<table class="form-table">
	<?php
	foreach ( $opts as $item ) {
		$item->value = get_option( $item->get_name(), $item->value );
		?>
			<tr valign="top"<?php echo $item->get_classes();
		if ( ! $item->visible ) {
			echo " style='display: none;'";
		}
			?>>
				<th scopre="row"><?php echo $item->get_title() ?></th>
				<td>
		<?php echo $item->render_input(); ?>
				</td>
			</tr>
	<?php } ?>
	</table>
	<?php
	submit_button( __( 'Save settings', 'soslider' ) );
	?></form><?php
	$active_sliders = apply_filters( 'soslider_handlers_state', array() );
	?>
	<table class="form-table">
		<thead><th><?php _e( 'Slider', 'soslider' ); ?></th><th><?php _e(
			'State',
			'soslider'
		); ?></th></thead>
	<tbody>
	<?php
	foreach ( $active_sliders as $slider => $state ) {
		echo sprintf(
			'<tr style="color: %s;"><td>%s</td><td>%s</td></tr>',
			($state ? 'green' : 'red' ), $slider,
			($state ? __( 'Active', 'soslider' ) : __( 'Inactive', 'soslider' ) )
		);
	}
	?>
	</tbody>
	</table>
	<?php
}

function __soslider_register_post_types() {
	$r = register_post_type(
		'soslider_cslider',
		array(
			'label' => 'Custom Sliderr',
			'labels' => array(
				'name' => 'Custom Slider',
				'singular_name' => 'Custom Slider',
				'all_items' => 'Custom Sliders',
				'add_new' => 'Add new Custom Slider',
				'add_new_item' => 'Add new Custom Slider',
				'edit_item' => 'Edit Custom Slider',
				'new_item' => 'New Custom Slider',
				'view_item' => 'View Custom Slider',
				'search_items' => 'Search Custom Slider',
				'not_found' => 'No Custom Sliders found',
				'not_found_in_trans' => 'No Custom Slider found in Trash',
			),
			'description' => 'Custom Slider Creation',
			'public' => true,
			'exclude_from_search' => true,
			'publicly_queryable' => false,
			'show_ui' => true,
			'show_in_nav_menus' => false,
			'show_in_admin_bar' => false,
			'show_in_menu' => false,
			'capability_type' => 'page',
			'supports' => array( 'title' ),
			'hierarchical' => false,
			'rewrite' => false,
		)
	);

	if ( $r instanceof WP_Error ) {
		throw new Exception( 'Unable to register Custom Slider post type.' );
	}
}

function __soslider_updated_messages_filter( $messages ) {
	global $post, $post_ID;
	$messages['soslider_cslider'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => sprintf(
			__( 'Custom Slider updated. <a href="%s">View Custom Slider</a>' ),
			esc_url( get_permalink( $post_ID ) )
		),
		2 => __( 'Custom field updated.', 'soslider' ),
		3 => __( 'Custom field deleted.', 'soslider' ),
		4 => __( 'Fpr, updated.', 'soslider' ),
		/* translators: %s: date and time of the revision */
		5 => isset( $_GET['revision'] ) ? sprintf( __( 'Custom Slider restored to revision from %s', 'soslider' ), wp_post_revision_title( ( int ) $_GET['revision'], false ) ) : false,
		6 => sprintf(
			__( 'Custom Slider published. <a href="%s">View Custom Slider</a>', 'soslider' ),
			esc_url( get_permalink( $post_ID ) )
		),
		7 => __( 'Custom Slider saved.', 'soslider' ),
		8 => sprintf(
			__( 'Custom Slider submitted. <a target="_blank" href="%s">Preview Custom Slider</a>' ),
			esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) )
		),
		9 => sprintf(
			__( 'Custom Slider scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Custom Slider</a>', 'soslider' ),
			date_i18n( __( 'M j, Y @ G:i', 'soslider' ), strtotime( $post->post_date ) ),
			esc_url( get_permalink( $post_ID ) )
		),
		10 => sprintf(
			__( 'Custom Slider draft updated. <a target="_blank" href="%s">Preview Custom Slider</a>' ),
			esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) )
		),
	);

	return $messages;
}

function __soslider_clear_meta_boxes( $post ) {
	global $wp_meta_boxes;
	global $post_type;
	if ( $post_type != 'soslider_cslider' ) {
		return;
	}
	$new_meta_boxes = array();
	$allowed = array( 'soslider_cslider' );
	foreach ( $wp_meta_boxes as $mbid => $mb ) {
		if ( in_array( $mbid, $allowed ) ) {
			$new_meta_boxes[$mbid] = $mb;
		}
	}

	if ( isset( $new_meta_boxes['soslider_cslider'] ) ) {
		$new_meta_boxes['soslider_cslider']['advanced']['default'] = array();
		$sd = $new_meta_boxes['soslider_cslider']['side']['core']['submitdiv'];
		$new_meta_boxes['soslider_cslider']['side']['core'] = array(
			'submitdiv' => $sd,
		);
		$t = isset( $new_meta_boxes['soslider_custom_slider']['normal']['core']['pps_mb1'] ) ? $new_meta_boxes['soslider_custom_slider']['normal']['core']['pps_mb1'] : null;
		$new_meta_boxes['soslider_custom_slider']['normal'] = array(
			'core' => array(
				'pps_mb1' => $t,
			),
		);
	}

	$wp_meta_boxes = $new_meta_boxes;
}

function __soslider_custom_sliders( $sliders ) {
	$slajdero = get_option( 'soslider_custom_sliders', null );
	if ( is_array( $slajdero ) ) {
		array_splice( $sliders, count( $sliders ), 0, $slajdero );
		return $sliders;
	}

	if ( ! is_array( $sliders ) ) {
		$sliders = array();
	}
	$args = array(
		'posts_per_page' => 30,
		'offset' => 0,
		'category' => '',
		'orderby' => 'title',
		'order' => 'ASC',
		'include' => '',
		'exclude' => '',
		'meta_key' => '',
		'meta_value' => '',
		'post_type' => 'soslider_cslider',
		'post_mime_type' => '',
		'post_parent' => '',
		'post_status' => 'publish',
		'suppress_filters' => true,
	);
	$query = new WP_Query( $args );
	while ( $query->have_posts() ) {
		$query->the_post();
		$post = get_post();
		array_push( $sliders, array( 'id' => $post->ID, 'title' => $post->post_title ) );
	}
	update_option( 'soslider_custom_sliders', $sliders );
	return $sliders;
}

function __soslider_add_custom_sliders_to_menu() {
	$sliders = apply_filters( 'soslider_custom_sliders', array() );
	foreach ( $sliders as $obj ) {
		$p = array( 'SOCustomSlider', '_menu_handler' );
		add_submenu_page(
			'soslider_moptions', __( $obj['title'], 'soslider' ),
			__( $obj['title'], 'soslider' ), 'manage_options', 'soslider_custom_' . $obj['id'], $p
		);
	}
}

function __soslider_redirect_after_save( $location ) {
	global $post_type;

	if ( $post_type == 'soslider_cslider' ) {
		$location = admin_url( 'edit.php?post_type=' . $post_type );
	}

	return $location;
}

function __soslider_save_post( $post_id ) {
	$post = get_post( $post_id );
	if ( $post->post_type == 'soslider_cslider' ) {
		update_option( 'soslider_custom_sliders', null );
		SOCustomSlider::_after_options_save();
	}
}

function __soslider_visual_designer() {
	wp_enqueue_script( 'jquery' );
	wp_enqueue_style(
		'jquery-style',
		'http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/themes/smoothness/jquery-ui.css'
	);
	wp_enqueue_script( 'jquery-ui-sortable' );
	wp_register_script(
		'sos-visual-conf',
		SOSLIDER_PLUGIN_URL . 'js/admin-visual-conf.min.js'
	);
	wp_enqueue_script( 'sos-visual-conf' );
	$handlers   = apply_filters( 'soslider_handlers_to_visual_config', null );
	$l_handlers = array();
	$c_handlers = array();
	$r_handlers = array();
	foreach ( $handlers as $name => $hinfo ) {
		if ( true == $hinfo['state'] && 'left' == $hinfo['align'] ) {
			$l_handlers[$name] = $hinfo;
		}
		if ( true == $hinfo['state'] && 'right' == $hinfo['align'] ) {
			$r_handlers[$name] = $hinfo;
		}
		if ( true != $hinfo['state'] ) {
			$c_handlers[$name] = $hinfo;
		}
	}
	if ( count( $l_handlers ) > 0 )
		@uksort( $l_handlers, '__soslider_visual_sort' );
	if ( count( $r_handlers ) > 0 )
		@uksort( $r_handlers, '__soslider_visual_sort' );
	if ( count( $c_handlers ) > 0 )
		@uksort( $c_handlers, '__soslider_visual_sort' );

	$nb = new SOOption();
	$nb->name    = 'slider_l_alignment';
	$l_alignment = get_option( $nb->get_name(), 'middle' );
	$nb->name    = 'slider_r_alignment';
	$r_alignment = get_option( $nb->get_name(), 'middle' );
	$nb->name    = 'slider_l_height';
	$l_height    = get_option( $nb->get_name(), 500 );
	$nb->name    = 'slider_l_width';
	$l_width     = get_option( $nb->get_name(), 300 );
	$nb->name    = 'slider_r_height';
	$r_height    = get_option( $nb->get_name(), 500 );
	$nb->name    = 'slider_r_width';
	$r_width     = get_option( $nb->get_name(), 300 );
	?><div class="wrap">
		<h3><?php _e( 'Sliders visual configuration', 'soslider' ); ?></h3>
		<?php include 'version/full.php'; ?>
		<center>
	<?php
	foreach ( $handlers as $name => $hinfo ) {
		if ( ! isset( $hinfo['custom'] ) ) {
			$dis = $name;
		} else {
			$dis = $hinfo['custom'];
		}
		echo '<div data-id="' . $dis . '" class="images-' . $dis . ' sos-conf-images" style="display: none;">';
		echo '<h3>' . $name . '&nbsp;<span class="ui-icon ui-icon-close"></span></h3>';
		foreach ( $hinfo['images'] as $o ) {
			echo '<span data-val="' . $o[0] . '" ' . (($o[0] == $hinfo['button']) ? ' class="sos-image-selected"' : '') . '>';
			echo $o[1];
			echo '</span>';
		}
		echo '</div>';
	}
	?>
		</center>
		<center>
			<table class="sos-table">
				<tr>
					<td valign="top">
						<span class="sos-title"><?php _e( 'Left', 'soslider' ); ?></span><br/><?php _e( 'Alignment', 'soslider' ); ?>
						<input type="radio" name="left_alignment" value="top"<?php
								if ( 'top' == $l_alignment ) {
									echo ' checked';
								}
								?>><?php _e( 'top', 'soslider' ); ?>&nbsp;
						<input type="radio" name="left_alignment" value="middle"<?php
								if ( 'middle' == $l_alignment ) {
									echo ' checked';
								}
								?>><?php _e( 'middle', 'soslider' ); ?>&nbsp;
						<input type="radio" name="left_alignment" value="bottom"<?php
							if ( 'bottom' == $l_alignment ) {
								echo ' checked';
							}
							?>><?php _e( 'bottom', 'soslider' ); ?>&nbsp;
						<div class="sos-conf-left">
							<ul class="sos-sortable">
								<?php
								foreach ( $l_handlers as $name => $hinfo ) {
									if ( true == $hinfo['state'] && 'left' == $hinfo['align'] ) {
										if ( ! isset( $hinfo['custom'] ) ) {
											$dis = $name;
										} else {
											$dis = $hinfo['custom'];
										}
										echo '<li data-name="' . $dis . '" data-image="' . $hinfo['button'] . '"><span class="ui-icon ui-icon-arrow-1-w sos-arrows"></span>' . $name . '<span class="ui-icon  ui-icon-arrow-1-e sos-arrows"></span><span class="ui-icon ui-icon-trash sos-conf-trash"></span><span class="ui-icon ui-icon-pencil sos-conf-edit"></span></li>';
									}
								}
								?>
							</ul>
						</div>
					</td>
					<td valign="top"><span class="sos-title"><?php _e( 'Inactive', 'soslider' ); ?></span><br/><br/>
						<div class="sos-conf-center">
							<ul class="sos-sortable">
						<?php
							foreach ( $c_handlers as $name => $hinfo ) {
								if ( false == $hinfo['state'] ) {
									if ( ! isset( $hinfo['custom'] ) ) {
										$dis = $name;
									} else {
										$dis = $hinfo['custom'];
									}
									echo '<li data-name="' . $dis . '" data-image="' . $hinfo['button'] . '"><span class="ui-icon ui-icon-arrow-1-w sos-arrows"></span>' . $name . '<span class="ui-icon  ui-icon-arrow-1-e sos-arrows"></span><span class="ui-icon ui-icon-trash sos-conf-trash"></span><span class="ui-icon ui-icon-pencil sos-conf-edit"></span></li>';
								}
							}
							?>
							</ul>
						</div></td>
					<td valign="top"><span class="sos-title"><?php _e( 'Right', 'soslider' ); ?></span><br/><?php _e( 'Alignment', 'soslider' ); ?>
						<input type="radio" name="right_alignment" value="top"<?php
								if ( 'top' == $r_alignment ) {
									echo ' checked';
								}
								?>><?php _e( 'top', 'soslider' ); ?>&nbsp;
						<input type="radio" name="right_alignment" value="middle"<?php
								if ( 'middle' == $r_alignment ) {
									echo ' checked';
								}
								?>><?php _e( 'middle', 'soslider' ); ?>&nbsp;
						<input type="radio" name="right_alignment" value="bottom"<?php
								if ( 'bottom' == $r_alignment ) {
									echo ' checked';
								}
								?>><?php _e( 'bottom', 'soslider' ); ?>&nbsp;
						<div class="sos-conf-right"><ul class="sos-sortable">
	<?php
	foreach ( $r_handlers as $name => $hinfo ) {
		if ( true == $hinfo['state'] && 'right' == $hinfo['align'] ) {
			if ( ! isset( $hinfo['custom'] ) ) {
				$dis = $name;
			} else {
				$dis = $hinfo['custom'];
			}
			echo '<li data-name="' . $dis . '" data-image="' . $hinfo['button'] . '"><span class="ui-icon ui-icon-arrow-1-w sos-arrows"></span>' . $name . '<span class="ui-icon  ui-icon-arrow-1-e sos-arrows"></span><span class="ui-icon ui-icon-trash sos-conf-trash"></span><span class="ui-icon ui-icon-pencil sos-conf-edit"></span></li>';
		}
	}
	?>
							</ul></div></td>
				</tr>
				<tr>
					<td valign="top">
	<?php _e( 'Slider height', 'soslider' ); ?><br/><input type="text" id="left_height" value="<?php echo $l_height; ?>" /><br/>
	<?php _e( 'Slider width', 'soslider' ); ?><br/><input type="text" id="left_width" value="<?php echo $l_width; ?>" /><br/>
					</td>
					<td></td>
					<td valign="top">
	<?php _e( 'Slider height', 'soslider' ); ?><br/><input type="text" id="right_height" value="<?php echo $r_height; ?>" /><br/>
	<?php _e( 'Slider width', 'soslider' ); ?><br/><input type="text" id="right_width" value="<?php echo $r_width; ?>" /><br/>
					</td>
				</tr>
			</table>
			<br/>
		</center>
		<button class="button button-primary" id="btnSave"><?php _e( 'Save', 'soslider' ); ?></button><span id="sos-saving" style="display: none; font-weight: bold;"><?php _e( 'Saving... Please wait...', 'soslider' ); ?></span>
		<br/>
	</div>
	<?php
}

function __soslider_visual_config( $internal_call = false ) {
	$nb = new SOOption();
	$nb->name = 'sliders_order';
	update_option( $nb->get_name(), array() );

	$p             = $_POST;
	$l_alignment   = $p['l_alignment'];
	$r_alignment   = $p['r_alignment'];
	$o_l_alignment = $l_alignment;
	$o_r_alignment = $r_alignment;

	if ( 'middle' == $l_alignment ) {
		$l_alignment = 'top';
	}
	if ( 'middle' == $r_alignment ) {
		$r_alignment = 'top';
	}

	if ( isset( $p['l_dimensions']['height'] ) ) {
		$p['l_dimensions']['height'] = intval( $p['l_dimensions']['height'] );
		if ( $p['l_dimensions']['height'] < 0 ) {
			$p['l_dimensions']['height'] = 500;
		}
	}
	if ( isset( $p['l_dimensions']['width'] ) ) {
		$p['l_dimensions']['width'] = intval( $p['l_dimensions']['width'] );
		if ( $p['l_dimensions']['width'] < 0 ) {
			$p['l_dimensions']['width'] = 300;
		}
	}
	if ( isset( $p['r_dimensions']['height'] ) ) {
		$p['r_dimensions']['height'] = intval( $p['r_dimensions']['height'] );
		if ( $p['r_dimensions']['height'] < 0 ) {
			$p['r_dimensions']['height'] = 500;
		}
	}
	if ( isset( $p['r_dimensions']['width'] ) ) {
		$p['r_dimensions']['width'] = intval( $p['r_dimensions']['width'] );
		if ( $p['r_dimensions']['width'] < 0 ) {
			$p['r_dimensions']['width'] = 300;
		}
	}

	$nb->name = 'slider_l_alignment';
	update_option( $nb->get_name(), $o_l_alignment );
	$nb->name = 'slider_r_alignment';
	update_option( $nb->get_name(), $o_r_alignment );
	$nb->name = 'slider_l_height';
	update_option( $nb->get_name(), $p['l_dimensions']['height'] );
	$nb->name = 'slider_l_width';
	update_option( $nb->get_name(), $p['l_dimensions']['width'] );
	$nb->name = 'slider_r_height';
	update_option( $nb->get_name(), $p['r_dimensions']['height'] );
	$nb->name = 'slider_r_width';
	update_option( $nb->get_name(), $p['r_dimensions']['width'] );

	$poz = 1;
	if ( isset( $p['l_items'] ) )
	foreach ( $p['l_items'] as $itm ) {
		$mtcs = array();
		if ( ! preg_match( '/custom_(\d+)/i', $itm['id'], $mtcs ) ) {
			do_action(
				'soslider_set_left_slider_' . strtolower( $itm['id'] ),
				$itm['image'],
				$poz++,
				$l_alignment,
				array( 'width' => $p['l_dimensions']['width'], 'height' => $p['l_dimensions']['height'], )
			);
		} else {
			do_action(
				'soslider_set_left_slider_custom', $mtcs[1], $poz++,
				$r_alignment,
				array( 'width' => $p['l_dimensions']['width'], 'height' => $p['l_dimensions']['height'], )
			);
		}
	}
	$poz = 1;
	if ( isset( $p['r_items'] ) )
	foreach ( $p['r_items'] as $itm ) {
		$mtcs = array();
		if ( ! preg_match( '/custom_(\d+)/i', $itm['id'], $mtcs ) ) {
			do_action(
				'soslider_set_right_slider_' . strtolower( $itm['id'] ),
				$itm['image'],
				$poz++,
				$r_alignment,
				array( 'width' => $p['r_dimensions']['width'], 'height' => $p['r_dimensions']['height'], )
			);
		} else {
			do_action(
				'soslider_set_right_slider_custom',
				$mtcs[1],
				$poz++,
				$r_alignment,
				array( 'width' => $p['r_dimensions']['width'], 'height' => $p['r_dimensions']['height'], )
			);
		}
	}
	if ( isset( $p['c_items'] ) )
	foreach ( $p['c_items'] as $itm ) {
		$mtcs = array();
		if ( ! preg_match( '/custom_(\d+)/i', $itm['id'], $mtcs ) ) {
			do_action( 'soslider_set_disabled_slider_' . strtolower( $itm['id'] ) );
		} else {
			do_action( 'soslider_set_disabled_slider_custom', $mtcs[1] );
		}
	}

	$min_left_height  = 0;
	$min_right_height = 0;
	if ( isset( $p['l_items'] ) )
	foreach ( $p['l_items'] as &$itm ) {
		$mtcs = array();
		if ( ! preg_match( '/custom_(\d+)/i', $itm['id'], $mtcs ) ) {
			$itm['button_height'] = apply_filters(
				'soslider_selected_button_height_' . strtolower( $itm['id'] ),
				false
			);
		} else {
			$itm['button_height'] = apply_filters(
				'soslider_selected_button_height_custom',
				$mtcs[1],
				false
			);
		}
		$min_left_height += ( int ) $itm['button_height'];
		$min_left_height += 5;
	}
	if ( isset( $p['r_items'] ) )
	foreach ( $p['r_items'] as &$itm2 ) {
		$mtcs = array();
		if ( ! preg_match( '/custom_(\d+)/i', $itm2['id'], $mtcs ) ) {
			$itm2['button_height'] = apply_filters(
				'soslider_selected_button_height_' . strtolower( $itm2['id'] ),
				false
			);
		} else {
			$itm2['button_height'] = apply_filters(
				'soslider_selected_button_height_custom',
				$mtcs[1],
				false
			);
		}
		$min_right_height += ( int ) $itm2['button_height'];
		$min_right_height += 5;
	}

	$max_left         = apply_filters( 'soslider_max_height_for_left', 0 );
	$max_right        = apply_filters( 'soslider_max_height_for_right', 0 );
	$max_left_custom  = apply_filters( 'soslider_max_height_for_left_custom', 0 );
	$max_right_custom = apply_filters( 'soslider_max_height_for_right_custom', 0 );

	$o_left_min_height  = $min_left_height;
	$o_right_min_height = $min_right_height;

	if ( $max_left_custom > $max_left ) {
		$max_left = $max_left_custom;
	}
	if ( $max_right_custom > $max_right ) {
		$max_right = $max_right_custom;
	}

	if ( $max_left > $min_left_height ) {
		$min_left_height = $max_left;
	}
	if ( $max_right > $min_right_height ) {
		$min_right_height = $max_right;
	}


	if ( 'top' == $o_l_alignment ) {
		$top_offset = 0;
		if ( isset( $p['l_items'] ) )
		foreach ( $p['l_items'] as $itmx ) {
			$mtcs = array();
			if ( ! preg_match( '/custom_(\d+)/i', $itmx['id'], $mtcs ) ) {
				do_action(
					'soslider_set_slider_offset_' . strtolower( $itmx['id'] ),
					$top_offset,
					$min_left_height
				);
			} else {
				do_action(
					'soslider_set_slider_offset_custom',
					$mtcs[1],
					$top_offset,
					$min_left_height
				);
			}
			$top_offset += $itmx['button_height'];
			$top_offset += 5;
		}
	}
	if ( 'top' == $o_r_alignment ) {
		$top_offset = 0;
		if ( isset( $p['r_items'] ) )
		foreach ( $p['r_items'] as $itmy ) {
			$mtcs = array();
			if ( ! preg_match( '/custom_(\d+)/i', $itmy['id'], $mtcs ) ) {
				do_action(
					'soslider_set_slider_offset_' . strtolower( $itmy['id'] ),
					$top_offset,
					$min_right_height
				);
			} else {
				do_action(
					'soslider_set_slider_offset_custom',
					$mtcs[1],
					$top_offset,
					$min_right_height
				);
			}
			$top_offset += $itmy['button_height'];
			$top_offset += 5;
		}
	}

	if ( 'bottom' == $o_l_alignment ) {
		$top_offset = 0;
		if ( isset( $p['l_items'] ) ) {
			$p['l_items'] = array_reverse( $p['l_items'] );
			foreach ( $p['l_items'] as $itmx ) {
				$mtcs = array();
				if ( ! preg_match( '/custom_(\d+)/i', $itmx['id'], $mtcs ) ) {
					do_action(
						'soslider_set_slider_offset_' . strtolower( $itmx['id'] ),
						$top_offset,
						$min_left_height
					);
				} else {
					do_action(
						'soslider_set_slider_offset_custom',
						$mtcs[1],
						$top_offset,
						$min_left_height
					);
				}
				$top_offset -= $itmx['button_height'];
				$top_offset -= 5;
			}
		}
	}

	if ( 'bottom' == $o_r_alignment ) {
		$top_offset = 0;
		if ( isset( $p['r_items'] ) ) {
			$p['r_items'] = array_reverse( $p['r_items'] );
			foreach ( $p['r_items'] as $itmy ) {
				$mtcs = array();
				if ( ! preg_match( '/custom_(\d+)/i', $itmy['id'], $mtcs ) ) {
					do_action(
						'soslider_set_slider_offset_' . strtolower( $itmy['id'] ),
						$top_offset,
						$min_right_height
					);
				} else {
					do_action(
						'soslider_set_slider_offset_custom',
						$mtcs[1],
						$top_offset,
						$min_right_height
					);
				}
				$top_offset -= $itmy['button_height'];
				$top_offset -= 5;
			}
		}
	}

	if ( 'middle' == $o_l_alignment ) {
		$top_offset = intval( ($max_left - $o_left_min_height) / 2 );
		if ( 0 > $top_offset ) {
			$top_offset = 0;
		}
		if ( isset( $p['l_items'] ) )
		foreach ( $p['l_items'] as $fuxu ) {
			$mtcs = array();
			if ( ! preg_match( '/custom_(\d+)/i', $fuxu['id'], $mtcs ) ) {
				do_action(
					'soslider_set_slider_offset_' . strtolower( $fuxu['id'] ),
					$top_offset,
					$min_left_height
				);
			} else {
				do_action(
					'soslider_set_slider_offset_custom',
					$mtcs[1],
					$top_offset,
					$min_left_height
				);
			}
			$top_offset += $fuxu['button_height'];
			$top_offset += 5;
		}
	}

	if ( 'middle' == $o_r_alignment ) {
		$top_offset = intval( ($max_right - $o_right_min_height) / 2 );
		if ( 0 > $top_offset ) {
			$top_offset = 0;
		}
		if ( isset( $p['r_items'] ) )
		foreach ( $p['r_items'] as $fuxu ) {
			$mtcs = array();
			if ( ! preg_match( '/custom_(\d+)/i', $fuxu['id'], $mtcs ) ) {
				do_action(
					'soslider_set_slider_offset_' . strtolower( $fuxu['id'] ),
					$top_offset,
					$min_right_height
				);
			} else {
				do_action(
					'soslider_set_slider_offset_custom',
					$mtcs[1],
					$top_offset,
					$min_right_height
				);
			}
			$top_offset += $fuxu['button_height'];
			$top_offset += 5;
		}
	}

	do_action( 'soslider_custom_post_save' );
	if ( ! $internal_call ) {
		$a = array(
			'result' => 0,
			'message' => __( 'Settings have been saved.', 'soslider' ),
		);
		echo json_encode( $a );
		die();
	}
}

function __soslider_visual_sort( $a, $b ) {
	$nb = new SOOption();
	$nb->name = 'sliders_order';
	$v        = get_option( $nb->get_name(), array() );
	$felem    = 1000000;
	$selem    = 1000000;
	if ( ! isset( $v['left'] ) ) {
		$v['left'] = array();
	}
	if ( ! isset( $v['right'] ) ) {
		$v['right'] = array();
	}
	foreach ( $v['left'] as $ord => $key ) {
		if ( $key == strtolower( $a ) ) {
			$felem = $ord;
			break;
		}
	}
	foreach ( $v['left'] as $ord => $key ) {
		if ( $key == strtolower( $b ) ) {
			$selem = $ord;
			break;
		}
	}

	foreach ( $v['right'] as $ord => $key ) {
		if ( $key == strtolower( $a ) ) {
			$felem = $ord;
			break;
		}
	}
	foreach ( $v['right'] as $ord => $key ) {
		if ( $key == strtolower( $b ) ) {
			$selem = $ord;
			break;
		}
	}

	if ( $felem < $selem )
		return -1;
	if ( $felem == $selem )
		return 0;
	if ( $felem > $selem )
		return 1;
}

function __soslider_after_save_item() {
	$r = $_REQUEST;
	if ( isset( $r['action'] ) && $r['action'] == 'sos_visual_config' ) {
		return;
	}
	$nb = new SOOption();
	$nb->name = 'sliders_order';
	$orders = get_option( $nb->get_name() );
	$nb->name = 'slider_l_alignment';
	$o_l_alignment = get_option( $nb->get_name(), 'middle' );
	$nb->name = 'slider_r_alignment';
	$o_r_alignment = get_option( $nb->get_name(), 'middle' );

	$p['l_alignment'] = $o_l_alignment;
	$p['r_alignment'] = $o_r_alignment;

	$nb->name                    = 'slider_l_height';
	$p['l_dimensions']['height'] = get_option( $nb->get_name(), 500 );
	$nb->name                    = 'slider_l_width';
	$p['l_dimensions']['width']  = get_option( $nb->get_name(), 300 );
	$nb->name                    = 'slider_r_height';
	$p['r_dimensions']['height'] = get_option( $nb->get_name(), 500 );
	$nb->name                    = 'slider_r_width';
	$p['r_dimensions']['width']  = get_option( $nb->get_name(), 300 );

	$modules = apply_filters( 'soslider_handlers_to_visual_config', null );
	if ( ! isset( $orders['left'] ) ) {
		$orders['left'] = array();
	}
	if ( ! isset( $orders['right'] ) ) {
		$orders['right'] = array();
	}
	$customs = array();
	foreach ( $modules as $key => $module ) {
		$handler     = strtolower( $key );
		$handler_key = strtolower( $key );
		if ( isset( $module['custom'] ) ) {
			$handler_key = $module['custom'];
			$customs[$handler] = $handler_key;
		}
		$nb->name = $handler_key . '_active';
		$act      = (get_option( $nb->get_name(), 'off' ) == 'on');
		$nb->name = $handler_key . '_position';
		$pos      = get_option( $nb->get_name(), 'left' );
		if ( $act ) {
			if ( ! in_array( $handler, $orders[$pos] ) ) {
				$orders[$pos][] = $handler;
			}
		}
	}

	if ( isset( $orders['left'] ) ) {
		foreach ( $orders['left'] as $handler ) {
			if ( isset( $customs[$handler] ) ) {
				$handler = $customs[$handler];
			}
			$nb->name = $handler . '_active';
			$act      = (get_option( $nb->get_name(), 'off' ) == 'on');
			$nb->name = $handler . '_slider_button';
			$img      = get_option( $nb->get_name(), 0 );
			$nb->name = $handler . '_position';
			$pos      = get_option( $nb->get_name(), 'left' );
			$target   = ($act) ? 'l_items' : 'c_items';
			if ( 'left' == $pos )
				$p[$target][] = array(
					'id' => $handler,
					'image' => $img,
				);
		}
	}
	if ( isset( $orders['right'] ) ) {
		foreach ( $orders['right'] as $handler ) {
			if ( isset( $customs[$handler] ) ) {
				$handler = $customs[$handler];
			}
			$nb->name = $handler . '_active';
			$act      = (get_option( $nb->get_name(), 'off' ) == 'on');
			$nb->name = $handler . '_slider_button';
			$img      = get_option( $nb->get_name(), 0 );
			$nb->name = $handler . '_position';
			$pos      = get_option( $nb->get_name(), 'left' );
			$target   = ($act) ? 'r_items' : 'c_items';
			if ( 'right' == $pos )
				$p[$target][] = array(
					'id' => $handler,
					'image' => $img,
				);
		}
	}
	$offed = array();
	if ( isset( $p['c_items'] ) ) {
		foreach ( $p['c_items'] as $k ) {
			$offed[] = $k['id'];
		}
	}

	foreach ( $modules as $key => $module ) {
		$key      = strtolower( $key );
		$nb->name = $key . '_active';
		$act      = (get_option( $nb->get_name(), 'off' ) == 'on');
		$target   = 'c_items';
		if ( ! $act && ! isset( $offed[$key] ) ) {
			$nb->name     = $key . '_slider_button';
			$img          = get_option( $nb->get_name(), 0 );
			$p[$target][] = array(
				'id' => $key,
				'image' => $img,
			);
		}
	}

	$_REQUEST['action'] = 'sos_visual_config';
	$_POST              = $p;
	__soslider_visual_config( true );
}

function __save_defaults() {
	$ops = array(
		'soslider_sliders_mobile_active' => 'on',
		'soslider_sliders_active' => 'on',
		'soslider_get_disable_parameter' => '',
		'soslider_use_visual_designer_positioner' => 'on',
		'soslider_slider_behaviour' => 'mouseover',
		'soslider_slider_speed' => 500,
		'custom_sliders_content' => '',
	);
	$uuid = uniqid();
	$o    = new SOOption();
	foreach ( $ops as $op => $val ) {
		$o->name = $op;
		$v       = get_option( $o->get_name(), $uuid );
		if ( $v == $uuid ) {
			update_option( $o->get_name(), $val );
		}
	}
}