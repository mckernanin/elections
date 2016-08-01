<?php
/**
 * Handle REST API functionality.
 *
 * @link       http://mckernan.in
 * @since      1.0.0
 * @package    OA Elections
 */

/**
 * Handle REST API functionality.
 *
 * @link       http://mckernan.in
 * @since      1.0.0
 * @package    OA Elections
 */
class OA_Elections_REST {
	/**
	 * Constructor.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_api_hooks' ) );

		register_activation_hook( __FILE__, 'activate' );
	}

	function activate() {
		flush_rewrite_rules();
	}

	function register_api_hooks() {
		$namespace = 'oa-elections/v1';

		register_rest_route( $namespace, '/election-dates/', array(
			'methods'  => 'GET',
			'callback' => array( $this, 'get_election_dates' ),
		) );
	}

	function get_election_dates() {
		$args = array(
			'post_type' => 'oa_election',
		);
		$query       = new WP_Query( $args );
		$date_fields = [ 'unit_date_1', 'unit_date_2', 'unit_date_3' ];
		$return      = array();
		while ( $query->have_posts() ) {
			$query->the_post();
			foreach ( $date_fields as $value ) {
				$id       = get_the_id();
				$date     = OA_Elections_Fields::get( $value, $id );
				$time     = OA_Elections_Fields::get( 'unit_meeting_time', $id );
				$start    = date( 'Y-m-d\TH:i:sP', strtotime( $date . ' ' . $time ) );
				$return[] = array(
					'ID'        => $id,
					'title'     => get_the_title(),
					'start'     => $start,
					'url' 		=> get_the_permalink(),
					'datefield' => $value,
				);
			}
		}
		$response = new WP_REST_Response( $return );
		$response->header( 'Access-Control-Allow-Origin', apply_filters( 'access_control_allow_origin', '*' ) );

		return $response;
	}
}

new OA_Elections_REST();
