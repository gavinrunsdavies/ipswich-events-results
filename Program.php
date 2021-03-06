<?php
/*
Plugin Name: Ipswich Event Results
Plugin URI:
Description: The new Ipswich Event Results plugin. Requires bootstrap to be part of the theme.
Version: 0.0.1
Author: Gavin Davies
Author URI: https://github.com/gavinrunsdavies/
*/
namespace IpswichEventResultsAPI;
		
$go = new Program();

class Program 
{	
	function __construct() {		
		add_action('init', array($this, 'registerShortCodes'));
    
    require_once "api/plugin.php";
	}
		
	public function registerShortCodes()	{				

		add_shortcode('ipswich-event-results', array( $this, 'processShortCode' ));
		
		add_action('wp_print_scripts', array($this, 'scripts'));
	}	
		
	public function processShortCode($attr, $content = "") {
		$atts = shortcode_atts(
			array(
				  'eventraceresultspageid' => 0,
				  'feature' => ''), 
			$attr);
				
		$eventRaceResultsPageUrl = get_permalink($atts['eventraceresultspageid']);
		
		$feature = $atts['feature'];		
		
		if ($feature != '') {			
			
			ob_start();
			require_once "html/$feature.php";
			$content = ob_get_clean();
		}
		
		return $content;
	}
		
	public function scripts() {		
		wp_enqueue_script(
			'datatables.min.js',
			 plugins_url('/lib/datatables.min.js', __FILE__ )
    );
    wp_enqueue_script(
			'datatables.rowGroup.min.js',
			'https://cdn.datatables.net/rowgroup/1.0.4/js/dataTables.rowGroup.min.js'
    );
	}
}
?>