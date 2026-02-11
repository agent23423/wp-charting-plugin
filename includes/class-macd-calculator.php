<?php
/**
 * MACD Calculator for Finanz Charts Plugin
 *
 * This class handles the calculation of Moving Average Convergence Divergence (MACD) indicator.
 *
 * @MACD components:
 * 1. MACD line: 12-day EMA - 26-day EMA
 * 2. Signal line: 9-day EMA of MACD line
 * 3. MACD histogram: MACD line - Signal line
 *
 * @package    Finanz-Charts
 * @author      Finanz Charts GmbH
 * @copyright   2024 Finanz Charts GmbH - All rights reserved
 * @license     GPL v2 or later
 */

class MACD_Calculator {
    
    /**
     * Calculate Exponential Moving Average (EMA)
     *
     * @param array $prices Array of price values
     * @param int  $period   EMA period (default: 12)
     * @return array Array of EUA values
     */
    public static function calculate_ema($prices, $period = 12) {
        if (count($prices) < $period) {
            return array();
        }
        
        $ema_values = array();
        $smoothing_factor = 2 / ($period + 1);
        
        // Calculate SMA for first value
        $sma = array_sum(array_slice($prices, 0, $period)) / $period;
        $ema_values[] = $sma;
        
        // Calculate EMA values
        for ($i = $period; $i < count($prices); $i++) {
            $current_price = $prices[$i];
            $previous_ema = $ema_values[count($ema_values) - 1];
            
            $ema = ($current_price * $smoothing_factor) + ($previous_ema * (1 - $smoothing_factor));
            $ema_values[] = $ema;
        }
        
        return $ema_values;
    }
    
    /**
     * Calculate MACD indicator
     *
     * @param array $prices     Array of price values
     * @param int  $ema_short  12-day EMA period (default: 12)
     * @param int  $ema_long   26-day EMA period (default: 26)
     * @param int  $signal    9-day EMA period (default: 9)
     * @return array Array with MACD data
     */
    public static function calculate_macd($prices, $ema_short = 12, $ema_long = 26, $signal = 9) {
        if (count($prices) < $ema_long) {
            return array(
                'error' => 'Not enough data to calculate MACD (minimum: ' . $ema_long . ' data points)',
                'macd_line' => array(),
                'signal_line' => array(),
                'histogram' => array()
            );
        }
        
        // Calculate EMAs
        $ema_short_values = self::calculate_ema($prices, $ema_short);
        $ema_long_values = self::calculate_ema($prices, $ema_long);
        
        // Calculate MACD line (12-day EMA - 26-day EMA)
        $macd_line = array();
        
        // Align arrays (Short EMA is longer due to shorter period)
        $start_index = count($prices) - count($ema_short_values);
        $ema_short_offset = array_slice($ema_short_values, $start_index);
        $ema_long_offset = array_slice($ema_long_values, $start_index);
        
        for ($i = 0; $i < count($ema_short_offset); $i++) {
            $macd_line[] = $ema_short_offset[$i] - $ema_long_offset[$i];
        }
        
        // Calculate Signal line (9-day EMA of MACD line)
        $signal_line = self::calculate_ema($macd_line, $signal);
        
        // Calculate Histogram (MACD line - Signal line)
        $histogram = array();
        
        $count = count($macd_line);
        $signal_count = count($signal_line);
        $offset = $count - $signal_count;
        
        for ($i = 0; $i < $signal_count; $i++) {
            $macd_index = $i + $offset;
            if (isset($macd_line[$macd_index]) && isset($signal_line[$i])) {
                $histogram[] = $macd_line[$macd_index] - $signal_line[$i];
            } else {
                $histogram[] = 0;
            }
        }
        
        // Return complete MACD data
        return array(
            'macd_line' => $macd_line,
            'signal_line' => $signal_line,
            'histogram' => $histogram,
            'parameters' => array(
                'ema_short' => $ema_short,
                'ema_long' => $ema_long,
                'signal' => $signal
            ),
            'signals' => self::analyze_macd_signals($macd_line, $signal_line, $histogram)
        );
    }
    
    /**
     * Analyze MACD signals (buy/sell signals)
     *
     * @param array $macd_line   MACD line values
     * @param array $signal_line Signal line values
     * @param array $histogram   Histogram values
     * @return array Array of signal data
     */
    public static function analyze_macd_signals($macd_line, $signal_line, $histogram) {
        $signals = array();
        $count = count($macd_line);
        
        for ($i = 1; $i < $count; $i++) {
            $current_macd = $macd_line[$i];
            $prev_macd = $macd_line[$i-1];
            $current_signal = $signal_line[$i];
            $prev_signal = $signal_line[$i-1];
            $current_hist = $histogram[$i];
            $prev_hist = $histogram[$i-1];
            
            // Buy signal: MACD crosses above signal line
            if ($prev_macd <= $prev_signal && $current_macd > $current_signal) {
                $signals[] = array(
                    'type' => 'buy',
                    'index' => $i,
                    'macd' => $current_macd,
                    'signal' => $current_signal,
                    'histogram' => $current_hist,
                    'timestamp' => time()
                );
            }
            
            // 