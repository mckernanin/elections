<?php
/**
 * Register post types and taxonomies.
 *
 * @link       http://mckernan.in
 * @since      1.0.0
 * @package    OA Tools
 */

/**
 * Register post types and taxonomies.
 *
 * @link       http://mckernan.in
 * @since      1.0.0
 * @package    OA Tools
 */
class OA_Elections_Content {
	/**
	 * Constructor.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->register();
	}

	public function register() {
		require_once( 'lib/class-cpt.php' );

		$election = new CPT([
			'post_type_name' => 'oae_election',
			'singular'       => 'Election',
			'plural'         => 'Elections',
		]);
		$election->register_taxonomy( 'status' );
		$election->register_taxonomy( 'chapter' );

		$candidate = new CPT([
			'post_type_name' => 'oae_candidate',
			'singular'       => 'Candidate',
			'plural'         => 'Candidates',
		]);
		$candidate->register_taxonomy( 'status' );
		$candidate->register_taxonomy( 'chapter' );
	}
}
