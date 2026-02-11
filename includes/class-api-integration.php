<?php
/**
 * API_Integration

 * Handles API integration for financial data sources
 * Supports Alpha Vantage, Yahoo Finance, and custom APIs
 */
class API_Integration {

    private $api_keys = array();
    private $api_endpoints = array();

    /**
     * Constructor
     */
    public function __construct() {
        $this->load_api_keys();
        $this->setup_api_endpoints();
    }

    /**
     * Load API keys
     */
    private function load_api_keys() {
        $this->api_keys = array(
            'alpha_vantage' => get_option( 'finanz_charts_alpha_vantage_key', '' ),
            'custom'       => get_option( 'finanz_charts_custom_api_key', '' ),
        );
    }

    /**
     * Setup API endpoints
     */
    private function setup_api_endpoints() {
        $this->api_endpoints = array(
            'alpha_vantage' => array(
                'base_url' => 'https://www.alphavantage.co/query?',
                'functions' => array(
                    'TIME_SERIES_DAILY'     => 'Time Series Daily',
                    'TIME_SERIES_WEEKLY'   => 'Time Series Weekly',
                    'TIME_SERIES_MONTHLY' => 'Time Series Monthly',
                    'FOREX_DAILY'         => 'Forex Daily',
                    'Crypto_DAYLY'       => 'Crypto Daily',
                ),
            ),
        );
    }

    /**
     * Get data from API source
     */
    public function get_data( $source, $function, $params = array() ) {
        if ( !isset( $this->api_endpoints[$source] ) ) {
            return new WP_Error( 'invalid_source', 'Invalid API source' );
        }

        $api_key = $this->api_keys[$source];

        if ( empty( $api_key ) ) {
            return new WP_Error( 'missing_api_key', 'API key is missing' );
        }

        $params['apikey'] = $api_key;
        $params['function'] = $function;
        $params['datatype'] = 'json';

        $url = $this->api_endpoints[$source]['base_url'] . http_build_query( $params );

        $response = wp_remote_get( $url );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $data = json_decode( $response['body'], true );

        return $data;
    }

    /**
     * Get stock data
     */
    public function get_stock_data( $symbol, $source = 'alpha_vantage', $timeframe = '1d' ) {
        switch ( $timeframe ) {
            case '1d':
                $function = 'TIME_SERIES_DAILY';
                break;
            case '7d':
                $function = 'TIME_SERIES_WEEKLY';
                break;
            case '30d':
                $function = 'TIME_SERIES_MONTHLY';
                break;
            default:
                $function = 'TIME_SERIES_DAILY';
        }

        $params = array(
            'symbol' => $symbol,
        );

        return $this->get_data( $source, $function, $params );
    }

    /**
     * Get forex data
     */
    public function get_forex_data( $from, $to, 'source = 'alpha_vantage' ) {
        $params = array(
            'from_currency' => $from,
            'to_currency'   => $to,
        );

        return $this->get_data( $source, 'FOREX_DAYLY', $params );
    }

    /**
     * Get crypto data
     */
    public function get_crypto_data( $symbol, 'source = 'alpha_vantage' ) {
        $params = array(
            'symbol' => $symbol,
        );

        return $this->get_data( $source, 'Crypto_DAILY', $params );
    }

    /**
     * Test API connection
     */
    public function test_api_connection( $source ) {
        if ( !isset( $this->api_keys[$source] ) ) {
            return new WP_Error( 'missing_api_key', 'API key not configured' );
        }

        if ( empty( $this->api_keys[$source] ) ) {
            return new WP_Error( 'empty_api_key', 'API key is empty' );
        }

        // Test with a simple request
        $test_data = $this->get_stock_data( 'MS', 'alpha_vantage', '1d' );

        if ( is_wp_error( $test_data ) ) {
            return $test_data;
        }

        return true;
    }
}
