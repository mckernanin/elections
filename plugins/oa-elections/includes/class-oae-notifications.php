<?php
class OAE_Notifications {

	function __construct() {

		 add_action( 'election_save', array( $this, 'new_election_notification' ) );
	}

	/**
	* New election notification function.
	*
	* @param $post_id
	*/
	static function new_election_notification( $post_id ) {
		// If this is just a revision, don't send the email.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		$fields = get_post_custom( $post_id );

		$post_title = get_the_title( $post_id );
		$post_url   = get_permalink( $post_id );
		$subject    = 'Election Submitted for ' . $post_title;

		ob_start();

		include( 'emails/new-election.php' );

		$message = ob_get_clean();

		// Send email to admin.
		$mail = wp_mail( $leader_email, $subject, $message );

		$copied_message    = 'You were copied on this message by ' . get_the_author( $post_id ) . '<br />' . $message;
		$copied_recipients = get_post_meta( $post_id, '_oa_election_leader_copied_emails', true );

		if ( $copied_recipients ) {
			foreach ( $copied_recipients as $email ) {
				$mail = wp_mail( $email, $subject, $copied_message );
			}
		}
	}
}

new OAE_Notifications();
