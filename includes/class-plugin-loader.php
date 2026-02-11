<?php
/**
 * Plugin_Loader
 *
 * Loads and initializes all plugin components
 */
class Plugin_Loader {

    private static $_instance;
    public $chart_manager;
    public $data_source_manager;
    public $chart_renderer;
    public $api_integration;
    public $cache_manager;
    public $export_manager;

    private function __construct() {
        $this->load_dependencies();
        $this->initialize_components();
    }

    public static function get_instance() {
        if ( null === self::$_instance ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function load_dependencies() {
        require_once FINANZ_CHARTS_INCLUDES_PATH . 'class-chart-manager.php';
        require_once FINANZ_CHARTS_INCLUDES_PATH . 'class-data-source-manager.php';
        require_once FINANZ_CHARTS_INCLUDES_PATH . 'class-chart-renderer.php';
        require_once FINANZ_CHARTS_INCLUDES_PATH . 'class-api-integration.php';
        require_once FINANZ_CHARTS_INCLUDES_PATH . 'class-cache-manager.php';
        require_once FINANZ_CHARTS_INCLUDES_PATH . 'class-export-manager.php';
    }

    private function initialize_components() {
        $this->chart_manager = new Chart_Manager();
        $this->data_source_manager = new Data_Source_Manager();
        $this->chart_renderer = new Chart_Renderer();
        $this->api_integration = new API_Integration();
        $this->cache_manager = new Cache_Manager();
        $this->export_manager = new Export_Manager();
    }

    public function get_chart_manager() {
        return $this->chart_manager;
    }

    public function get_data_source_manager() {
        return $this->data_source_manager;
    }

    public function get_chart_renderer() {
        return $this->chart_renderer;
    }

    public function get_api_integration() {
        return $this->api_integration;
    }

    public function get_cache_manager() {
        return $this->cache_manager;
    }

    public function get_export_manager() {
        return $this->export_manager;
    }

    public function register_shortcodes() {
        add_shortcode( 'finanz_chart', array( $this, 'render_chart_shortcode' ) );
    }

    public function render_chart_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'id'       => 0,
            'symbol'    => '',
            'type'      => 'line',
            'timeframe' => '1d',
            'width'     => '100%',
            'height'    => '400px',
        ), $atts );

        if ( $atts['id'] ) {
            $chart = $this->chart_manager->get_chart( $atts['id'] );
            if ( !$chart ) {
                return '<p>Chart not found</p>';
            }
            return '<p>Chart: ' . esc_html( $chart->post_title ) . '</p>';
        } else {
            return '<p>Dynamic chart will be implemented in Phase 2</p>';
        }
    }
}
