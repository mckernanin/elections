<?php
/**
 * Custom fields and metaboxes.
 *
 * @package OA_Elections
 */

/**
 * Include and setup custom metaboxes and fields.
 *
 * @category OA-Elections
 * @package  CMB2
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     https://github.com/WebDevStudios/CMB2
 */
class OAE_Fields {

	/**
	 * Get the bootstrap!
	 */
	function __construct() {
		$this->load_fields();
		include( 'lib/cmb2-custom-field-type-address.php' );
	}

	/**
	 * Load all fields.
	 */
	public function load_fields() {
		add_action( 'cmb2_init', array( $this, 'admin_metaboxes' ) );
		add_action( 'cmb2_init', array( $this, 'election_metaboxes' ) );
		add_action( 'cmb2_init', array( $this, 'candidate_metaboxes' ) );
		add_action( 'cmb2_init', array( $this, 'user_metaboxes' ) );
	}

	/**
	 * Static function for getting field data.
	 *
	 * @param string $field_name The name of the field to query.
	 * @param int 	 $id		 The ID of the post to query (optional).
	 */
	static function get( $field_name = null, $id = null ) {
		if ( null === $id ) {
			$id = get_the_ID();
		}
		if ( false === strpos( $field_name, '_oa_election_' ) ) {
			$field_name = '_oa_election_' . $field_name;
		}
		$field = get_post_meta( $id, $field_name, true );
		return '' !== $field ? $field : false;
	}

	/**
	 * Static function for updating field data.
	 *
	 * @param string $field_name The name of the field to update.
	 * @param string $value 	 The value to set.
	 * @param int 	 $id		 The ID of the post to update (optional).
	 */
	static function update( $field_name = null, $value = null, $id = null ) {
		if ( null === $id ) {
			$id = get_the_ID();
		}
		if ( false === strpos( $field_name, '_oa_election_' ) ) {
			$field_name = '_oa_election_' . $field_name;
		}
		$field = update_post_meta( $id, $field_name, $value );
		return '' !== $field ? $field : false;
	}

	/**
	 * Define the metabox and field configurations.
	 */
	public function election_metaboxes() {

		/**
		 * Initiate the metabox
		 */
		$cmb = new_cmb2_box( array(
			'id'            => 'unit_fields',
			'title'         => __( 'Unit Fields', 'OA-Elections' ),
			'object_types'  => array( 'oae_election' ),
			'context'       => 'normal',
			'priority'      => 'core',
			'show_names'    => true,
		) );

		/**
		 * Unit Leader Fields
		 */

		$prefix = '_oa_election_leader_';

		$cmb->add_field( array(
			'name' => 'Unit Leader Information',
			'type' => 'title',
			'id'   => 'unit_leader',
		) );

		$cmb->add_field( array(
			'name' => 'First Name',
			'id'   => $prefix . 'fname',
			'type' => 'text',
		) );

		$cmb->add_field( array(
			'name' => 'Last Name',
			'id'   => $prefix . 'lname',
			'type' => 'text',
		) );

		$cmb->add_field( array(
			'name' => 'Phone',
			'id'   => $prefix . 'phone',
			'type' => 'text',
		) );

		$cmb->add_field( array(
			'name' => 'Email',
			'id'   => $prefix . 'email',
			'type' => 'text_email',
		) );

		$cmb->add_field( array(
			'name'    => 'Unit Leader Position',
			'id'      => $prefix . 'position',
			'type'    => 'select',
			'options' => array(
				''                      => '---',
				'scoutmaster'           => __( 'Scoutmaster', 'OA-Elections' ),
				'assistant-scoutmaster' => __( 'Assistant Scoutmaster', 'OA-Elections' ),
				'committee-chair'       => __( 'Committee Chair', 'OA-Elections' ),
				'committee-member'      => __( 'Committee Member', 'OA-Elections' ),
				'other'                 => __( 'Other', 'OA-Elections' ),
			),
		) );

		$cmb->add_field( array(
			'name' => 'Unit Leader Position - Other',
			'id'   => $prefix . 'position_other',
			'type' => 'text',
			'attributes' => array(
				'data-conditional-id'    => $prefix . 'position',
				'data-conditional-value' => 'other',
				'required'               => true,
			),
		) );

		$cmb->add_field( array(
			'name'    => 'Your involvement in the Order of the Arrow',
			'id'      => $prefix . 'involvement',
			'type'    => 'select',
			'options' => array(
				''            => '---',
				'non-member'  => __( 'Non-member', 'OA-Elections' ),
				'ordeal'      => __( 'Ordeal', 'OA-Elections' ),
				'brotherhood' => __( 'Brotherhood', 'OA-Elections' ),
				'vigil'       => __( 'Vigil', 'OA-Elections' ),
				'other'       => __( 'Other', 'OA-Elections' ),
			),
		) );

		$cmb->add_field( array(
			'name' => 'How many elections have you previously organized?',
			'id'   => $prefix . 'previous_elections',
			'type' => 'text',
		) );

		$cmb->add_field( array(
			'name' => 'Copied Emails',
			'desc' => 'Enter email addresses for any members of your troop who you would like to be copied on status updates. <strong>Do not copy candidates!</strong>',
			'id'   => $prefix . 'copied_emails',
			'type' => 'text',
			'repeatable' => true,
		) );

		/**
		 * Unit Information Fields
		 */

		$prefix = '_oa_election_unit_';

		$cmb->add_field( array(
			'name' => 'Unit Information',
			'type' => 'title',
			'id'   => 'unit',
		) );

		$cmb->add_field( array(
			'name' => 'Unit Number',
			'id'   => $prefix . 'number',
			'type' => 'text',
		) );

		$cmb->add_field( array(
			'name'     => 'District / Chapter',
			'id'       => $prefix . 'chapter',
			'type'     => 'taxonomy_select',
			'taxonomy' => 'oae_chapter',
		) );

		$cmb->add_field( array(
			'name' => 'Typical Attendance',
			'id'   => $prefix . 'attendance',
			'type' => 'text',
		) );

		$cmb->add_field( array(
			'name' => 'Meeting Location',
			'id'   => $prefix . 'location',
			'type' => 'pw_map',
		) );

		$cmb->add_field( array(
			'name' => 'Meeting Location Details',
			'desc' => 'Name of place, where in the building you meet, etc.',
			'id'   => $prefix . 'location_details',
			'type' => 'textarea',
		) );

		$cmb->add_field( array(
			'name' => 'Meeting Time',
			'id'   => $prefix . 'meeting_time',
			'type' => 'text_time',
		) );

		$cmb->add_field( array(
			'name' => 'Date 1',
			'desc' => 'Please provide 3 date possibilities',
			'id'   => $prefix . 'date_1',
			'type' => 'text_date',
		) );

		$cmb->add_field( array(
			'name' => 'Date 2',
			'desc' => 'Please provide 3 date possibilities',
			'id'   => $prefix . 'date_2',
			'type' => 'text_date',
		) );

		$cmb->add_field( array(
			'name' => 'Date 3',
			'desc' => 'Please provide 3 date possibilities',
			'id'   => $prefix . 'date_3',
			'type' => 'text_date',
		) );

		/**
		 * Unit Adviser Fields
		 */

		$prefix = '_oa_election_unit_adviser_';

		$cmb->add_field( array(
			'name' => 'Unit Adviser Information',
			'type' => 'title',
			'id'   => 'unit_adviser',
		) );

		$cmb->add_field( array(
			'name' => 'First Name',
			'id'   => $prefix . 'fname',
			'type' => 'text',
		) );

		$cmb->add_field( array(
			'name' => 'Last Name',
			'id'   => $prefix . 'lname',
			'type' => 'text',
		) );

		$cmb->add_field( array(
			'name' => 'Phone',
			'id'   => $prefix . 'phone',
			'type' => 'text',
		) );

		$cmb->add_field( array(
			'name' => 'Email',
			'id'   => $prefix . 'email',
			'type' => 'text_email',
		) );

		/**
		 * Unit Representative Fields
		 */

		$prefix = '_oa_election_unit_representative_';

		$cmb->add_field( array(
			'name' => 'Unit Representative Information',
			'type' => 'title',
			'id'   => 'unit_representative',
		) );

		$cmb->add_field( array(
			'name' => 'First Name',
			'id'   => $prefix . 'fname',
			'type' => 'text',
		) );

		$cmb->add_field( array(
			'name' => 'Last Name',
			'id'   => $prefix . 'lname',
			'type' => 'text',
		) );

		$cmb->add_field( array(
			'name' => 'Phone',
			'id'   => $prefix . 'phone',
			'type' => 'text',
		) );

		$cmb->add_field( array(
			'name' => 'Email',
			'id'   => $prefix . 'email',
			'type' => 'text_email',
		) );
	}

	/**
	 * Admin metaboxes on elections
	 */
	public function admin_metaboxes() {
		/**
		 * Initiate the metabox
		 */
		$election_admin = new_cmb2_box( array(
			'id'            => 'admin_fields',
			'title'         => __( 'Admin Fields', 'OA-Elections' ),
			'object_types'  => array( 'oae_election' ),
			'context'       => 'normal',
			'priority'      => 'core',
			'show_names'    => true,
		) );

		/**
		 * Admin Fields
		 */

		$prefix = '_oa_election_';

		$election_admin->add_field( array(
			'name'     => 'Election Status',
			'id'       => $prefix . 'status',
			'type'     => 'taxonomy_select',
			'taxonomy' => 'oae_status',
		));

		$election_admin->add_field( array(
			'name' => 'Election Date',
			'id'   => $prefix . 'selected_date',
			'type' => 'text_date',
		));

		$election_admin->add_field( array(
			'name'    => 'Candidates',
			'desc'    => 'Drag posts from the left column to the right column to attach them to this page.<br />You may rearrange the order of the posts in the right column by dragging and dropping.',
			'id'      => $prefix . 'candidates',
			'type'    => 'custom_attached_posts',
			'options' => array(
				'show_thumbnails' => true,
				'filter_boxes'    => true,
				'query_args'      => array( 'post_type' => 'oae_candidate' ),
			),
		));
	}

	/**
	 * Candidate metaboxes
	 */
	public function candidate_metaboxes() {

		/**
		 * Initiate the metabox
		 */
		$cmb = new_cmb2_box( array(
			'id'            => 'candidate_fields',
			'title'         => __( 'Candidate Fields', 'OA-Elections' ),
			'object_types'  => array( 'oae_candidate' ),
			'context'       => 'normal',
			'priority'      => 'core',
			'show_names'    => true,
		));

		$prefix = '_oa_candidate_';

		$cmb->add_field(  array(
			'name' => __( 'Personal Information', 'OA-Elections' ),
			'id'   => $prefix . 'title',
			'type' => 'title',
		) );

		$cmb->add_field(  array(
			'name' => 'BSA ID',
			'id'   => $prefix . 'bsa_id',
			'type' => 'text',
		) );

		$cmb->add_field(  array(
			'name' => 'Date of Birth',
			'id'   => $prefix . 'dob',
			'type' => 'text_date',
		) );

		$cmb->add_field(  array(
			'name' => 'First Name',
			'id'   => $prefix . 'fname',
			'type' => 'text',
		) );

		$cmb->add_field(  array(
			'name' => 'Last Name',
			'id'   => $prefix . 'lname',
			'type' => 'text',
		) );

		$cmb->add_field(  array(
			'name' => 'Address',
			'id'   => $prefix . 'address',
			'type' => 'address',
		) );

		$cmb->add_field(  array(
			'name' => 'Parent Phone',
			'id'   => $prefix . 'parent_phone',
			'type' => 'text',
		) );

		$cmb->add_field(  array(
			'name' => 'Parent Email',
			'id'   => $prefix . 'parent_email',
			'type' => 'text_email',
		) );

		$cmb->add_field(  array(
			'name' => 'Youth Phone',
			'id'   => $prefix . 'youth_phone',
			'type' => 'text',
		) );

		$cmb->add_field(  array(
			'name' => 'Youth Email',
			'id'   => $prefix . 'youth_email',
			'type' => 'text_email',
		) );

		$cmb->add_field(  array(
			'name' => __( 'Eligibility Information', 'cmb2' ),
			'id'   => $prefix . 'eligibility_information',
			'type' => 'title',
		) );

		$cmb->add_field(  array(
			'name' => 'Camping Nights - Long Term',
			'id'   => $prefix . 'camping_long_term',
			'type' => 'text',
		) );

		$cmb->add_field(  array(
			'name' => 'Camping Nights - Short Term',
			'id'   => $prefix . 'camping_short_term',
			'type' => 'text',
		) );

		$cmb->add_field(  array(
			'name'    => 'Rank',
			'id'      => $prefix . 'rank',
			'type'    => 'select',
			'options' => array(
				null          => __( '---', 'OA-Elections' ),
				'first-class' => __( 'First Class', 'OA-Elections' ),
				'star'        => __( 'Star', 'OA-Elections' ),
				'life'        => __( 'Life', 'OA-Elections' ),
				'eagle'       => __( 'Eagle', 'OA-Elections' ),
			),
		) );

		$cmb->add_field(  array(
			'name' => 'Scout Spirit',
			'desc' => 'As the unit leader, it is up to you to approve each candidate. This is just as important of a requirement as the others.',
			'id'   => $prefix . 'scout_spirit',
			'type' => 'checkbox',
		) );

	}

	/**
	 * Admin metaboxes on elections
	 */
	public function user_metaboxes() {
		/**
		 * Initiate the metabox
		 */
		$user = new_cmb2_box( array(
			'id'            => 'user_fields',
			'title'         => __( 'User Fields', 'OA-Elections' ),
			'object_types'  => array( 'user' ),
			'context'       => 'normal',
			'priority'      => 'core',
			'show_names'    => true,
		) );

		/**
		 * Admin Fields
		 */

		$prefix = '_oa_election_user_';

		$user->add_field( array(
			'name' => 'First Name',
			'id'   => $prefix . 'fname',
			'type' => 'text',
		) );

		$user->add_field( array(
			'name' => 'Last Name',
			'id'   => $prefix . 'lname',
			'type' => 'text',
		) );

		$user->add_field( array(
			'name' => 'Phone',
			'id'   => $prefix . 'phone',
			'type' => 'text',
		) );

		$user->add_field( array(
			'name' => 'Email',
			'id'   => $prefix . 'email',
			'type' => 'text_email',
		) );

		$user->add_field( array(
			'name'     => 'Chapter',
			'id'       => $prefix . 'chapter',
			'type'     => 'taxonomy_select',
			'taxonomy' => 'oae_chapter',
		));

		$user->add_field( array(
			'name'     => 'Availability',
			'id'       => $prefix . 'availability',
			'type'     => 'multicheck',
			'options'          => array(
				'monday'    => 'Monday',
				'tuesday'   => 'Tuesday',
				'wednesday' => 'Wednesday',
				'thursday'  => 'Thursday',
				'friday'    => 'Friday',
			),
		));
	}


}

new OAE_Fields();
