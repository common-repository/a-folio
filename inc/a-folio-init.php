<?php
// Initial settings for the a-folio plugin



// The minimum required Themple Framework version to use with this plugin. It's needed to ensure the plugin is compatible with the theme
define( 'A_FOLIO_REQ_TPL_VERSION', '1.3' );



// Themple Lite Purgatory
global $tpl_load_version;
$a_folio_tpl_version = array(
	"name"		=> 'a-folio',
	"version"	=> '1.3',
);
if ( !is_array( $tpl_load_version ) ) {
	$tpl_load_version = $a_folio_tpl_version;
}
else {
	if ( version_compare( $tpl_load_version["version"], $a_folio_tpl_version["version"] ) < 0 ) {
		$tpl_load_version = $a_folio_tpl_version;
	}
}



// The initializer function
function a_folio_init() {

	global $tpl_load_version, $a_folio_tpl_version;

	// If previously the load_version was detected as this plugins TPL Framework instance then load it!
	if ( $tpl_load_version["name"] == $a_folio_tpl_version["name"] ) {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . "framework/tpl-fw.php";

		// Load the framework's l10n files in this case
		$mo_filename = plugin_dir_path( dirname( __FILE__ ) ) . 'framework/languages/' . get_locale() . '.mo';
		if ( is_admin() && file_exists( $mo_filename ) ) {
			load_textdomain( 'tpl', $mo_filename );
		}

	}

}
add_action( 'after_setup_theme', 'a_folio_init' );




// This function is needed for interpreting the Settings page settings.
function a_folio_settings () {

	tpl_settings_page( 'a_folio_settings');

}



/* SCRIPT HANDLING */

// Font Awesome CSS loader function. Loads the FA CSS file if it's not yet available in the front end
function a_folio_fa_css() {

	wp_enqueue_style( 'font-awesome', plugins_url( 'assets/font-awesome.min.css', dirname( __FILE__ ) ) );

}



// Adding some extra settings just to make sure JS works fine
add_filter( 'tpl_admin_js_strings', 'a_folio_admin_js_values', 10, 1 );

function a_folio_admin_js_values( $values ) {

	$values["remover_confirm"] = 'yes';
	$values["pb_fewer_confirm"] = 'yes';
	$values["pb_fewer_instances"] = '';

	return $values;
}



// Load the plugin's front end CSS if it's enabled in admin
add_action( 'wp_enqueue_scripts', function() {

	if ( tpl_get_option( 'a_folio_load_css' ) == 'yes' ) {

		wp_register_style( 'a-folio-style', plugins_url( 'assets/a-folio.min.css', dirname( __FILE__ ) ), array(), A_FOLIO_VERSION );
		wp_register_script( 'a-folio-script', plugins_url( 'assets/a-folio.min.js', dirname( __FILE__ ) ), array( 'jquery' ), A_FOLIO_VERSION );

		// Add some responsive code if it was enabled in plugin settings
		if ( tpl_get_option( 'a_folio_responsive' ) == 'yes' ) {

			$custom_css = '@media (max-width: ' . tpl_get_value( 'a_folio_responsive_breakpoints/0/breakpoint_1' ) . ') {
				.a-folio-tile-size-1_2, .a-folio-tiled-container { width: 100%; }
				.a-folio-tile-size-1_4 { width: 50%; }
				.a-folio-tile-size-1_3 { width: 100%; }
			}
			@media (max-width: ' . tpl_get_value( 'a_folio_responsive_breakpoints/0/breakpoint_2' ) . ') {
				.a-folio-tile-size-1_4 { width: 100%; }
				.a-folio-tiled-container { height: auto; padding-bottom: 0; }
				.a-folio-tiled-container .a-folio-tile-size-1_4 { padding-left: 0; padding-right: 0; width: 100%; }
			}';
			wp_add_inline_style( 'a-folio-style', esc_html( $custom_css ) );

		}

	}

} );



// JS functions for admin panel
add_action( 'admin_enqueue_scripts', function() {

	wp_enqueue_script( 'a-folio-admin-script', plugins_url( '', dirname( __FILE__ ) ) . '/assets/a-folio-admin.min.js', array( 'jquery', 'tpl-admin-scripts' ) );
	wp_enqueue_style( 'a-folio-admin-style', plugins_url( '', dirname( __FILE__ ) ) . '/assets/a-folio-admin.min.css' );

});

/* END OF SCRIPT HANDLING */



// Add the default image size for the plugin
add_filter( 'tpl_image_sizes', 'a_folio_image_sizes', 10, 1 );

function a_folio_image_sizes( $image_sizes = array() ) {

	// The large tile image size
	$image_sizes["a-folio-tile"] = array(
		'title'		=> __( 'a-folio tile', 'a-folio' ),
		'width'		=> 580,
		'height'	=> 440,
		'crop'		=> array( 'center', 'center' ),
		'select'	=> true,
	);

	return $image_sizes;

}



// Rewrite rules update to avoid 404 errors
function a_folio_flush_rewrites() {

	a_folio_cpt();
	flush_rewrite_rules();

}
