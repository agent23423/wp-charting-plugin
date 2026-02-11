<?php
/**
 * Plugin Name: Finanz Charts WordPress Plugin
 * Plugin URI: https://vyftec.com/finanz-charts-plugin-stock-market-charting-solution/
 * Description: A comprehensive financial charting plugin for WordPress with RSI, MACD, and moving average indicators.
 * Version: 1.0.0
 * Author: Finanz Charts GmbH
 * License: GPL v2 or later
 * Text Domain: finanz-charts
 */

// Prevent direct access
defined('ABSPATH') or die('No direct access allowed');

/**
 * Plugin Class
 */
class FinanzChartsPlugin {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', array($this, 'initialize'));
    }
    
    /**
     * Initialize the plugin
     */
    public function initialize() {
        // Register shortcodes
        add_shortcode('finanz_rsi_chart', array($this, 'rsi_shortcode'));
        
        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_enqueue_styles', array($this, 'enqueue_styles'));
        
        // Register Ajax endpoints
        add_action('wp_ajax', array($this, 'register_ajax_endpoints'));
    }
    
    /**
     * Enqueue scripts
     */
    public function enqueue_scripts() {
        wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/chart.js', array(), '2.9.4', true);
        wp_enqueue_script('finanz-charts-rsi', plugins_url('finanz-ch\ËØ\ÜÙ]ËÚœËÜœÚKšœÉÊK\œ˜^J	ØÚ\ZœÉÊK	ÌKŒŒ	ËYJNÂˆBˆˆÊŠ‚ˆ
ˆ[œ]Y]YHÝ[\Âˆ
‹ÂˆX›XÈ[˜Ý[Ûˆ[œ]Y]YWÜÝ[\Ê
HÂˆÜÙ[œ]Y]YWÜÝ[J	Ùš[˜[ž‹XÚ\Ë\Ý[IËYÚ[œ×Ý\›
	Ùš[˜[ž‹XÚ\ËØ\ÜÙ]ËØÜÜËÜÝ[K˜ÜÜÉÊK\œ˜^J
K	ÌKŒŒ	ÊNÂˆBˆˆÊŠ‚ˆ
ˆ™YÚ\Ý\ˆZ˜^[™Ú[Âˆ
‹ÂˆX›XÈ[˜Ý[Ûˆ™YÚ\Ý\—ØZ˜^Ù[™Ú[Ê
HÂˆYØXÝ[ÛŠ	ÝÜØZ˜^ÜœÚWØØ[Ý[]IË\œ˜^J	\Ë	ØZ˜^ÜœÚWØØ[Ý[]IÊJNÂˆBˆˆÊŠ‚ˆ
ˆZ˜^[™\ˆ›Üˆ”ÒHØ[Ý[][Û‚ˆ
‹ÂˆX›XÈ[˜Ý[ÛˆZ˜^ÜœÚWØØ[Ý[]J
HÂˆËÈ™\šYžH›Û˜ÙBˆÚXÚ×ØZ˜^Ü™Y™\™\Š
NÂˆˆËÈÙ]šXÙH]Hœ›ÛH™\]Y\Ýˆ	šXÙ\ÈHœÛÛ—ÙXÛÙJÝš\Û\Ú\Ê	ÔÔÕÉÜšXÙ\É×JKYJNÂˆ	\š[ÙH[˜[
	ÔÔÕÉÜ\š[Ù	×JNÂˆˆËÈØ[Ý[]H”ÒBˆ	œÚHH	\ËO˜Ø[Ý[]WÜœÚJ	šXÙ\Ë	\š[Ù
NÂˆˆËÈ™]\›ˆ™\Ý[ˆÜÜÙ[™ÚœÛÛŠ	œÚJNÂˆÜÙYJ
NÂˆBˆˆÊŠ‚ˆ
ˆ”ÒHØ[Ý[][Ûˆ[˜Ý[Û‚ˆ
‹Âˆš]˜]H[˜Ý[ÛˆØ[Ý[]WÜœÚJ	šXÙ\Ë	\š[ÙHM
HÂˆYˆ
ÛÝ[
	šXÙ\ÊHH	\š[Ù
HÂˆ™]\›ˆ\œ˜^J	Ù\œ›Ü‰ÈOˆ	Ó›Ý[›ÝYÚ]HÚ[È›Üˆ”ÒHØ[Ý[][Û‰ÊNÂˆBˆˆ	œÚWÝ˜[Y\ÈH\œ˜^J
NÂˆ	ØZ[œÈH\œ˜^J
NÂˆ	ÜÜÙ\ÈH\œ˜^J
NÂˆˆËÈØ[Ý[]HØZ[œÈ[™ÜÜÙ\Âˆ›Üˆ
	HHNÈ	HÛÝ[
	šXÙ\ÊNÈ	JÊÊHÂˆ	Ú[™ÙHH	šXÙ\ÖÉWHH	šXÙ\ÖÉKLWNÂˆYˆ
	Ú[™ÙHH
HÂˆ	ØZ[œÖ×HH	Ú[™ÙNÂˆ	ÜÜÙ\Ö×HHÂˆH[ÙHÂˆ	ØZ[œÖ×HHÂˆ	ÜÜÙ\Ö×HHXœÊ	Ú[™ÙJNÂˆBˆBˆˆËÈØ[Ý[]H]™\˜YÙHØZ[ˆ[™]™\˜YÙHÜÜÂˆ	]™×ÙØZ[ˆH\œ˜^WÜÛXÙJ	ØZ[œË	\š[Ù
NÂˆ	]™×ÛÜÜÈH\œ˜^WÜÛXÙJ	ÜÜÙ\Ë	\š[Ù
NÂˆˆ	]™×ÙØZ[ˆH\œ˜^WÜÝ[J	]™×ÙØZ[ŠHÈ	\š[ÙÂˆ	]™×ÛÜÜÈH\œ˜^WÜÝ[J	]™×ÛÜÜÊHÈ	\š[ÙÂˆˆËÈØ[Ý[]H”ÒH›Üˆš\œÝ\š[ÙˆYˆ
	]™×ÛÜÜÈOH
HÂˆ	œÚWÝ˜[Y\Ö×HHLÂˆH[ÙHÂˆ	œÈH	]™×ÙØZ[ˆÈ	]™×ÛÜÜÎÂˆ	œÚWÝ˜[Y\Ö×HHLH
LÈ
H
È	œÊJNÂˆBˆˆËÈØ[Ý[]H”ÒH›Üˆ™[XZ[š[™ÈÚ[Âˆ›Üˆ
	HH	\š[ÙÈ	HÛÝ[
	šXÙ\ÊNÈ	JÊÊHÂˆ	Ý\œ™[ÙØZ[ˆH	ØZ[œÖÉKLWNÂˆ	Ý\œ™[ÛÜÜÈH	ÜÜÙ\ÖÉKLWNÂˆˆ	]™×ÙØZ[ˆH

]™×ÙØZ[ˆ
ˆ
	\š[ÙHJJH
È	Ý\œ™[ÙØZ[ŠHÈ	\š[ÙÂˆ	]™×ÛÜÜÈH

	]™×ÛÜÜÈ
ˆ
	\š[ÙHJJH
È	Ý\œ™[ÛÜÜÊHÈ	\š[ÙÂˆˆYˆ
	]™×ÛÜÜÈOH
HÂˆ	œÚWÝ˜[Y\Ö×HHLÂˆH[ÙHÂˆ	œÈH	]™×ÙØZ[ˆÈ	]™×ÛÜÜÎÂˆ	œÚWÝ˜[Y\Ö×HHLH
LÈ
H
È	œÚJJNÂˆBˆBˆˆ™]\›ˆ\œ˜^Jˆ	ÜÝXØÙ\ÜÉÈOˆYKˆ	ÜœÚWÝ˜[Y\ÉÈOˆ	œÚWÝ˜[Y\Ëˆ	ÛÝ™\˜›ÝYÚ	ÈOˆÌˆ	ÛÝ™\œÛÛ	ÈOˆÌˆ	Û\ÝÜšXÙIÈOˆ[™
	šXÙ\ÊKˆ	ØÝ\œ™[ÜœÚIÈOˆ[™
	œÚWÝ˜[Y\ÊBˆ
NÂˆBˆˆÊŠ‚ˆ
ˆ”ÒHÚÜÛÙBˆ
‹ÂˆX›XÈ[˜Ý[ÛˆœÚWÜÚÜÛÙJ	]ÊHÂˆ	]ÈHÚÜÛÙWØ]Ê\œ˜^Jˆ	ÜÞ[X›Û	ÈOˆ	ÐT	Ëˆ	Ü\š[Ù	ÈOˆMˆ	ÝÚY	ÈOˆ	ÌL	IËˆ	ÚZYÚ	ÈOˆ	Í	Ëˆ	ÜÚÝ×Û]™[ÉÈOˆ	ÝYIÂˆ
K	]ÊNÂˆˆËÈÙ[™\˜]H[š\]YHQ›ÜˆHÚ\ˆ	Ú\ÚYH	ÜœÚKXÚ\IÈˆ[š\ZY

NÂˆˆËÈÜ™X]HHÚ\ÛÛZ[™\‚ˆ	Ý]]H	Ï]ˆÛ\ÜÏH™š[˜[ž‹\œÚKXÚ\‰ÎÂˆ	Ý]]H	Ï]ˆYH‰Èˆ	Ú\ÚYˆ	ÈˆÝ[OHÚYˆ	Èˆ\Ø×Ø]Š	]ÖÉÝÚY	×JHˆ	ÎÈZYÚˆ	Èˆ\Ø×Ø]Š	]ÖÉÚZYÚ	×JHˆ	ÎÈÙ]‰ÎÂˆ	Ý]]H	ÏÙ]‰ÎÂˆˆËÈY˜]˜TØÜš\È[š]X[^™H