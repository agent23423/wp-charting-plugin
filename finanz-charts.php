<?php
/**
 * Plugin Name: Finanz Charts
 * Plugin URI: https://vyftec.com/finanz-charts
 * Description: A professional financial charting plugin for WordPress with real-time stock market data, technical indicators, and advanced charting features.
 * Version: 1.0.0
 * Author: Finanz Charts GmbH
 * License: GPL v2 or later
 * Text Domain: finanz-charts
 * Domain Path: /languages/
 */

// Prevent direct access
defined( 'ABSPATH' ) || die( 'No direct access allowed!' );

/**
 * Current plugin version
 */
define( 'FINANZ_CHARTS_VERSION', '1.0.0' );

/**
 * Plugin base path
 */
define( FINANZ_CHARTS_PATH, plugin_dirpath( __FILE__ ) );

/**
 * Plugin base URL
 */
define( FINANZ_CHARTS_URL, plugin_dir_url( __FILE__ ) );

/**
 * Plugin includes path
 */
define( FINANZ_CHARTS_INCLUDES_PATH, FINANZ_CHARTS_PATH . 'includes/' );

/**
 * Custom post type for charts
 */
define( 'FINANZ_CHARTS_POST_TYPE', 'finanz_chart' );

/**
 * Main plugin initialization
 */
function finanz_charts_init() {
    require_once FINANZ_CHARTS_INCLUDES_PATH . 'class-plugin-loader.php';
    
    // Initialize the plugin loader
    $loader = Plugin_Loader::get_instance();
    
    // Register shortcodes
    add_action( 'init', array( $loader, 'register_shortcodes' ) );
}

// Initialize the plugin
add_action( 'init', 'finanz_charts_init' );

/**
 * Activation hook
 */
function finanz_charts_activate() {
    // Create default options
    add_option( 'finanz_charts_alpha_vantage_key', '' );
    add_option( 'finanz_charts_cache_enabled', true );
    add_option( 'finanz_charts_cache_duration', 3600 );
}

/**
 * Deactivation hook
 */
function finanz_charts_deactivate() {
    // Clear cache on deactivation
    require_once FINANZ_CHARTS_INCLUDES_PATH . 'class-cache-manager.php';
    $cache_manager = new Cache_Manager();
    $cache_manager->clear_all();
}

// Register activation and deactivation hooks
register_activation_hook( __FILE__, 'finanz_charts_activate' );
register_deactivation_hook( __FILE__, 'finanz_charts_deactivate' );

/**
 * Shortcode: [inanz_chart]
 */
function finanz_chart_shortcode( $atts ) {
    require_once FINANZ_CHARTS_INCLUDES_PATH . 'class-plugin-loader.php';
    $loader = Plugin_Loader::get_instance();
    return $loader->render_chart_shortcode( $atts );
}
add_shortcode( 'finanz_chart', 'finanz_chart_shortcode' );

/**
 * Admin menu
 */
function finanz_charts_add_admin_menu() {
    add_menu_page(
        __( 'Finanz Charts', 'finanz-charts' ),
        __( 'Finanz Charts', 'finanz-charts' ),
        'manage_options',
        'finanz_charts_settings',
        'dashicons-chart-area',
        30
    );
}
add_action( 'admin_menu', 'finanz_charts_add_admin_menu' );

/**
 * Settings page
 */
function finanz_charts_settings() {
    ?>
    <div class="wrap">
        <h1><?php __( 'Finanz Charts Settings', 'finanz-charts' ); ?></h1>
        <form method="post" action="options.php">
            <table class="form-table">
                <tr>
                    <th scope="row"><?php __( 'Alpha Vantage API Key', 'finanz_charts' ); ?></th>
                    <td><input type="text" name="finanz_charts_alpha_vantage_key" value="<?php echo get_option( 'finanz_charts_alpha_vantage_key', '' ); ?>" size="50" /></td>
                </tr>
                <tr>
                    <th scope="row"><?php __( 'Cache Duration (seconds)', 'finanz-charts' ); ?></th>
                    <td><input type="number" name="finanz_charts_cache_duration" value="<?php echo get_option( 'finanz_charts_cache_duration', 3600 ); ?>" /></td>
                </tr>
                <tr>
                    <th scope="row"></th>
                    <td><?php submit_button( __( 'Save Settings', 'finanz_charts' ) ); ?></td>
                </tr>
            </table>
            <?php wp_nonce_field( 'finanz_charts_settings' ); ?>
        </form>
    </div>
    <?php
}

/**
 * Save settings
 */
function finanz_charts_save_settings() {
    if ( !isset( $_POST['finanz_charts_settings_nonce'] ) || !wp_verify_nonce( $_POST['finanz_charts_settings_nonce'], 'finanz_charts_settings' ) ) {
        return;
    }
    
    if ( isset( $_POST['finanz_charts_alpha_vantage_key'] ) ) {
        update_option( 'finanz_charts_alpha_vantage_key', sanitize_text_field( $_POST['finanz_charts_alpha_vantage_key'] ) );
    }
    
    if ( isset( $_POST['finanz_charts_cache_duration'] ) ) {
        update_option( 'finanz_charts_cache_duration', intval( $_POST['finanz_charts_cache_duration'] ) );
    }
    
    wp_redirect( add_query_arg( array( 'page' => 'finanz_charts_settings', 'message' => 'saved' ), 'admin.php' ) );
}
add_action( 'admin_post_finanz_charts_settings', 'finanz_charts_save_settings' );
