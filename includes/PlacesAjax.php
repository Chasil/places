<?php
namespace PM;

if ( ! defined( 'ABSPATH' ) ) { exit; }


class PlacesAjax {
    public function __construct() {

        add_action( 'wp_ajax_pm_add_place', [ $this, 'addPlace' ] );
        add_action( 'wp_ajax_nopriv_pm_add_place', [ $this, 'addPlace' ] );

        add_action( 'wp_ajax_pm_get_places', [ $this, 'getPlaces' ] );
        add_action( 'wp_ajax_nopriv_pm_get_places', [ $this, 'getPlaces' ] );

        add_action( 'wp_ajax_pm_update_place', [ $this, 'updatePlace' ] );
    }

    /**
     * @return void
     */
    private function verify_nonce() : void {
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'pm_nonce' ) ) {
            wp_send_json_error( [ 'message' => __( 'Invalid nonce.', 'places-manager' ) ], 403 );
        }
    }

    /**
     * @return void
     */
    public function addPlace() : void {
        $this->verify_nonce();

        $name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
        $address = isset( $_POST['address'] ) ? sanitize_text_field( wp_unslash( $_POST['address'] ) ) : '';
        $nip = isset( $_POST['nip'] ) ? sanitize_text_field( wp_unslash( $_POST['nip'] ) ) : '';
        $regon = isset( $_POST['regon'] ) ? sanitize_text_field( wp_unslash( $_POST['regon'] ) ) : '';

        if ( empty( $name ) ) {
            wp_send_json_error( [ 'message' => __( 'Name is required.', 'places-manager' ) ], 400 );
        }

        $post_id = wp_insert_post( [
            'post_type' => PlacesCPT::POST_TYPE,
            'post_title' => $name,
            'post_status' => 'publish',
        ], true );



        if ( is_wp_error( $post_id ) ) {
            wp_send_json_error( [ 'message' => $post_id->get_error_message() ], 500 );
        }

        update_post_meta( $post_id, 'address', $address );
        update_post_meta( $post_id, 'nip', $nip );
        update_post_meta( $post_id, 'regon', $regon );

        wp_send_json_success( [ 'message' => __( 'Place added.', 'places-manager' ), 'id' => $post_id ] );
    }

    /**
     * @return void
     */
    public function getPlaces() : void {
        $this->verify_nonce();

        $page = isset( $_POST['page'] ) ? max( 1, absint( $_POST['page'] ) ) : 1;
        $ppp = 4;

        $filters = [
            'name' => isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '',
            'address' => isset( $_POST['address'] ) ? sanitize_text_field( wp_unslash( $_POST['address'] ) ) : '',
            'nip' => isset( $_POST['nip'] ) ? sanitize_text_field( wp_unslash( $_POST['nip'] ) ) : '',
            'regon' => isset( $_POST['regon'] ) ? sanitize_text_field( wp_unslash( $_POST['regon'] ) ) : '',
        ];

        $meta_query = [ 'relation' => 'AND' ];
        if ( $filters['address'] !== '' ) {
            $meta_query[] = [
                'key' => 'address',
                'value' => $filters['address'],
                'compare' => 'LIKE',
            ];
        }
        if ( $filters['nip'] !== '' ) {
            $meta_query[] = [
                'key' => 'nip',
                'value' => $filters['nip'],
                'compare' => 'LIKE',
            ];
        }
        if ( $filters['regon'] !== '' ) {
            $meta_query[] = [
                'key' => 'regon',
                'value' => $filters['regon'],
                'compare' => 'LIKE',
            ];
        }

        $args = [
            'post_type' => PlacesCPT::POST_TYPE,
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC',
            'posts_per_page' => $ppp,
            'paged' => $page,
            's' => $filters['name'],
        ];

        if ( count( $meta_query ) > 1 ) {
            $args['meta_query'] = $meta_query;
        }

        $q = new \WP_Query( $args );
        $items = [];
        while ( $q->have_posts() ) {
            $q->the_post();
            $id = get_the_ID();
            $items[] = [
                'id' => $id,
                'name' => get_the_title(),
                'address' => get_post_meta( $id, 'address', true ),
                'nip' => get_post_meta( $id, 'nip', true ),
                'regon' => get_post_meta( $id, 'regon', true ),
            ];
        }
        wp_reset_postdata();

        wp_send_json_success( [
            'items' => $items,
            'found_posts' => (int) $q->found_posts,
            'max_page' => (int) $q->max_num_pages,
            'page' => $page,
        ] );
    }

    /**
     * @return void
     */
    public function updatePlace() : void {
        $this->verify_nonce();

        $id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
        $name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
        $address = isset( $_POST['address'] ) ? sanitize_text_field( wp_unslash( $_POST['address'] ) ) : '';
        $nip = isset( $_POST['nip'] ) ? sanitize_text_field( wp_unslash( $_POST['nip'] ) ) : '';
        $regon = isset( $_POST['regon'] ) ? sanitize_text_field( wp_unslash( $_POST['regon'] ) ) : '';

        if ( ! current_user_can( 'edit_post', $id ) ) {
            wp_send_json_error( [ 'message' => __( 'You cannot edit this item.', 'places-manager' ) ], 403 );
        }
        if ( ! $id ) {
            wp_send_json_error( [ 'message' => __( 'Invalid ID.', 'places-manager' ) ], 400 );
        }

        if ( $name !== '' ) {
            wp_update_post( [ 'ID' => $id, 'post_title' => $name ] );
        }
        update_post_meta( $id, 'address', $address );
        update_post_meta( $id, 'nip', $nip );
        update_post_meta( $id, 'regon', $regon );

        wp_send_json_success( [ 'message' => __( 'Updated.', 'places-manager' ) ] );
    }
}