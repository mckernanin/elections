<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://mckernan.in
 * @since      1.0.0
 *
 * @package    OA_Elections
 * @subpackage OA_Elections/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    OA_Elections
 * @subpackage OA_Elections/admin
 * @author     Kevin McKernan <kevin@mckernan.in>
 */
class OAE_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		include_once( 'class-oae-cli.php' );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in OAE_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The OAE_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/oa-elections-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in OAE_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The OAE_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/oa-elections-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register unit-leader role.
	 */
	static function add_unit_leader_role() {
		$unit_leader = get_role( 'unit-leader' );
		if ( null === $unit_leader ) {
			$contributor = get_role( 'contributor' );
			add_role( 'unit-leader', 'Unit Leader', $contributor->capabilities );
			$unit_leader = get_role( 'unit-leader' );
		}
	}

	/**
	 * Register chapter-admin role.
	 */
	static function add_chapter_admin_role() {
		$chapter_admin = get_role( 'chapter-admin' );
		if ( null === $chapter_admin ) {
			$editor = get_role( 'editor' );
			add_role( 'chapter-admin', 'Chapter Admin', $editor->capabilities );
			$chapter_admin = get_role( 'chapter-admin' );
		}
	}

	/**
	 * Register election-team role.
	 */
	static function add_election_team_role() {
		$election_team = get_role( 'election-team' );
		if ( null === $election_team ) {
			$contributor = get_role( 'contributor' );
			add_role( 'election-team', 'Election Team', $contributor->capabilities );
			$election_team = get_role( 'election-team' );
		}
	}

	static function add_council_approval_role() {
		$council_approval = get_role( 'council-approval' );
		if ( null === $council_approval ) {
			$contributor = get_role( 'contributor' );
			add_role( 'council-approval', 'Council Approval', $contributor->capabilities );
			$council_approval = get_role( 'council-approval' );
		}
	}

	static function setup_roles() {
		OAE_Admin::add_council_approval_role();
		OAE_Admin::add_election_team_role();
		OAE_Admin::add_chapter_admin_role();
		OAE_Admin::add_election_team_role();
	}
}
