<?php
/**
 * Chart_Renderer
 *
 * Renders charts in various formats (HTML, Canvas, SVG)
 * Supports Chart.js, Highcharts, and custom renderers
 */
class Chart_Renderer {

    private $chart_library = 'chartjs';

    /**
     * Constructor
     */
    public function __construct( $library = 'chartjs' ) {
        $this->chart_library = $library;
        $this->init_hooks();
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
    }

    /**
     * Enqueue scripts
     */
    public function enqueue_scripts() {
        switch ( $this->chart_library ) {
            case 'chartjs':
                wp_enqueue_script(
                    'chart-js',
                    'https://cdn.jsdelivr.net/npm/chart.js',
                    array(),
                    '3.9.1',
                    true
                );
                break;
            case 'highcharts':
                wp_enqueue_script(
                    'highcharts',
                    'https://code.highcharts.com/highcharts.js',
                    array(),
                    '10.3.3',
                    true
                );
                break;
        }
    }

    /**
     * Render chart
     */
    public function render_chart( $chart_data, $options = array() ) {
        $default_options = array(
            'width'       => '100%',
            'height'      => '400px',
            'theme'       => 'light',
            'responsive'   => true,
            'interactive' => true,
            'show_legend'  => true,
            'show_tooltip' => true,
            'animation'    => true,
        );

        $options = wp_parse_args( $options, $default_options );

        switch ( $this->chart_library ) {
            case 'chartjs':
                return $this->render_chartjs( $chart_data, $options );
            case 'highcharts':
                return $this->render_highcharts( $chart_data, $options );
            default:
                return $this->render_html( $chart_data, $options );
        }
    }

    /**
     * Render Chart.js chart
     */
    private function render_chartjs( $chart_data, $options ) {
        $chart_id = 'chart-' . uniqid();

        ob_start();
        ?>
        <div class="finanz-chart-container" style="width: <?php echo esc_attr( $options['width'] ); ?>; height: <?php echo esc_attr( $options['height'] ); ?>;">
            <canvas id="<?php echo $chart_id; ?>"></canvas>
        </div>

        <script>
        jQuery(document).ready(function($) {
            var ctx = document.getElementById('<?php echo $chart_id; ?>').getContext('2d');
            new Chart(ctx, {
                type: '<?php echo $chart_data['chart_type']; ?>',
                data: {
                    labels: <?php echo json_encode( $chart_data['labels'] ); ?>,
                    datasets: [
                        {
                            label: '<?php echo esc_js_attr( $chart_data['symbol'] ); ?>',
                            data: <?php echo json_encode( array_column( $chart_data['data'], 'y' ) ); ?>,
                            borderColor: '#0073aa',
                            backgroundColor: 'rgba(0, 115, 170, 0.1)'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        });
        </script>
        <?php
        return ob_get_clean();
    }

    /**
     * Render HTML fallback
     */
    private function render_html( $chart_data, $options ) {
        ob_start();
        ?>
        <div class="finanz-chart-html" style="width: <?php echo esc_attr( $options['width'] ); ?>; height: <?php echo esc_attr( $options['height'] ); ?>;">
            <table class="chart-data-table">
                <thead>
                    <tr>
                        <th>Label</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $chart_data['data'] as $index => $data_point ): ?>
                    <tr>
                        <td><?php echo esc_html( $chart_data['labels'][$index] ? $chart_data['labels'][$index] : $index ); ?></td>
                        <td><?php echo esc_html( $data_point['y'] ? $data_point['y'] : $N/A ); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
        return ob_get_clean();
    }
}
