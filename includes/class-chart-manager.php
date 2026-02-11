<?php
/**
 * Chart_Manager
 *
 * Manages financial charts, including creation, editing, deletion, and retrieval.
 * Supports multiple chart types (line, bar, area, candlestick, pie, donut)
 * Handles data sources (Alpha Vantage, Yahoo Finance, manual)
 */
class Chart_Manager {

    private $post_type = 'finanz_chart';

    /**
     * Constructor
     */
    public function __construct() {
        $this->init_hooks();
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action( 'init', array( $this, 'register_post_type' ) );
    }

    /**
     * Register custom post type
     */
    public function register_post_type() {
        $labels = array(
            'name'               => __( 'Charts', 'finanz-charts' ),
            'singular_name'      => __( 'Chart', 'finanz-charts' ),
            'add_new'             => __( 'Add New Chart', 'finanz_charts' ),
        );

        $args = array(
            'labels'            => $labels,
            'description'       => __( 'Manage financial charts', 'finanz-charts' ),
            'public'             => true,
            'publicly_querable'   => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'menu_position'       => 20,
            'menu_icon'           => 'dashicons-chart-area',
            'supports'            => array( 'title', 'editor', 'author', 'thumbnail' ),
            'has_archive'        => true,
        );

        register_post_type( $this->post_type, $args );
    }

    /**
     * Get chart by ID
     */
    public function get_chart( $chart_id ) {
        return get_post( $chart_id );
    }

    /**
     * Get all charts
     */
    public function get_all_charts( $args = array() ) {
        $defaults = array(
            'post_type'       => $this->post_type,
            'post_status'    => 'publish',
            'number_posts' => -1,
        );

        $args = wp_parse_args( $args, $defaults );

        return get_posts( $args );
    }

    /**
     * Create new chart
     */
    public function create_chart( $title, $data = array() ) {
        $post_data = array(
            'post_type'    => $this->post_type,
            'post_title'   => $title,
            'post_status' => 'draft',
        );

        return wp_insert_post( $post_data );
    }

    /**
     * Delete chart
     */
    public function delete_chart( $chart_id ) {
        return wp_destroy_post( $chart_id, true );
    }
}
