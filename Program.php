<?php
/*
Plugin Name: Ipswich JAFFA RC Event Results
Plugin URI:
Description: Display results from blobs (files) held in a database.
Version: 0.1.0
Author: Gavin Davies
Author URI: https://github.com/gavinrunsdavies/
*/

namespace IpswichEventResultsAPI;

$go = new Program();

class Program
{
	function __construct()
	{
		add_action('init', array($this, 'registerShortCodes'));

		require_once "api/plugin.php";
	}

	public function registerShortCodes()
	{
		add_shortcode('ipswich-event-results', array($this, 'processShortCode'));

		add_action('wp_print_scripts', array($this, 'scripts'));
	}

	public function processShortCode($attr, $content = "")
	{
		$atts = shortcode_atts(
			array(
				'eventraceresultspageid' => 0,
				'feature' => ''
			),
			$attr
		);

		$feature = $atts['feature'];

		if ($feature != '') {

			ob_start();
			require_once "html/$feature.php";
			$content = ob_get_clean();
		}

		return $content;
	}

	public function scripts()
	{
	}
}
