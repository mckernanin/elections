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
class OAE_REST {
	/**
	 * Constructor.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_api_hooks' ) );

		register_activation_hook( __FILE__, 'activate' );
	}

	/**
	 * Activation functions.
	 */
	function activate() {
		flush_rewrite_rules();
	}

	/**
	 * Register routes.
	 */
	function register_api_hooks() {
		$namespace = 'oa-elections/v1';

		register_rest_route( $namespace, '/election-dates/', array(
			'methods'  => 'GET',
			'callback' => array( $this, 'get_election_dates' ),
		));

		register_rest_route( $namespace, '/schedule-election/', array(
			'methods'  => 'POST',
			'callback' => array( $this, 'schedule_election' ),
		));

		register_rest_route( $namespace, '/set-election-results/', array(
			'methods'  => 'POST',
			'callback' => array( $this, 'set_election_results' ),
		));

		register_rest_route( $namespace, '/set-nomination-status/', array(
			'methods'  => 'POST',
			'callback' => array( $this, 'set_nomination_status' ),
		));

		register_rest_route( $namespace, '/reports/elections_by_chapter/', array(
			'methods'  => 'GET',
			'callback' => array( $this, 'report_elections_by_chapter' ),
		));

		register_rest_route( $namespace, '/reports/candidates_by_chapter/', array(
			'methods'  => 'GET',
			'callback' => array( $this, 'report_candidates_by_chapter' ),
		));
	}

	/**
	 * Get election dates for calendar shortcode
	 */
	function get_election_dates() {
		$args = array(
			'post_type' => 'oae_election',
			'posts_per_page' => 500,
		);
		if ( 'all' !== $_GET['chapter'] ) {
			$args['tax_query'] = [
				[
					'taxonomy' => 'oae_chapter',
					'field' => 'id',
					'terms' => abs( $_GET['chapter'] ),
				],
			];
		}
		$query       = new WP_Query( $args );
		$date_fields = [ 'unit_date_1', 'unit_date_2', 'unit_date_3' ];
		$return      = array();
		while ( $query->have_posts() ) {
			$query->the_post();
			global $post;
			$id            = get_the_id();
			$election_date = OAE_Fields::get( 'selected_date', $id );
			$time          = OAE_Fields::get( 'unit_meeting_time', $id );

			if ( $election_date ) {
				$start    = date( 'Y-m-d\TH:i:sP', strtotime( $election_date . ' ' . $time ) );
				$return[] = array(
					'ID'        => $id,
					'title'     => get_the_title(),
					'start'     => $start,
					'url'       => get_the_permalink(),
					'className' => 'scheduled-election',
				);
			} else {
				foreach ( $date_fields as $value ) {

					$date     = OAE_Fields::get( $value, $id );
					$start    = date( 'Y-m-d\TH:i:sP', strtotime( $date . ' ' . $time ) );
					$return[] = array(
						'ID'        => $id,
						'title'     => get_the_title(),
						'start'     => $start,
						'url' 		=> '#' . $id . '_' . $value,
						'datefield' => $value,
						'className' => [
							'unit-' . $post->post_name,
							'requested-election',
						],
					);
				}
			}
		}
		$response = new WP_REST_Response( $return );
		$response->header( 'Access-Control-Allow-Origin', apply_filters( 'access_control_allow_origin', '*' ) );

		return $response;
	}

	/**
	 * Schedule elections
	 */
	function schedule_election() {

		$elections = $_POST['elections'];
		$i;
		$response = [];

		foreach ( $elections as $election ) {
			$post_id = $election['postID'];
			$selected_date = get_post_meta( $post_id, '_oa_election_unit_date_' . $election['selectedDate'], true );
			update_post_meta( $post_id, '_oa_election_selected_date', $selected_date );
			wp_set_object_terms( $post_id, 'scheduled', 'oae_status' );
			$response['scheduled_elections'][] = [
				'unit' => get_the_title( $election['postID'] ),
				'date' => $selected_date,
			];
			do_action( 'election_schedule', $post_id );
			$i++;
		}
		$response['message'] = 'Elections have been scheduled for ' . $i . ' units.';

		$response = new WP_REST_Response( $response );
		$response->header( 'Access-Control-Allow-Origin', apply_filters( 'access_control_allow_origin', '*' ) );

		return $response;
	}

	/**
	 * Submit the election report
	 */
	public function set_election_results() {

		if ( ! isset( $_POST['report'] ) ) {
			return;
		}

		$report = $_POST['report'];
		$post_id = absint( $report['ID'] );
		wp_set_object_terms( $post_id, 'results-entered', 'oae_status' );
		update_post_meta( $post_id, '_oa_election_registered_youth', absint( $report['registeredActiveYouth'] ) );
		update_post_meta( $post_id, '_oa_election_youth_attendance', absint( $report['youthAttendance'] ) );
		update_post_meta( $post_id, '_oa_election_election_one_ballots', absint( $report['electionOne']['ballots'] ) );
		update_post_meta( $post_id, '_oa_election_election_two_ballots', absint( $report['electionTwo']['ballots'] ) );

		$candidates = OAE_Fields::get( 'candidates', $post_id );

		foreach ( $candidates as $candidate ) {
			if ( in_array( absint( $candidate ), $report['candidates'], true ) ) {
				wp_set_object_terms( $candidate, 'elected', 'oae_cand_status' );
			} else {
				wp_set_object_terms( $candidate, 'not-elected', 'oae_cand_status' );
			}
		}

		$response = 'Election results have been saved.';
		$response = new WP_REST_Response( $response );
		return $response;
	}

	/**
	 * Data endpoint, elections by chapter.
	 */
	public function report_elections_by_chapter() {

		$chapters = get_terms( [
			'taxonomy'   => 'oae_chapter',
			'hide_empty' => false,
		]);

		$response = [
			'sum' => 0,
			'results' => [],
		];

		foreach ( $chapters as $chapter ) {
			$args = [
				'post_type'      => 'oae_election',
				'posts_per_page' => 500,
				'oae_chapter'    => $chapter->slug,
			];
			$elections = new WP_Query( $args );
			$chapter_name = OAE_Util::chapter_name_from_slug( $chapter->slug );

			$response['results'][ $chapter_name ] = $elections->post_count;
			$response['sum'] = $response['sum'] + $elections->post_count;
		}

		$response = new WP_REST_Response( $response );
		return $response;
	}

	/**
	 * Data endpoint, candidates by chapter.
	 */
	public function report_candidates_by_chapter() {

		$chapters = get_terms( [
			'taxonomy'   => 'oae_chapter',
			'hide_empty' => false,
		]);

		$response = [
			'sum' => 0,
			'results' => [],
		];

		foreach ( $chapters as $chapter ) {
			$args = [
				'post_type'       => 'oae_candidate',
				'posts_per_page'  => 500,
				'oae_chapter'     => $chapter->slug,
				'oae_cand_status' => 'elected',
			];
			$candidates = new WP_Query( $args );
			$chapter_name = OAE_Util::chapter_name_from_slug( $chapter->slug );

			$response['results'][ $chapter_name ] = $candidates->post_count;
			$response['sum'] = $response['sum'] + $candidates->post_count;
		}

		$response = new WP_REST_Response( $response );
		return $response;
	}

	/**
	 * Submit the election report
	 */
	public function set_nomination_status() {

		if ( ! isset( $_POST['id'] ) ) {
			return 'Data missing, please try again.';
		}

		$post_id = absint( $_POST['id'] );
		wp_set_object_terms( $post_id, $_POST['status'], 'oae_nom_status' );

		$response = 'Status updated';
		$response = new WP_REST_Response( $response );
		return $response;
	}
}

new OAE_REST();
