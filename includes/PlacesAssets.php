<?php
namespace PM;

if ( ! defined( 'ABSPATH' ) ) { exit; }


class PlacesAssets {
    public function __construct() {
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_front' ] );
    }

    /**
     * @return void
     */
    public function enqueue_front() : void {
        wp_enqueue_style( 'places-bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', [], '5.3.3' );
        wp_enqueue_script( 'places-bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', [ 'jquery' ], '5.3.3', true );

        wp_register_script( 'places-js', PM_PLUGIN_URL . 'assets/js/places.js', [ 'jquery' ], '1.0.0', true );
        wp_localize_script( 'places-js', 'PM_DATA', [
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'pm_nonce' ),
            'i18n' => [
                'loadMore' => __( 'Load More', 'places-manager' ),
                'saving' => __( 'Saving…', 'places-manager' ),
                'save' => __( 'Save', 'places-manager' ),
                'edit' => __( 'Edit', 'places-manager' ),
                'cancel' => __( 'Cancel', 'places-manager' ),
                'added' => __( 'Place added successfully.', 'places-manager' ),
                'updated' => __( 'Place updated successfully.', 'places-manager' ),
                'error' => __( 'Something went wrong.', 'places-manager' ),
                'add'      => __( 'Add', 'places-manager' ),
            ],
        ] );
        wp_enqueue_script( 'places-js' );
    }
}