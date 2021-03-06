<?php
class OAE_Notifications {

	function __construct() {
		add_action( 'election_save', [ $this, 'new_election_notification_unit' ] );
		add_action( 'election_save', [ $this, 'new_election_notification_chapter' ] );
		add_action( 'election_save', [ $this, 'new_election_slack' ] );
		add_action( 'election_schedule', [ $this, 'election_scheduled_unit' ] );
		add_action( 'election_results_submitted', [ $this, 'election_results_submitted_chapter' ] );
		add_action( 'election_results_submitted', [ $this, 'election_results_submitted_slack' ] );
		add_action( 'nomination_save', [ $this, 'nomination_approved' ] );
	}

	/**
	* New election notification function.
	*
	* @param $post_id
	*/
	static function new_election_notification_unit( $post_id ) {
		// If this is just a revision, don't send the email.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		if ( 'production' !== WP_ENV ) {
			return;
		}

		$fields = get_post_custom( $post_id );

		if ( ! is_array( $fields['_oa_election_leader_email'] ) ) {
			return;
		}

		$post_title   = get_the_title( $post_id );
		$post_url     = get_permalink( $post_id );
		$subject      = 'Election Submitted for ' . $post_title;
		$unit_fname   = current( $fields['_oa_election_leader_fname'] );
		$unit_lname   = current( $fields['_oa_election_leader_lname'] );
		$unit_num     = current( $fields['_oa_election_unit_number'] );
		$unit_type    = current( $fields['_oa_election_unit_type'] );
		$date_1       = current( $fields['_oa_election_unit_date_1'] );
		$date_2       = current( $fields['_oa_election_unit_date_2'] );
		$date_3       = current( $fields['_oa_election_unit_date_3'] );
		$leader_email = current( $fields['_oa_election_leader_email'] );
		$chapter      = OAE_Util::get_chapter( $post_id );
		$pass         = $unit_num . substr( $chapter, 0, 1 ) . $unit_lname . '383';
		$election     = get_post( $post_id );
		wp_set_password( $pass, $election->post_author );

		ob_start();

		include( 'emails/new-election-unit.php' );

		$message = ob_get_clean();

		// Send email to author.
		$mail = wp_mail( $leader_email, $subject, $message );

		$copied_message    = 'You were copied on this message by ' . get_the_author( $post_id ) . '<br />' . $message;
		$copied_recipients = get_post_meta( $post_id, '_oa_election_leader_copied_emails', true );

		if ( $copied_recipients ) {
			foreach ( $copied_recipients as $email ) {
				$mail = wp_mail( $email, $subject, $copied_message );
			}
		}
	}

	static function new_election_notification_chapter( $post_id ) {
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		if ( 'production' !== WP_ENV ) {
			return;
		}

		$fields = get_post_custom( $post_id );

		if ( ! is_array( $fields['_oa_election_leader_email'] ) ) {
			return;
		}

		$post_title   = get_the_title( $post_id );
		$post_url     = get_permalink( $post_id );
		$subject      = 'Election Requested for ' . $post_title;
		$unit_fname   = current( $fields['_oa_election_leader_fname'] );
		$unit_lname   = current( $fields['_oa_election_leader_lname'] );
		$unit_num     = current( $fields['_oa_election_unit_number'] );
		$unit_type    = current( $fields['_oa_election_unit_type'] );
		$date_1       = current( $fields['_oa_election_unit_date_1'] );
		$date_2       = current( $fields['_oa_election_unit_date_2'] );
		$date_3       = current( $fields['_oa_election_unit_date_3'] );
		$leader_email = current( $fields['_oa_election_leader_email'] );
		$chapter      = OAE_Util::get_chapter_term( $post_id );

		$user_args = [
			'role'       => 'chapter-admin',
			'meta_key'   => '_oa_election_user_chapter',
			'meta_value' => $chapter->term_id,
		];

		$chapter_admins = new WP_User_Query( $user_args );

		foreach ( $chapter_admins->results as $chapter_admin ) {
			ob_start();
			include( 'emails/new-election-chapter.php' );

			$message = ob_get_clean();
			$mail = wp_mail( $chapter_admin->data->user_email, $subject, $message );
		}
	}

	static function new_election_slack( $post_id ) {
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		$fields = get_post_custom( $post_id );

		if ( ! is_array( $fields['_oa_election_leader_email'] ) ) {
			return;
		}

		$post_title   = get_the_title( $post_id );
		$post_url     = get_permalink( $post_id );
		$subject      = 'Election Submitted for ' . $post_title;
		$unit_fname   = current( $fields['_oa_election_leader_fname'] );
		$unit_lname   = current( $fields['_oa_election_leader_lname'] );
		$unit_num     = current( $fields['_oa_election_unit_number'] );
		$unit_type    = current( $fields['_oa_election_unit_type'] );
		$date_1       = current( $fields['_oa_election_unit_date_1'] );
		$date_2       = current( $fields['_oa_election_unit_date_2'] );
		$date_3       = current( $fields['_oa_election_unit_date_3'] );
		$leader_email = current( $fields['_oa_election_leader_email'] );
		$chapter      = OAE_Util::get_chapter( $post_id );
		$pass         = $unit_num . substr( $chapter, 0, 1 ) . $unit_lname . '383';

		// Slack webhook endpoint from Slack settings
		$slack_endpoint = OAE_SLACK_WEBHOOK;
		$payload = array(
			'text'        => 'New Election Requested',
			'attachments' => array(
				array(
					'fallback' => 'fallback',
					'color'    => 'good',
					'fields'   => array(
						array(
							'title' => 'Unit',
							'value' => $unit_num,
						),
						array(
							'title' => 'Chapter',
							'value' => $chapter,
						),
						array(
							'title' => 'Dates',
							'value' => $date_1 . ', ' . $date_2 . ', ' . $date_3,
						),
					),
				),
			),
			'username'   => 'Election Bot',
			'icon_emoji' => ':bow_and_arrow:',
		);
		// Prepare the data / payload to be posted to Slack
		$slack_data = array(
			'payload'   => wp_json_encode( $payload ),
		);
		// Post our data via the slack webhook endpoint using wp_remote_post
		$posting_to_slack = wp_remote_post( $slack_endpoint, array(
			'method'      => 'POST',
			'timeout'     => 30,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => array(),
			'body'        => $slack_data,
			'cookies'     => array(),
			)
		);
	}

	static function election_scheduled_unit( $post_id ) {
		$fields = get_post_custom( $post_id );

		if ( 'production' !== WP_ENV ) {
			return;
		}

		if ( ! is_array( $fields['_oa_election_leader_email'] ) ) {
			return;
		}

		$post_title   = get_the_title( $post_id );
		$post_url     = get_permalink( $post_id );
		$subject      = 'Election Date Confirmed For for ' . $post_title;
		$unit_fname   = current( $fields['_oa_election_leader_fname'] );
		$unit_lname   = current( $fields['_oa_election_leader_lname'] );
		$unit_num     = current( $fields['_oa_election_unit_number'] );
		$unit_type    = current( $fields['_oa_election_unit_type'] );
		$leader_email = current( $fields['_oa_election_leader_email'] );
		$confirm_date = current( $fields['_oa_election_selected_date'] );
		$meeting_time = current( $fields['_oa_election_unit_meeting_time'] );
		$chapter      = OAE_Util::get_chapter( $post_id );

		ob_start();

		include( 'emails/election-scheduled-unit.php' );

		$message = ob_get_clean();

		// Send email to author.
		$mail = wp_mail( $leader_email, $subject, $message );

		$copied_message    = 'You were copied on this message by ' . get_the_author( $post_id ) . '<br />' . $message;
		$copied_recipients = get_post_meta( $post_id, '_oa_election_leader_copied_emails', true );

		if ( $copied_recipients ) {
			foreach ( $copied_recipients as $email ) {
				$mail = wp_mail( $email, $subject, $copied_message );
			}
		}
	}

	static function election_results_submitted_chapter( $post_id ) {
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		if ( 'production' !== WP_ENV ) {
			return;
		}

		$fields = get_post_custom( $post_id );

		if ( ! is_array( $fields['_oa_election_leader_email'] ) ) {
			return;
		}

		$post_title   = get_the_title( $post_id );
		$post_url     = get_permalink( $post_id );
		$subject      = 'Election Results Submitted for ' . $post_title;
		$unit_fname   = current( $fields['_oa_election_leader_fname'] );
		$unit_lname   = current( $fields['_oa_election_leader_lname'] );
		$unit_num     = current( $fields['_oa_election_unit_number'] );
		$unit_type    = current( $fields['_oa_election_unit_type'] );
		$chapter      = OAE_Util::get_chapter_term( $post_id );

		$user_args = [
			'role'       => 'chapter-admin',
			'meta_key'   => '_oa_election_user_chapter',
			'meta_value' => $chapter->term_id,
		];

		$chapter_admins = new WP_User_Query( $user_args );

		foreach ( $chapter_admins->results as $chapter_admin ) {
			ob_start();
			include( 'emails/election-results-chapter.php' );

			$message = ob_get_clean();
			$mail = wp_mail( $chapter_admin->data->user_email, $subject, $message );
		}
	}

	static function election_results_submitted_slack( $post_id ) {
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		if ( 'production' !== WP_ENV ) {
			return;
		}

		$fields = get_post_custom( $post_id );

		if ( ! is_array( $fields['_oa_election_leader_email'] ) ) {
			return;
		}

		$post_title   = get_the_title( $post_id );
		$post_url     = get_permalink( $post_id );
		$subject      = 'Election Submitted for ' . $post_title;
		$unit_fname   = current( $fields['_oa_election_leader_fname'] );
		$unit_lname   = current( $fields['_oa_election_leader_lname'] );
		$unit_num     = current( $fields['_oa_election_unit_number'] );
		$unit_type    = current( $fields['_oa_election_unit_type'] );
		$date_1       = current( $fields['_oa_election_unit_date_1'] );
		$date_2       = current( $fields['_oa_election_unit_date_2'] );
		$date_3       = current( $fields['_oa_election_unit_date_3'] );
		$leader_email = current( $fields['_oa_election_leader_email'] );
		$chapter      = OAE_Util::get_chapter( $post_id );
		$pass         = $unit_num . substr( $chapter, 0, 1 ) . $unit_lname . '383';

		// Slack webhook endpoint from Slack settings
		$slack_endpoint = OAE_SLACK_WEBHOOK;
		$payload = array(
			'text'        => 'Election Results Entered',
			'attachments' => array(
				array(
					'fallback' => 'fallback',
					'color'    => 'good',
					'fields'   => array(
						array(
							'title' => 'Unit',
							'value' => $unit_num,
						),
						array(
							'title' => 'Chapter',
							'value' => $chapter,
						),
						array(
							'title' => 'Elected Candidates',
							'value' => OAE_Util::elected_candidate_count( $post_id ),
						),
					),
				),
			),
			'username'   => 'Election Bot',
			'icon_emoji' => ':bow_and_arrow:',
		);
		// Prepare the data / payload to be posted to Slack
		$slack_data = array(
			'payload'   => wp_json_encode( $payload ),
		);
		// Post our data via the slack webhook endpoint using wp_remote_post
		$posting_to_slack = wp_remote_post( $slack_endpoint, array(
			'method'      => 'POST',
			'timeout'     => 30,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => array(),
			'body'        => $slack_data,
			'cookies'     => array(),
			)
		);
	}

	/**
	* Triggered send when a nomination is marked as approved.
	* Email goes to nominee.
	*/
	static function nomination_approved( $post_id ) {
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		if ( 'production' !== WP_ENV ) {
			return;
		}

		if ( 'approved' !== OAE_Util::get_nom_status( $post_id ) ) {
			return;
		}

		$fields = get_post_custom( $post_id );

		if ( ! is_array( $fields['_oa_nomination_email'] ) ) {
			return;
		}
		$post_title   = get_the_title( $post_id );
		$post_url     = get_permalink( $post_id );
		$chapter      = OAE_Util::get_chapter_term( $post_id );

		// Email to Nominee
		$subject      = "Congratulations ${post_title}, you have been elected into the Order of the Arrow!";
		ob_start();

		include( 'emails/nomination/approval-candidate.php' );

		$message = ob_get_clean();

		$mail = wp_mail( $fields['_oa_nomination_email'], $subject, $message );

		// Email to chapter & unit.
		$user_args = [
			'role'       => 'chapter-admin',
			'meta_key'   => '_oa_election_user_chapter',
			'meta_value' => $chapter->term_id,
		];

		$chapter_admins = new WP_User_Query( $user_args );
		$copied_recipients = get_post_meta( $election_id, '_oa_election_leader_copied_emails', true );
		$unit_leader = get_post_meta( $election_id, '_oa_election_leader_email', true );
		$emails = [ $unit_leader ];
		$emails = array_merge( $chapter_admins->results, $copied_recipients, $emails );
		$subject = "Adult nomination for ${post_title} has been approved";

		ob_start();

		include( 'emails/nomination/approval-unit.php' );

		$message = ob_get_clean();

		foreach ( $emails as $email ) {
			$mail = wp_mail( $email, $subject, $message );
		}
	}

	/**
	* Triggered send when a nomination is marked as having a membership issue.
	* Emails go to all unit leaders, and chapter admins.
	*/
	static function nomination_membership_issue( $post_id ) {
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		if ( 'production' !== WP_ENV ) {
			return;
		}

		if ( 'non-member' !== OAE_Util::get_nom_status( $post_id ) ) {
			return;
		}

		$fields = get_post_custom( $post_id );

		$post_title = get_the_title( $post_id );
		$subject    = "OA Elections - Council Registration issue with ${post_title}";
		$chapter    = OAE_Util::get_chapter_term( $post_id );
		$election_id = OAE_Util::nomination_get( 'election_id' );

		ob_start();

		include( 'emails/nomination/registration-issue.php' );

		$message = ob_get_clean();

		$user_args = [
			'role'       => 'chapter-admin',
			'meta_key'   => '_oa_election_user_chapter',
			'meta_value' => $chapter->term_id,
		];

		$chapter_admins = new WP_User_Query( $user_args );
		$copied_recipients = get_post_meta( $election_id, '_oa_election_leader_copied_emails', true );
		$unit_leader = get_post_meta( $election_id, '_oa_election_leader_email', true );
		$emails = [ $unit_leader ];
		$emails = array_merge( $chapter_admins->results, $copied_recipients, $emails );

		foreach ( $emails as $email ) {
			$mail = wp_mail( $email, $subject, $message );
		}
	}

	/**
	* Triggered send when a nomination is marked as approved.
	* Email goes to nominee.
	*/
	static function nomination_council_issue( $post_id ) {
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		if ( 'production' !== WP_ENV ) {
			return;
		}

		if ( 'approved' !== OAE_Util::get_nom_status( $post_id ) ) {
			return;
		}

		$fields = get_post_custom( $post_id );

		if ( ! is_array( $fields['_oa_nomination_email'] ) ) {
			return;
		}
		$post_title   = get_the_title( $post_id );
		$post_url     = get_permalink( $post_id );
		$chapter      = OAE_Util::get_chapter_term( $post_id );
		$subject      = 'Status up date on your Order of the Arrow Nomination';
		ob_start();

		include( 'emails/nomination/council-issue-nominee.php' );

		$message = ob_get_clean();

		$mail = wp_mail( $fields['_oa_nomination_email'], $subject, $message );

		$subject = "Congratulations ${post_title}, you have been elected into the Order of the Arrow";
		ob_start();

		include( 'emails/nomination/council-issue-admin.php' );

		$message = ob_get_clean();

		// Send email to nominee.
		$mail = wp_mail( $fields['_oa_nomination_email'], $subject, $message );
	}

}

new OAE_Notifications();
