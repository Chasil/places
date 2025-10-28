<?php

namespace PM;

if ( ! defined( 'ABSPATH' ) ) { exit; }


class PlacesCPT {
    const POST_TYPE = 'place';

    /**
     * @return void
     */
    public static function register() : void {
        $labels = [
            'name' => _x( 'Places', 'Post Type General Name', 'places-manager' ),
            'singular_name' => _x( 'Place', 'Post Type Singular Name', 'places-manager' ),
            'menu_name' => __( 'Places', 'places-manager' ),
            'name_admin_bar' => __( 'Place', 'places-manager' ),
            'add_new' => __( 'Add New', 'places-manager' ),
            'add_new_item' => __( 'Add New Place', 'places-manager' ),
            'new_item' => __( 'New Place', 'places-manager' ),
            'edit_item' => __( 'Edit Place', 'places-manager' ),
            'view_item' => __( 'View Place', 'places-manager' ),
            'all_items' => __( 'All Places', 'places-manager' ),
            'search_items' => __( 'Search Places', 'places-manager' ),
            'not_found' => __( 'No places found.', 'places-manager' ),
            'not_found_in_trash' => __( 'No places found in Trash.', 'places-manager' ),
        ];

        $args = [
            'labels' => $labels,
            'public' => true,
            'show_in_rest' => true,
            'has_archive' => true,
            'rewrite' => [ 'slug' => 'places' ],
            'menu_icon' => 'dashicons-location',
            'supports' => [ 'title' ],
        ];

        register_post_type( self::POST_TYPE, $args );
        self::register_meta();
    }

    /**
     * @return void
     */
    public static function register_meta() : void {
        $meta_fields = [
            'address' => [ 'type' => 'string' ],
            'nip' => [ 'type' => 'string' ],
            'regon' => [ 'type' => 'string' ],
        ];

        foreach ( $meta_fields as $key => $schema ) {
            register_post_meta( self::POST_TYPE, $key, [
                'type' => $schema['type'],
                'single' => true,
                'show_in_rest' => true,
                'sanitize_callback' => function( $value ) {
                    return sanitize_text_field( $value );
                },
                'auth_callback' => function() { return current_user_can( 'edit_posts' ); },
            ] );
        }
    }
}