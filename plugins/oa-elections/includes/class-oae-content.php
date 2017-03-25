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
class OAE_Content {
	/**
	 * Constructor.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->register();
	}

	/**
	 * Register post types
	 */
	public function register() {
		require_once( 'lib/class-cpt.php' );

		$election = new CPT(
			[
			'post_type_name' => 'oae_election',
			'singular'       => 'Election',
			'plural'         => 'Elections',
			'slug'           => 'election',
			],
			[
				'supports'   => [ 'title', 'author' ],
			]
		);
		$election->register_taxonomy([
		    'taxonomy_name' => 'oae_status',
		    'singular'      => 'Status',
		    'plural'        => 'Statuses',
		    'slug'          => 'status',
		]);
		$election->register_taxonomy([
		    'taxonomy_name' => 'oae_chapter',
		    'singular'      => 'Chapter',
		    'plural'        => 'Chapters',
		    'slug'          => 'chapter',
		]);

		$candidate = new CPT([
			'post_type_name' => 'oae_candidate',
			'singular'       => 'Candidate',
			'plural'         => 'Candidates',
		]);
		$candidate->register_taxonomy([
		    'taxonomy_name' => 'oae_cand_status',
		    'singular'      => 'Status',
		    'plural'        => 'Statuses',
		    'slug'          => 'status',
		]);
		$candidate->register_taxonomy([
		    'taxonomy_name' => 'oae_chapter',
		    'singular'      => 'Chapter',
		    'plural'        => 'Chapters',
		    'slug'          => 'chapter',
		]);

		$nomination = new CPT([
			'post_type_name' => 'oae_nomination',
			'singular'       => 'Nomination',
			'plural'         => 'Nominations',
		]);
		$nomination->register_taxonomy([
			'taxonomy_name' => 'oae_nom_status',
			'singular'      => 'Status',
			'plural'        => 'Statuses',
			'slug'          => 'status',
		]);
		$nomination->register_taxonomy([
			'taxonomy_name' => 'oae_chapter',
			'singular'      => 'Chapter',
			'plural'        => 'Chapters',
			'slug'          => 'chapter',
		]);
		$nomination->register_taxonomy([
			'taxonomy_name' => 'oae_nom_type',
			'singular'      => 'Type',
			'plural'        => 'Types',
			'slug'          => 'type',
		]);
	}
}
