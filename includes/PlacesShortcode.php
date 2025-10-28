<?php
namespace PM;


if ( ! defined( 'ABSPATH' ) ) { exit; }


class PlacesShortcode {
    public function __construct() {
        add_shortcode( 'places_manager', [ $this, 'render' ] );
    }

    /**
     * @param array $atts
     * @param $content
     * @return string
     */
    public function render(array $atts = [], $content = '' ) : string {
        ob_start();
        $template = PM_PLUGIN_DIR . 'templates/places.php';
        if ( file_exists( $template ) ) {
            include $template;
        } else {
            echo '<div class="alert alert-danger">' . esc_html__( 'Template not found.', 'places-manager' ) . '</div>';
        }
        return (string) ob_get_clean();
    }
}