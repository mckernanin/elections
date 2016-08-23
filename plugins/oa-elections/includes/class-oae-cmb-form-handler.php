<?php
/**
 * Form handlers for all front end forms
 *
 * @package OA_Elections
 */

/**
 * Form handlers for all front end forms.
 */
class OAE_CMB_Form_Handler {

	protected $post_data;

	protected $post_id;

	protected $election_id;

	protected $action;

	protected $metabox;

	protected $sanitized_values;

	/**
	 * Constructor
	 */
	function __construct( $post ) {
		$this->post_data = $post;
		$this->post_id   = absint( $post['_post_id'] );
		$this->action    = sanitize_text_field( $post['_form_action'] );
		$this->metabox   = sanitize_text_field( $post['object_id'] );
		$this->which_form();
	}

	/**
	 * Check which form is being submitted.
	 */
	public function which_form() {
		switch ( $this->metabox ) {
			case 'unit_fields':
				$this->unit();
				break;

			case 'candidate_fields':
				$this->candidate();
				break;

			default:
				return false;
				break;
		}
	}

	/**
	 * See if the user has an existing account.
	 *
	 * If the user's email address is registered, use their existing account. If not, create one for them.
	 *
	 * @param string $email The user's email address.
	 * @param string $fname The user's first name.
	 * @param string $lname The user's last name.
	 * @param string $role  The role to be assigned to the user.
	 */
	public function check_for_user( $email, $fname, $lname, $role ) {
		if ( null === username_exists( $email ) ) {

			$password = wp_generate_password( 12, false );
			$user_id  = wp_create_user( $email, $password, $email );

			wp_update_user(
				array(
					'ID'          => $user_id,
					'nickname'    => $email,
					'first_name'  => $fname,
					'last_name'	  => $lname,
				)
			);

			$user = new WP_User( $user_id );
			$user->set_role( $role );
			wp_new_user_notification( $user_id, null, 'both' );

		} else {
			$user = get_user_by( 'email', $email );
			$user_id = $user->ID;
		}
		return $user_id;
	}

	/**
	 * Check if we are updating an existing item, or creating a new one.
	 *
	 * @param string $post_type Post type to be created
	 * @param string $post_title Title to give post if creation is necessary.
	 * @param string $post_name Custom slug for post to be created (Optional).
	 */
	public function new_or_update( $post_type, $post_title, $post_name = null ) {
		if ( 'update' !== $this->action ) {
			$this->election_id = $this->post_id;
			$user_id = get_current_user_id();
			$post_data = array(
				'post_type'   => $post_type,
				'post_status' => 'publish',
				'post_author' => $user_id,
				'post_title'  => $post_title,
			);
			if ( null !== $post_name ) {
				$post_data['post_name'] = $post_name;
			}
			$this->post_id = wp_insert_post( $post_data, true );

			if ( ! is_int( $this->post_id ) ) {
				wp_die( 'An error has occured, please try again.' );
			}

			if ( 'oae_candidate' === $post_type ) {
				$candidates = OAE_Fields::get( 'candidates', $this->election_id );
				if ( ! is_array( $candidates ) ) {
					$candidates = [ $post_id ];
				} else {
					$candidates[] = $post_id;
				}
				OAE_Fields::update( 'candidates', $candidates, $election_id );
			}
		}
	}

	/**
	 * Sanitize inputs, update all post meta.
	 *
	 * @param object $cmb CMB2 Form object.
	 */
	public function update_meta( $cmb ) {
		$this->sanitized_values = $cmb->get_sanitized_values( $this->post_data );
		foreach ( $this->sanitized_values as $key => $value ) {
			if ( is_array( $value ) ) {
				$value = array_filter( $value );
				if ( ! empty( $value ) ) {
					update_post_meta( $this->post_id, $key, $value );
				}
			} else {
				update_post_meta( $this->post_id, $key, $value );
			}
		}
	}

	/**
	 * Form submission handler for Unit Edit form.
	 *
	 * @package OA_Elections
	 */
	public function unit() {
		$unit_number   = intval( $this->post_data['_oa_election_unit_number'] );
		$post_title    = 'Troop ' . $unit_number;
		$post_name     = 'troop-' . $unit_number . '-' . date( 'Y' );
		$this->new_or_update( 'oae_election', $post_title, $post_name );
		$cmb           = cmb2_get_metabox( $this->metabox, $this->post_id );
		if ( ! isset( $this->post_data[ $cmb->nonce() ] ) || ! wp_verify_nonce( $this->post_data[ $cmb->nonce() ], $cmb->nonce() ) ) {
			return $cmb->prop( 'submission_error', wp_die( 'security_fail', esc_html( 'Security check failed.' ) ) );
		}
		$this->update_meta( $cmb );

		/*
		 * Redirect back to the form page with a query variable with the new post ID.
		 * This will help double-submissions with browser refreshes
		 */
		$args = array(
			'p' => $post_id,
			'update' => true,
		);

		if ( 'oae_election' !== $current_post_type ) {
			$args['new_election'] = true;
			$args['update'] = false;
			$this->new_election_notification( $post_id );
			wp_set_object_terms( $post_id, 'requested', 'oae_status' );
		}

		wp_safe_redirect( esc_url_raw( add_query_arg( $args ) ) );
		exit;
	}

	/**
	 * Form submission handler for Candidate forms.
	 *
	 * @package OA_Elections
	 */
	public function candidate() {
		$post_title = sanitize_text_field( $this->post['_oa_candidate_fname'] . ' ' . $this->post['_oa_candidate_lname'] );
		$post_name  = intval( $this->post['_oa_candidate_bsa_id'] );
		$this->new_or_update( 'oae_election', $post_title, $post_name );
		$cmb        = cmb2_get_metabox( $this->metabox, $this->post_id );
		if ( ! isset( $_POST[ $cmb->nonce() ] ) || ! wp_verify_nonce( $_POST[ $cmb->nonce() ], $cmb->nonce() ) ) {
			return $cmb->prop( 'submission_error', wp_die( 'security_fail', esc_html( 'Security check failed.' ) ) );
		}
		$this->update_meta( $cmb );

		/*
		 * Redirect back to the form page with a query variable with the new post ID.
		 * This will help double-submissions with browser refreshes
		 */
		$args = array(
			'p' => $post_id,
			'update' => true,
		);

		wp_safe_redirect( esc_url_raw( add_query_arg( $args ) ) );
		exit;
	}
}
