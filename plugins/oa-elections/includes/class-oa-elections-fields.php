<?php
/**
 * Include and setup custom metaboxes and fields.
 *
 * @category OA-Elections
 * @package  CMB2
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     https://github.com/WebDevStudios/CMB2
 */

class OA_Elections_Fields {

	/**
	 * Get the bootstrap!
	 */

	function __construct() {
		$this->load_fields();
	}

	/**
	 * Load all fields.
	 */
	public function load_fields() {
		add_action( 'cmb2_init', array( $this, 'election_metaboxes' ) );
	}

	/**
	 * Static function for getting field data.
	 */
	static function get( $field_name = null, $id = null ) {
		if ( null === $id ) {
			$id = get_the_ID();
		}
		if ( false === strpos( $field_name, '_oa_election_' ) ) {
			$field_name = '_oa_election_' . $field_name;
		}
		$field = get_post_meta( $id, $field_name, true );
		return '' != $field ? $field : false;
	}

	/**
	 * Define the metabox and field configurations.
	 */
	public function election_metaboxes() {

		// Start with an underscore to hide fields from custom fields list
		$prefix = '_oa_election_leader_';

		/**
		 * Initiate the metabox
		 */
		$cmb = new_cmb2_box( array(
			'id'            => 'unit_fields',
			'title'         => __( 'Unit Fields', 'OA-Elections' ),
			'object_types'  => array( 'oae_election' ), // Post type
			'context'       => 'normal',
			'priority'      => 'core',
			'show_names'    => true, // Show field names on the left
			// 'cmb_styles' => false, // false to disable the CMB stylesheet
			// 'closed'     => true, // Keep the metabox closed by default
		) );

		// Unit Leader info

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

		$prefix = '_oa_election_unit_';

		$cmb->add_field( array(
			'name' => 'Unit Information',
			'type' => 'title',
			'id'   => 'unit',
		) );

		// Unit info
		$cmb->add_field( array(
			'name' => 'Unit Number',
			'id'   => $prefix . 'number',
			'type' => 'text',
		) );

		$cmb->add_field( array(
			'name'    => 'District / Chapter',
			'id'      => $prefix . 'chapter',
			'type'     => 'taxonomy_select',
			'taxonomy' => 'oa_chapter', // Taxonomy Slug
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

		$prefix = '_oa_election_unit_adviser_';

		$cmb->add_field( array(
			'name' => 'Unit Adviser Information',
			'type' => 'title',
			'id'   => 'unit_adviser',
		) );

		// Unit Leader info
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

		$prefix = '_oa_election_candidates_';

		// Start with an underscore to hide fields from custom fields list
		$prefix = '_oa_election_';

		/**
		 * Initiate the metabox
		 */
		$election_admin = new_cmb2_box( array(
			'id'            => 'admin_fields',
			'title'         => __( 'Admin Fields', 'OA-Elections' ),
			'object_types'  => array( 'oa_election' ), // Post type
			'context'       => 'normal',
			'priority'      => 'core',
			'show_names'    => true, // Show field names on the left
			// 'cmb_styles' => false, // false to disable the CMB stylesheet
			// 'closed'     => true, // Keep the metabox closed by default
		) );

		// Unit Leader info

		$election_admin->add_field( array(
			'name'     => 'Election Status',
			'id'       => $prefix . 'status',
			'type'     => 'taxonomy_select',
			'taxonomy' => 'oa_election_status', // Taxonomy Slug
		) );

		$election_admin->add_field( array(
			'name' => 'Election Date',
			'id'   => $prefix . 'selected_date',
			'type' => 'text_date',
		) );
	}

	public function candidate_metaboxes() {
		/**
		 * Initiate the metabox
		 */
		$cmb_group = new_cmb2_box( array(
			'id'            => 'candidate_fields',
			'title'         => __( 'Candidate Fields', 'OA-Elections' ),
			'object_types'  => array( 'oae_election' ), // Post type
			'context'       => 'normal',
			'priority'      => 'core',
			'show_names'    => true, // Show field names on the left
			// 'cmb_styles' => false, // false to disable the CMB stylesheet
			// 'closed'     => true, // Keep the metabox closed by default
		) );

		$group_field_id = $cmb_group->add_field( array(
			'id'          => $prefix . 'candidate',
			'type'        => 'group',
			'description' => __( 'Election Candidate', 'OA-Elections' ),
			'options'     => array(
				'group_title'   => __( 'Candidate {#}', 'OA-Elections' ), // {#} gets replaced by row number
				'add_button'    => __( 'Add Another Candidate', 'OA-Elections' ),
				'remove_button' => __( 'Remove Candidate', 'OA-Elections' ),
				'sortable'      => true, // beta
				// 'closed'     => true, // true to have the groups closed by default
			),
		) );

		$cmb_group->add_group_field( $group_field_id, array(
			'name' => __( 'Personal Information', 'OA-Elections' ),
			'id'   => 'title',
			'type' => 'title',
		) );

		$cmb_group->add_group_field( $group_field_id, array(
			'name' => 'BSA ID',
			'id'   => 'bsa-id',
			'type' => 'text',
		) );

		$cmb_group->add_group_field( $group_field_id, array(
			'name' => 'Date of Birth',
			'id'   => 'dob',
			'type' => 'text_date',
		) );

		$cmb_group->add_group_field( $group_field_id, array(
			'name' => 'First Name',
			'id'   => 'fname',
			'type' => 'text',
		) );

		$cmb_group->add_group_field( $group_field_id, array(
			'name' => 'Last Name',
			'id'   => 'lname',
			'type' => 'text',
		) );

		$cmb_group->add_group_field( $group_field_id, array(
			'name' => 'Address',
			'id'   => 'address',
			'type' => 'address',
		) );

		$cmb_group->add_group_field( $group_field_id, array(
			'name' => 'Parent Phone',
			'id'   => 'parent-phone',
			'type' => 'text',
		) );

		$cmb_group->add_group_field( $group_field_id, array(
			'name' => 'Parent Email',
			'id'   => 'parent-email',
			'type' => 'text_email',
		) );

		$cmb_group->add_group_field( $group_field_id, array(
			'name' => 'Youth Phone',
			'id'   => 'youth-phone',
			'type' => 'text',
		) );

		$cmb_group->add_group_field( $group_field_id, array(
			'name' => 'Youth Email',
			'id'   => 'youth-email',
			'type' => 'text_email',
		) );

		$cmb_group->add_group_field( $group_field_id, array(
			'name' => __( 'Eligibility Information', 'cmb2' ),
			'id'   => 'eligibility_information',
			'type' => 'title',
		) );


		$cmb_group->add_group_field( $group_field_id, array(
			'name' => 'Camping Nights - Long Term',
			'id'   => 'camping-long-term',
			'type' => 'text',
		) );

		$cmb_group->add_group_field( $group_field_id, array(
			'name' => 'Camping Nights - Short Term',
			'id'   => 'camping-short-term',
			'type' => 'text',
		) );

		$cmb_group->add_group_field( $group_field_id, array(
			'name'    => 'Rank',
			'id'      => 'rank',
			'type'    => 'select',
			'options' => array(
				null          => __( '---', 'OA-Elections' ),
				'first-class' => __( 'First Class', 'OA-Elections' ),
				'star'        => __( 'Star', 'OA-Elections' ),
				'life'        => __( 'Life', 'OA-Elections' ),
				'eagle'       => __( 'Eagle', 'OA-Elections' ),
			),
			) );

		$cmb_group->add_group_field( $group_field_id, array(
			'name' => 'Scout Spirit',
			'desc' => 'As the unit leader, it is up to you to approve each candidate. This is just as important of a requirement as the others.',
			'id'   => 'scout-spirit',
			'type' => 'checkbox',
		) );

	}
}

new OA_Elections_Fields();
