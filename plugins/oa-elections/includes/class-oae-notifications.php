<?php
class OAE_Notifications {

	function __construct() {
		add_action( 'init', function() {
			// do_action( 'election_create', 164 );
		});
		add_action( 'wp_async_election_create', array( $this, 'new_election_notification_unit' ) );
		add_action( 'wp_async_election_create', array( $this, 'new_election_notification_chapter' ) );
		add_action( 'wp_async_election_create', array( $this, 'new_election_slack' ) );

		include( 'lib/class-wp-async-task.php' );
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

		$fields = get_post_custom( $post_id );

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

		$fields = get_post_custom( $post_id );

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
		$slack_endpoint = 'https://hooks.slack.com/services/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';
		// Prepare the data / payload to be posted to Slack
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
}

new OAE_Notifications();

class OAE_Election_Create_Notifications extends WP_Async_Task {

	protected $action = 'election_create';

	/**
	 * Prepare data for the asynchronous request
	 *
	 * @throws Exception If for any reason the request should not happen
	 *
	 * @param array $data An array of data sent to the hook
	 *
	 * @return array
	 */
	protected function prepare_data( $data ) {
		return array( 'post_id' => $data[0] );
	}

	/**
	 * Run the async task action
	 */
	protected function run_action() {
		do_action( "wp_async_$this->action", $_POST['post_id'] );
		// error_log( 'async test', 0 );
	}
}

add_action( 'plugins_loaded', 'notify_loader' );

function notify_loader() {
	error_log( '===================================' );
	$class = new OAE_Election_Create_Notifications();
	// var_dump( $class );
	do_action( 'wp_async_election_create', 1221 );
	error_log( 'async test', 0 );
	// wp_die( var_dump( $class ) );

}
