<?php
/*
Plugin Name: Ipswich Ekiden Results
Plugin URI:
Description: The new Ipswich Ekiden Results plugin. Requires bootstrap to be part of the theme.
Version: 2.0
Author: Gavin Davies
Author URI: https://github.com/gavinrunsdavies/
*/
namespace IpswichEkidenResults;
		
$go = new Program();

class Program 
{	
	function __construct() {		
		add_action('init', array($this, 'registerShortCodes'));
    
    require_once "api/plugin.php";
	}
		
	public function registerShortCodes()	{				

		add_shortcode('ipswich-event-results', array( $this, 'processShortCode' ));
		
		add_action('wp_print_styles', array($this, 'styles'));
		add_action('wp_print_scripts', array($this, 'scripts'));
	}	
		
	public function processShortCode($attr, $content = "") {

		require_once "html/RaceResults.php";
		$content = ob_get_clean();
		
		return $content;
	}
		
	public function scripts() {		
		wp_enqueue_script(
			'datatables.min.js',
			 plugins_url('/lib/datatables.min.js', __FILE__ )
    );
	}
	
	public function styles()
	{		
		wp_enqueue_style(
			'dataTables.min.css',
			plugins_url('/lib/datatables.min.css', __FILE__ )
		);		
	}
}
?>