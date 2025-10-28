<?php

namespace PM;

if ( ! defined( 'ABSPATH' ) ) { exit; }

class PlacesPlugin {
    private static $instance = null;

    public static function instance() : self {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {

        add_action( 'init', [ PlacesCPT::class, 'register' ] );

        new PlacesAssets();
        new PlacesAjax();
        new PlacesShortcode();
    }
}