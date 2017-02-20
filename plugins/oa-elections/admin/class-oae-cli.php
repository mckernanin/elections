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

		function link_authors( $args, $assoc_args ) {
			$post_args = [
				'post_type'      => 'oae_election',
				'posts_per_page' => 500,
			];
			$elections = new WP_Query( $post_args );
			$progress = \WP_CLI\Utils\make_progress_bar( 'Linking authors', $elections->post_count );
			while ( $elections->have_posts() ) {
				$elections->the_post();
				$leader_email = OAE_Fields::get( '_oa_election_leader_email' );
				$user = get_user_by( 'email', $leader_email );
				if ( is_object( $user ) ) {
					wp_update_post([
						'post_id'     => get_the_id(),
						'post_author' => $user->ID,
					]);
				}
				$progress->tick();
			}
			$progress->finish();
		}

		function fix_roles() {
			$user_args = [
				'role__not_in' => [ 'administrator', 'chapter-admin', 'election-team', 'unit-leader' ],
			];
			$users = new WP_User_Query( $user_args );
			$progress = \WP_CLI\Utils\make_progress_bar( 'Fixing roles', count( $users->results ) );
			foreach ( $users->results as $userdata ) {
				$user = new WP_User( $userdata->data->ID );
				$user->set_role( 'unit-leader' );
				$progress->tick();
			}
			$progress->finish();
		}
	}

	WP_CLI::add_command( 'elections', 'OAE_CLI' );
} // End if().
