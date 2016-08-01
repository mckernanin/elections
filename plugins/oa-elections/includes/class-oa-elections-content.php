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
		add_action( 'init', array( $this, 'post_type_election' ), 0 );
		add_action( 'init', array( $this, 'taxonomy_chapter' ), 0 );
		add_action( 'init', array( $this, 'taxonomy_status' ), 0 );
	}

	/**
	 * Register the election post type.
	 *
	 * @since    1.0.0
	 */
	public function post_type_election() {
		$labels = array(
			'name' 					=> 'Elections',
			'singular_name' 		=> 'Election',
			'menu_name' 			=> 'Elections',
			'parent_item_colon' 	=> 'Parent Item:',
			'all_items' 			=> 'All Elections',
			'view_item' 			=> 'View Election',
			'add_new_item' 			=> 'Add Election',
			'add_new' 				=> 'Add Election',
			'edit_item' 			=> 'Edit Election',
			'update_item' 			=> 'Update Election',
			'search_items' 			=> 'Search Elections',
			'not_found' 			=> 'Not found',
			'not_found_in_trash' 	=> 'Not found in Trash',
		);
		$args = array(
			'label' 				=> 'Elections',
			'description' 			=> 'Elections ',
			'labels' 				=> $labels,
			'supports' 				=> array( 'title', 'thumbnail' ),
			'hierarchical' 			=> false,
			'public' 				=> true,
			'show_ui' 				=> true,
			'show_in_menu' 			=> true,
			'show_in_nav_menus' 	=> true,
			'show_in_admin_bar' 	=> true,
			'menu_position' 		=> 5,
			'menu_icon' 			=> 'dashicons-admin-users',
			'can_export' 			=> true,
			'has_archive'			=> false,
			'exclude_from_search' 	=> true,
			'publicly_queryable' 	=> true,
			'rewrite' 				=> array( 'slug' => 'election' ),
			'capability_type' 		=> 'page',
		);
		register_post_type( 'oa_election', $args );
	}
	/**
	 * Register the chapter taxonomy.
	 *
	 * @since    1.0.0
	 */
	public function taxonomy_chapter() {
		$labels = array(
			'name'                       => _x( 'Chapters', 'Taxonomy General Name', 'oa-elections' ),
			'singular_name'              => _x( 'Chapter', 'Taxonomy Singular Name', 'oa-elections' ),
			'menu_name'                  => __( 'Chapter', 'oa-elections' ),
			'all_items'                  => __( 'All Chapters', 'oa-elections' ),
			'parent_item'                => __( 'Parent Chapter', 'oa-elections' ),
			'parent_item_colon'          => __( 'Parent Chapter:', 'oa-elections' ),
			'new_item_name'              => __( 'New Chapter Name', 'oa-elections' ),
			'add_new_item'               => __( 'Add New Chapter', 'oa-elections' ),
			'edit_item'                  => __( 'Edit Chapter', 'oa-elections' ),
			'update_item'                => __( 'Update Chapter', 'oa-elections' ),
			'separate_items_with_commas' => __( 'Separate groups with commas', 'oa-elections' ),
			'search_items'               => __( 'Search Chapter', 'oa-elections' ),
			'add_or_remove_items'        => __( 'Add or remove groups', 'oa-elections' ),
			'choose_from_most_used'      => __( 'Choose from the most used groups', 'oa-elections' ),
			'not_found'                  => __( 'Not Found', 'oa-elections' ),
		);
		$args = array(
			'labels' 				=> $labels,
			'hierarchical' 			=> true,
			'public' 				=> true,
			'show_ui' 				=> true,
			'show_admin_column' 	=> true,
			'show_in_nav_menus' 	=> false,
			'show_tagcloud' 		=> false,
		);
		register_taxonomy( 'oa_chapter', array( 'oa_election' ), $args );
	}

	/**
	 * Register the status taxonomy.
	 *
	 * @since    1.0.0
	 */
	public function taxonomy_status() {
		$labels = array(
			'name'                       => _x( 'Statuses', 'Taxonomy General Name', 'oa-elections' ),
			'singular_name'              => _x( 'Status', 'Taxonomy Singular Name', 'oa-elections' ),
			'menu_name'                  => __( 'Status', 'oa-elections' ),
			'all_items'                  => __( 'All Statuses', 'oa-elections' ),
			'parent_item'                => __( 'Parent Status', 'oa-elections' ),
			'parent_item_colon'          => __( 'Parent Status:', 'oa-elections' ),
			'new_item_name'              => __( 'New Status Name', 'oa-elections' ),
			'add_new_item'               => __( 'Add New Status', 'oa-elections' ),
			'edit_item'                  => __( 'Edit Status', 'oa-elections' ),
			'update_item'                => __( 'Update Status', 'oa-elections' ),
			'separate_items_with_commas' => __( 'Separate groups with commas', 'oa-elections' ),
			'search_items'               => __( 'Search Status', 'oa-elections' ),
			'add_or_remove_items'        => __( 'Add or remove groups', 'oa-elections' ),
			'choose_from_most_used'      => __( 'Choose from the most used groups', 'oa-elections' ),
			'not_found'                  => __( 'Not Found', 'oa-elections' ),
		);
		$args = array(
			'labels' 				=> $labels,
			'hierarchical' 			=> true,
			'public' 				=> true,
			'show_ui' 				=> true,
			'show_admin_column' 	=> true,
			'show_in_nav_menus' 	=> false,
			'show_tagcloud' 		=> false,
		);
		register_taxonomy( 'oa_election_status', array( 'oa_election' ), $args );
	}
}
