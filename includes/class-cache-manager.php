<?php
/**
 * Cache_Manager
 *
 * Manages caching for financial data and charts
 * Supports WordPress transients, object cache, and file cache
 */
class Cache_Manager {

    private $cache_enabled = true;
    private $default_duration = 3600; // 1 stunde

    /**
     * Constructor
     */
    public function __construct() {
        $this->cache_enabled = get_option( 'finanz_charts_cache_enabled', true );
        $this->default_duration = get_option( 'finanz_charts_cache_duration', 3600 );
    }

    /**
     * Get item from cache
     */
    public function get( $key ) {
        if ( !$this->cache_enabled ) {
            return false;
        }

        return get_transient( $this->get_cache_key( $key ) );
    }

    /**
     * Set item in cache
     */
    public function set( $key, $data, $duration = null ) {
        if ( !$this->cache_enabled ) {
            return false;
        }

        if ( $duration === null ) {
            $duration = $this->default_duration;
        }

        return set_transient( $this->get_cache_key( $key ), $data, $duration );
    }

    /**
     * Delete item from cache
     */
    public function delete( $key ) {
        return delete_transient( $this->get_cache_key( $key ) );
    }

    /**
     * Clear all cache
     */
    public function clear_all() {
        global $wpdb;

        $transients = $wpdb->get_col(
            $sql = "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE '_transient_finanz_charts_%'",
            $args = array()
        );

        foreach ( $transients as $transient ) {
            $transient_name = str_replace( '_transient_', '', $transient );
            delete_transient( $transient_name );
        }

        return true;
    }

    /**
     * Get cache key with prefix
     */
    private function get_cache_key( $key ) {
        return 'finanz_charts_' . $key;
    }

    /**
     * Cache chart data
     */
    public function cache_chart_data( $chart_id, $data, $duration = null ) {
        $key = 'chart_data_' . $chart_id;
        return $this->set( $key, $data, $duration );
    }

    /**
     * Get cached chart data
     */
    public function get_cached_chart_data( $chart_id ) {
        $key = 'chart_data_' . $chart_id;
        return $this->get( $key );
    }

    /**
     * Clear chart cache
     */
    public function clear_chart_cache( $chart_id ) {
        $key = 'chart_data_' . $chart_id;
        return $this->delete( $key );
    }

    /**
     * Cache API response
     */
    public function cache_api_response( $api_key, $data, $duration = null ) {
        $key = 'api_' . $api_key;
        return $this->set( $key, $data, $duration );
    }

    /**
     * Get cached API response
     */
    public function get_cached_api_response( $api_key ) {
        $key = 'api_' . $api_key;
        return $this->get( $key );
    }

    /**
     * Clear API cache
     */
    public function clear_api_cache( $api_key ) {
        $key = 'api_' . $api_key;
        return $this->delete( $key );
    }

    /**
     * Get cache stats
     */
    public function get_cache_stats() {
        global $wpdb;

        $transients = $wpdb->get_results(
            $sql = "SELECT COUNT(*) as count FROM {$wpdb->options} WHERE option_name LIKE '_transient_finanz_charts_%'",
            $args = array()
        );

        $count = 0;
        if ( $transients && isset( $transients[0] ) ) {
            $count = $transients[0]->count;
        }

        return array(
            'total_items'     => $count,
            'cache_enabled' => $this->cache_enabled,
            'duration'       => $this->default_duration,
        );
    }
}
