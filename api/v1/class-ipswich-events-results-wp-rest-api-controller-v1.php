<?php
namespace IpswichEventResultsAPI\V1;
	
require_once plugin_dir_path( __FILE__ ) .'class-ipswich-events-results-data-access.php';

class Ipswich_Events_Results_WP_REST_API_Controller_V1 {
	
	private $data_access;
	
	private $user;
	
	public function __construct() {
		$this->data_access = new Ipswich_Events_Results_Data_Access();
	}
	
	public function rest_api_init( ) {			
		
		$namespace = 'ipswich-events-api/v1'; // base endpoint for our custom API
				
		$this->register_routes_authentication($namespace);
		$this->register_routes_results($namespace);						
		
		add_filter( 'rest_endpoints', array( $this, 'remove_wordpress_core_endpoints'), 10, 1 );			
	}
	
	public function plugins_loaded() {

		// enqueue WP_API_Settings script
		add_action( 'wp_print_scripts', function() {
			wp_enqueue_script( 'wp-api' );
		} );					
	}
	
	private function register_routes_authentication($namespace) {					
		register_rest_route( $namespace, '/login', array(
			'methods'             => \WP_REST_Server::CREATABLE,
			'callback'            => array( $this, 'login' ),
			'args'                => array(
				'username'           => array(
					'required'          => true						
					),
				'password'           => array(
					'required'          => true						
					)
				)				
		) );					
	}

  private function register_routes_results($namespace) {
		
		register_rest_route( $namespace, '/results/(?P<eventId>[\d]+)/(?P<year>[\d]+)/(?P<leg>[\d]+)', array(
			'methods'             => \WP_REST_Server::READABLE,				
			'callback'            => array( $this, 'get_raceResults' ),
			'args'                => array(
				'eventId'           => array(
					'required'          => true,						
					'validate_callback' => array( $this, 'is_valid_id' )
					)
				),
        'year'           => array(
					'required'          => true,						
					'validate_callback' => array( $this, 'is_valid_id' )
					)
				),
        'leg'           => array(
					'required'          => false,						
					'validate_callback' => array( $this, 'is_valid_id' )
					)
				)
		) );			
	}		

		
		public function get_raceResults( \WP_REST_Request $request ) {
		    $response = $this->data_access->getRaceResults($request['eventId'], $request['year'], $request['leg']);

			return rest_ensure_response( $response );
		}		
		
		public function permission_check( \WP_REST_Request $request ) {
			$id = $this->basic_auth_handler($this->user);
			if ( $id  <= 0 ) {				
				return new \WP_Error( 'rest_forbidden',
					sprintf( 'You must be logged in to use this API.' ), array( 'status' => 403 ) );
			} else if (!user_can( $id, 'publish_pages' )){
				return new \WP_Error( 'rest_forbidden',
					sprintf( 'You do not have enough privileges to use this API.' ), array( 'status' => 403 ) );
			} else {
				return true;
			}
		}
	
		/**
		 * Unsets all core WP endpoints registered by the WordPress REST API (via rest_endpoints filter)
		 * @param  array   $endpoints   registered endpoints
		 * @return array
		 */
		public function remove_wordpress_core_endpoints( $endpoints ) {

			foreach ( array_keys( $endpoints ) as $endpoint ) {
				if ( stripos( $endpoint, '/wp/v2' ) === 0 ) {
					unset( $endpoints[ $endpoint ] );
				}
			}

			return $endpoints;
		}
		
		public function login(\WP_REST_Request $request) {
			$username = base64_decode($request['username']);
			$password = base64_decode($request['password']);
			
			$this->user = wp_authenticate( $username, $password );
			
			return $this->user;
		}
		
		private function basic_auth_handler( $user ) {
			// Don't authenticate twice
			if ( ! empty( $user ) ) {
				return $user->ID;
			}
			
			// Check that we're trying to authenticate
			if ( !isset( $_SERVER['PHP_AUTH_USER'] ) ) {
				return $user->ID;
			}
			$username = $_SERVER['PHP_AUTH_USER'];
			$password = $_SERVER['PHP_AUTH_PW'];
			
			/**
			 * In multi-site, wp_authenticate_spam_check filter is run on authentication. This filter calls
			 * get_currentuserinfo which in turn calls the determine_current_user filter. This leads to infinite
			 * recursion and a stack overflow unless the current function is removed from the determine_current_user
			 * filter during authentication.
			 */
			remove_filter( 'determine_current_user', 'json_basic_auth_handler', 20 );
			$user = wp_authenticate( $username, $password );
			add_filter( 'determine_current_user', 'json_basic_auth_handler', 20 );
			if ( is_wp_error( $user ) ) {				
				return 0;
			}
			
			return $user->ID;
		}	

	public function is_valid_id( $value, $request, $key ) {
		if ( $value < 1 ) {
			// can return false or a custom \WP_Error
			return new \WP_Error( 'rest_invalid_param',
				sprintf( '%s %d must be greater than 0', $key, $value ), array( 'status' => 400 ) );
		} else {
			return true;
		}
	}		
}