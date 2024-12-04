<?php

namespace IpswichEventResultsAPI\V1;

require_once plugin_dir_path(__FILE__) . 'class-ipswich-events-results-data-access.php';

class Ipswich_Events_Results_WP_REST_API_Controller_V1
{

	private $data_access;

	private $user;

	public function __construct()
	{
		$this->data_access = new Ipswich_Events_Results_Data_Access();
	}

	public function rest_api_init()
	{

		$namespace = 'ipswich-events-api/v1'; // base endpoint for our custom API

		$this->register_routes_results($namespace);
	}

	public function plugins_loaded()
	{

		// enqueue WP_API_Settings script
		add_action('wp_print_scripts', function () {
			wp_enqueue_script('wp-api');
		});
	}

	private function register_routes_results($namespace)
	{
		register_rest_route($namespace, '/events/(?P<eventId>[\d]+)/races/(?P<raceId>[\d]+)/results', array(
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => array($this, 'get_race_results'),
			'args'                => array(
				'raceId'           => array(
					'required'          => true,
					'validate_callback' => array($this, 'is_valid_id')
				),
				'eventId'           => array(
					'required'          => true,
					'validate_callback' => array($this, 'is_valid_id')
				)
			)
		));

		register_rest_route($namespace, '/events/(?P<eventId>[\d]+)/meetings', array(
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => array($this, 'get_meetings'),
			'args'                => array(
				'eventId'           => array(
					'required'          => true,
					'validate_callback' => array($this, 'is_valid_id')
				)
			)
		));

		register_rest_route($namespace, '/events/(?P<eventId>[\d]+)/meetings/(?P<meetingId>[\d]+)/races', array(
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => array($this, 'get_races'),
			'args'                => array(
				'eventId'           => array(
					'required'          => true,
					'validate_callback' => array($this, 'is_valid_id')
				)
			)
		));

		register_rest_route($namespace, '/events', array(
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => array($this, 'get_events')
		));
	}

	public function get_race_results(\WP_REST_Request $request)
	{
		$response = $this->data_access->get_race_results($request['raceId']);

		$rows = array_map('str_getcsv', explode("\n", $response[0]->results));
        $header = array_shift($rows); // Get the header row

        $jsonArray = [];
        foreach ($rows as $row) {
            $jsonArray[] = array_combine($header, $row);
        }

		return rest_ensure_response($jsonArray);
	}

	public function get_meetings(\WP_REST_Request $request)
	{
		$response = $this->data_access->get_meetings($request['eventId']);

		return rest_ensure_response($response);
	}

	public function get_races(\WP_REST_Request $request)
	{
		$response = $this->data_access->get_races($request['meetingId']);

		return rest_ensure_response($response);
	}

	public function get_events(\WP_REST_Request $request)
	{
		$response = $this->data_access->get_events();

		return rest_ensure_response($response);
	}

	public function is_valid_id($value, $request, $key)
	{
		if ($value < 1) {
			// can return false or a custom \WP_Error
			return new \WP_Error(
				'rest_invalid_param',
				sprintf('%s %d must be greater than 0', $key, $value),
				array('status' => 400)
			);
		} else {
			return true;
		}
	}
}
