<?php

/*
  Plugin Name: SoSlider - Social Slider Lite for WordPress
  Plugin URI: http://soslider.com
  Description: Slider for Dailymotion, Facebook, Flickr, GooglePlus, Instagram, LinkedIn, Pinterest, Twitter, Vimeo, YouTube, SoundCloud or for your very custom slider.
  Author: SoSoft
  Version: 1.2.3
  Author URI: http://soslider.com
 */

if ( is_admin() ) {
	$o = WP_PLUGIN_DIR;
	if ( file_exists( $o . '/sosoft-soslider/soslider.php' ) ) {
		require_once 'includes/plugin.php';
		deactivate_plugins( __FILE__ );
		activate_plugin( $o . '/sosoft-soslider/soslider.php' );
		return;
	}
}

define( 'SOSLIDER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SOSLIDER_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'SOSLIDER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

include_once 'class/SOAbstractHandler.php';
include_once 'handlers/facebook/init.php';
require_once 'sos-admin-functions.php';
require_once 'class/SOOption.php';
require_once 'class/SOOptionBase.php';

function __soslider_init() {
	add_action( 'admin_menu', '__soslider_admin_menu' );
	add_action( 'admin_init', '__soslider_admin_init' );
	add_action( 'wp_enqueue_scripts', '__soslider_fp_init', 10000 );

	$opt = new SOOption();
	$opt->name = 'soslider_sliders_active';
	$sliders_active = get_option( $opt->get_name(), 'on' );
	$opt->name = 'soslider_sliders_mobile_active';
	$sliders_mobile_active = get_option( $opt->get_name(), 'on' );
	$opt->name = 'soslider_get_disable_parameter';
	$disable_get_parameter = get_option( $opt->get_name(), 'on' );
	$act = true;

	if ( 'on' != $sliders_active ) {
		$act = false;
	} elseif ( '' != $disable_get_parameter && isset( $_GET[$disable_get_parameter] ) ) {
		$act = false;
	} elseif ( 'on' != $sliders_mobile_active && wp_is_mobile() ) {
		$act = false;
	}

	if ( $act ) {
		add_action( 'wp_footer', '__soslider_wp_footer' );
		add_action( 'wp_print_styles', '__soslider_buld_css' );
	}
	register_deactivation_hook( __FILE__, '__gxlider_deactivation_hook' );
	register_activation_hook( __FILE__, '__gxlider_activation_hook' );
	if ( $custom_handler ) {
		__soslider_register_post_types();
	}
}

function __soslider_admin_menu() {
	$hook_suffix = add_menu_page(
		'SoSlider Options', 'SoSlider', 'manage_options',
		'soslider_moptions', '__soslider_main_options', null
	);
	add_submenu_page(
		'soslider_moptions',
		__( 'Settings', 'soslider' ),
		__( 'Settings', 'soslider' ),
		'manage_options',
		'soslider_moptions',
		'__soslider_main_options'
	);
	add_submenu_page(
		'soslider_moptions',
		__( 'Visual designer', 'soslider' ),
		__( 'Visual designer', 'soslider' ),
		'manage_options',
		'soslider_vdesigner',
		'__soslider_visual_designer'
	);

	$modules = array();
	$modules = apply_filters( 'soslider_modules', $modules );
	foreach ( $modules as $key => $value ) {
		add_submenu_page(
			'soslider_moptions',
			__( $key, 'soslider' ),
			__( $key, 'soslider' ),
			'manage_options',
			$value['ident'],
			$value['menuhandler']
		);
	}
	add_submenu_page(
		'soslider_moptions',
		__( 'Help', 'soslider' ),
		__( 'Help', 'soslider' ),
		'manage_options',
		'soslider_help',
		'_soslider_help'
	);
}

function _soslider_help() {
	wp_redirect( 'http://goo.gl/vfXniC' );
	exit();
}

function __soslider_admin_init() {
	add_action( 'wp_ajax_sos_visual_config', '__soslider_visual_config' );

	do_action( 'soslider_handlers_init' );
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'wp-color-picker' );
	wp_register_script(
		'so-admin-config',
		SOSLIDER_PLUGIN_URL . 'js/admin-config.min.js'
	);
	wp_enqueue_script( 'media-upload' );
	wp_enqueue_script( 'thickbox' );
	wp_enqueue_script( 'jquery' );
	wp_register_script( 'soslider-multi', SOSLIDER_PLUGIN_URL . 'js/multi.min.js' );
	wp_enqueue_script( 'soslider-multi' );
	wp_register_style(
		'so-admin',
		SOSLIDER_PLUGIN_URL . 'assets/soslider-admin.min.css'
	);
	wp_enqueue_style( 'so-admin' );
	wp_enqueue_style( 'thickbox' );

	$o = new SOOption();
	$o->name = 'soslider_sliders_active';
	register_setting( 'soslider_base', $o->get_name() );
	$o->name = 'soslider_sliders_mobile_active';
	register_setting( 'soslider_base', $o->get_name() );
	$o->name = 'googleplus_api_key';
	register_setting( 'soslider_base', $o->get_name() );
	$o->name = 'soslider_get_disable_parameter';
	register_setting( 'soslider_base', $o->get_name() );
	$o->name = 'soslider_slider_behaviour';
	register_setting( 'soslider_base', $o->get_name() );
	$o->name = 'soslider_slider_speed';
	register_setting( 'soslider_base', $o->get_name() );
	$o->name = 'soslider_use_visual_designer_positioner';
	register_setting( 'soslider_base', $o->get_name() );
	if ( ! SOOptionBase::is_used_visual_designer() ) {
		add_action( 'soslider_after_save', '__soslider_after_save_item' );
	}
}

function __soslider_fp_init() {
	$active = apply_filters( 'soslider_active_handlers', false );
	if ( $active ) {
		wp_enqueue_script( 'jquery' );
		wp_register_script(
			'jquery-soslider',
			SOSLIDER_PLUGIN_URL . 'js/jquery.soslider.min.js', array( 'jquery' ), null, true
		);
		wp_enqueue_script( 'jquery-soslider' );
		wp_register_style( 'sos-style', SOSLIDER_PLUGIN_URL . 'css/sos_style.min.css' );
		wp_enqueue_style( 'sos-style' );
	}
}

function __soslider_wp_footer() {
	do_action( 'soslider_handlers_output' );
	print '<style>';
	print '@-ms-viewport{ width: auto !important; }';
	print '</style>';
}

function __gxlider_deactivation_hook() {
	do_action( 'soslider_deactivation_hook' );
}

function __gxlider_activation_hook() {
	do_action( 'soslider_activation_hook' );
}

function __soslider_buld_css() {
	do_action( 'soslider_build_css' );
}

__soslider_init();
