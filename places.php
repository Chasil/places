<?php
/**
 * Plugin Name: Places Manager
 * Description: CPT "Places" + AJAX add, filters, load more, and editable table.
 * Version: 1.0.0
 * Author: Mateusz Wójcik
 * Text Domain: places-manager
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'PM_PLUGIN_FILE', __FILE__ );
define( 'PM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'PM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

$autoload = __DIR__ . '/vendor/autoload.php';
if ( file_exists( $autoload ) ) {
    require_once $autoload;
} else {
    add_action( 'admin_notices', function() {
        echo '<div class="notice notice-error"><p>' .
            esc_html__( 'Places Manager: missing Composer autoloader. Run `composer install` or include the vendor/ directory in the plugin package.', 'places-manager' ) .
            '</p></div>';
    } );
    return;
}

add_action( 'plugins_loaded', function() {
    load_plugin_textdomain( 'places-manager', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    PM\PlacesPlugin::instance();
} );

register_activation_hook( __FILE__, function() {
    PM\PlacesCPT::register();
    flush_rewrite_rules();
} );

register_deactivation_hook( __FILE__, function() {
    flush_rewrite_rules();
} );
