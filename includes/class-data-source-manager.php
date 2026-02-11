<?php
/**
 * Data_Source_Manager
 *
 * Manages different data sources for financial data
 * Supports Alpha Vantage, Yahoo Finance, and custom sources
 */
class Data_Source_Manager {

    private $api_keys = array();
    private $cache_enabled = true;
    private $cache_duration = 3600; // 1 stunde

    /**
     * Constructor
     */
    public function __construct() {
        $this->load_api_keys();
    }

    /**
     * Load API keys
     */
    private function load_api_keys() {
        $this->api_keys = array(
            'alpha_vantage' => get_option( 'finanz_charts_alpha_vantage_key', '' ),
            'yahoo_finance' => get_option( 'finanz_charts_yahoo_finance_key', '' ),
            'custom'       => get_option( 'finanz_charts_custom_api_key', '' ),
        );
    }

    /**
     * Get data from source
     */
    public function get_data( $symbol, $source = 'alpha_vantage', $timeframe = '1d' ) {
        $cache_key = 'finanz_charts_data_' . strtolower( $source ) . '_' . $symbol . '_' . $timeframe;

        // Check cache
        if ( $this->cache_enabled ) {
            $cached_data = get_transient( $cache_key );
            if ( $cached_data !== false ) {
                return $cached_data;
            }
        }

        // Fetch data from source
        $data = $this->fetch_from_source( $symbol, $source, $timeframe );

        // Cache the data
        if ( $this->cache_enabled && $data ) {
            set_transient( $cache_key, $data, $this->cache_duration );
        }

        return $data;
    }

    /**
     * Fetch data from specific source
     */
    private function fetch_from_source( $symbol, $source, $timeframe ) {
        switch ( $source ) {
            case 'alpha_vantage':
                return $this->fetch_from_alpha_vantage( $symbol, $timeframe );
            case 'yahoo_finance':
                return $this->fetch_from_yahoo_finance( $symbol, $timeframe );
            case 'custom':
                return $this->fetch_from_custom( $symbol, $timeframe );
            default:
                return new WP_Error( 'invalid_source', 'Invalid data source' );
        }
    }

    /**
     * Fetch data from Alpha Vantage
     */
    private function fetch_from_alpha_vantage( $symbol, $timeframe ) {
        $api_key = $this->api_keys['alpha_vantage'];

        if ( empty( $api_key ) ) {
            return new WP_Error( 'missing_api_key', 'Alpha Vantage API key is missing' );
        }

        $api_url = 'https://www.alphavantage.co/query?';
        $params = array(
            'function' => 'TIME_SERIES_DAILY',
            'symbol'   => $symbol,
            'apikey'  => $api_key,
            'datatype' => 'json',
        );

        $url = $api_url . http_build_query( $params );
        $response = wp_remote_get( $url );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $data = json_decode( $response['body'], true );

        return $data;
    }

    /**
     * Fetch data from Yahoo Finance
     */
    private function fetch_from_yahoo_finance( $symbol, $timeframe ) {
        // This will be implemented in Phase 2
        return array();
    }

    /**
     * Fetch data from custom source
     */
    private function fetch_from_custom( $symbol, $timeframe ) {
        // This will be implemented in Phase 2
        return array();
    }

    /**
     * Clear cache
     */
    public function clear_cache( $symbol = null, $source = null, $timeframe = null ) {
        if ( $symbol ) {
            $cache_key = 'finanz_charts_data_' . strtolower( $source ) . '_' . $symbol . '_' . $timeframe;
            delete_transient( $cache_key );
        } else {
            // Clear all caches
            global $wpdb;
            $transients = $wpdb->get_col(
                $sql = "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE '_transient_finanz_charts_%'",
                $args = array()
            );

            foreach ( $transients as $transient ) {
                $transient_name = str_replace( '_transient_', '', $transient );
                delete_transient( $transient_name );
            }
        }

        return true;
    }
}
