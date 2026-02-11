<?php
/**
 * Export_Manager
 *
 * Manages export of charts in various formats
 * Supports PNG, CSV, PDF, JSON, and XML
 */
class Export_Manager {

    /**
     * Export chart
     */
    public function export_chart( $chart_data, $format = 'png', $options = array() ) {
        switch ( strtolower( $format ) ) {
            case 'png':
                return $this->export_to_png( $chart_data, $options );
            case 'csv':
                return $this->export_to_csv( $chart_data, $options );
            case 'pdf':
                return $this->export_to_pdf( $chart_data, $options );
            case 'json':
                return $this->export_to_json( $chart_data, $options );
            case 'xml':
                return $this->export_to_xml( $chart_data, $options );
            default:
                return new WP_Error( 'invalid_format', 'Invalid export format' );
        }
    }

    /**
     * Export to PNG
     */
    private function export_to_png( $chart_data, $options ) {
        // This will be implemented in Phase 2 with Chart.js export
        return new WP_Error( 'not_implemented', 'PNG export will be implemented in Phase 2' );
    }

    /**
     * Export to CSV
     */
    private function export_to_csv( $chart_data, $options ) {
        $default_options = array(
            'delimiter'   => ',',
            'enclosure'   => '"',
            'escape'      => '\\',
            'headers'     => true,
        );

        $options = wp_parse_args( $options, $default_options );

        $output = fopen( 'php://temp', 'w' );

        // Add headers
        if ( $options['headers'] ) {
            fcsv( $output, array( 'Label', 'Value' ), $options['delimiter'], $options['enclosure'], $options['escape'] );
        }

        // Add data
        foreach ( $chart_data['data'] as $index => $data_point ) {
            $label = $chart_data['labels'][$index] ? $chart_data['labels'][$index] : $index;
            $value = $data_point['y'] ? $data_point['y'] : $data_point['value'];
            fcsv( $output, array( $label, $value ), $options['delimiter'], $options['enclosure'], $options['escape'] );
        }

        rewind( $output );
        $csv = stream_get_contents( $output );
        fclose( $output );

        return $csv;
    }

    /**
     * Export to JSON
     */
    private function export_to_json( $chart_data, $options ) {
        $default_options = array(
            'pretty' => true,
        );

        $options = wp_parse_args( $options, $default_options );

        $export_data = array(
            'meta' => array(
                'title'       => $chart_data['meta']['title'] ? $chart_data['meta']['title'] : 'Chart',
                'description' => $chart_data['meta']['description'] ? $chart_data['meta']['description'] : '',
                'created_at' => current_time( 'mysql' ),
            ),
            'chart_type'  => $chart_data['chart_type'],
            'symbol'       => $chart_data['symbol'],
            'data_source'  => $chart_data['data_source'],
            'timeframe'    => $chart_data['timeframe'],
            'data'         => $chart_data['data'],
            'labels'       => $chart_data['labels'],
        );

        if ( $options['pretty'] ) {
            return json_encode( $export_data, JSON_PRETTY_PRINT );
        }

        return json_encode( $export_data );
    }

    /**
     * Export to XML
     */
    private function export_to_xml( $chart_data, $options ) {
        // This will be implemented in Phase 2
        return new WP_Error( 'not_implemented', 'XML export will be implemented in Phase 2' );
    }

    /**
     * Generate export file name
     */
    public function generate_file_name( $chart_data, $format ) {
        $title = $chart_data['meta']['title'] ? $chart_data['meta']['title'] : 'chart';
        $symbol = $chart_data['symbol'] ? $chart_data['symbol'] : 'unknown';
        $date = date( 'Ymd_His', current_time( 'Timestamp' ) );

        $filename = sanitize_title( $title . '_' . $symbol . '_' . $date );
        $filename = strtolower( $filename );
        $filename = preg_replace( '/[^a-z0-9-]+/', '-', $filename );
        $filename = trim( $filename, '-' );

        return $filename . '.' . $format;
    }

    /**
     * Send export as download
     */
    public function send_as_download( $data, $filename, $format ) {
        $content_types = array(
            'csv'  => 'text/csv',
            'json' => 'application/json',
            'png'  => 'image/png',
            'pdf'  => 'application/pdf',
            'xml'  => 'text/xml',
        );

        $content_type = isset( $content_types[$format] ) ? $content_types[$format] : 'application/octet-stream';

        // Send headers
        header( 'Content-Type: ' . $content_type );
        header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
        header( 'Content-Length: ' . strlen( $data ) );
        header( 'Pragma: no-cache' );
        header( 'Expires: 0' );

        // Output data
        echo $data;
        exit;
    }
}
