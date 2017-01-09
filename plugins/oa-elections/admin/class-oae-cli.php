<?php

if ( defined( 'WP_CLI' ) && WP_CLI ) {

	class OAE_CLI {

		function notify_unit( $args, $assoc_args ) {
			$post_id = $assoc_args['id'];

			if ( $post_id ) {
				OAE_Notifications::new_election_notification_unit( $post_id );
				$title = get_the_title( $post_id );
				WP_CLI::success( 'Unit notifications sent for ' . $title );
			} else {
				WP_CLI::error( 'You must supply an election ID' );
			}
		}

		function notify_chapter( $args, $assoc_args ) {
			$post_id = $assoc_args['id'];

			if ( $post_id ) {
				OAE_Notifications::new_election_notification_chapter( $post_id );
				$title = get_the_title( $post_id );
				WP_CLI::success( 'Chapter notifications sent for ' . $title );
			} else {
				WP_CLI::error( 'You must supply an election ID' );
			}
		}

		function notify_slack( $args, $assoc_args ) {
			$post_id = $assoc_args['id'];

			if ( $post_id ) {
				OAE_Notifications::new_election_slack( $post_id );
				$title = get_the_title( $post_id );
				WP_CLI::success( 'Slack notifications sent for ' . $title );
			} else {
				WP_CLI::error( 'You must supply an election ID' );
			}
		}
	}

	WP_CLI::add_command( 'elections', 'OAE_CLI' );
}
