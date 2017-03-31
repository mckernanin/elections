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

		function notify_unit_scheduled( $args, $assoc_args ) {
			$post_id = $assoc_args['id'];

			if ( $post_id ) {
				if ( has_term( 'scheduled', 'oae_status',  $post_id ) ) {
					OAE_Notifications::election_scheduled_unit( $post_id );
					$title = get_the_title( $post_id );
					WP_CLI::success( 'Unit notifications sent for ' . $title );
				} else {
					WP_CLI::error( 'This election has not been scheduled yet.' );
				}
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

		public function set_candidate_chapters() {
			$post_args = [
				'post_type'      => 'oae_election',
				'posts_per_page' => 500,
			];
			$elections = new WP_Query( $post_args );
			$progress = \WP_CLI\Utils\make_progress_bar( 'Setting candidate chapters', $elections->post_count );
			while ( $elections->have_posts() ) {
				$elections->the_post();
				$chapter = OAE_Util::get_chapter_term()->term_id;
				$candidates = OAE_Fields::get( 'candidates' );
				if ( $candidates ) {
					foreach ( $candidates as $candidate ) {
						wp_set_object_terms( $candidate, $chapter, 'oae_chapter' );
					}
				}
				$progress->tick();
			}
			$progress->finish();
		}

		public function admin_mg_list() {
			$query_args = [
				'role' => 'chapter-admin',
			];

			$users = new WP_User_Query( $query_args );
			$progress = \WP_CLI\Utils\make_progress_bar( 'Syncing mailing list', count( $users->results ) );
			foreach ( $users->results as $user ) {
				$response = wp_remote_post( 'https://api.mailgun.net/v3/lists/electionadmins@tahosalodge.org/members', array(
					'headers' => array(
						'Authorization' => MG_BASIC_AUTH,
						'Content-Type'  => 'application/x-www-form-urlencoded; charset=utf-8',
					),
					'body' => array(
						'subscribed' => 'True',
						'address'    => $user->data->user_email,
				 	),
				) );
				$progress->tick();
				if ( is_wp_error( $response ) ) {
					// There was an error making the request
					$error_message = $response->get_error_message();
					die( esc_html( $error_message ) );
				}
			}
			$progress->finish();
		}

		public function unit_mg_list() {
			$query_args = [
				'role' => 'unit-leader',
			];

			$users = new WP_User_Query( $query_args );
			$progress = \WP_CLI\Utils\make_progress_bar( 'Syncing mailing list', count( $users->results ) );
			foreach ( $users->results as $user ) {
				$response = wp_remote_post( 'https://api.mailgun.net/v3/lists/electionunitleaders@tahosalodge.org/members', array(
					'headers' => array(
						'Authorization' => MG_BASIC_AUTH,
						'Content-Type'  => 'application/x-www-form-urlencoded; charset=utf-8',
					),
					'body' => array(
						'subscribed' => 'True',
						'address'    => $user->data->user_email,
					),
				) );
				$progress->tick();
				if ( is_wp_error( $response ) ) {
					// There was an error making the request
					$error_message = $response->get_error_message();
					die( esc_html( $error_message ) );
				}
			}
			$progress->finish();
		}

		public function cand_id_type_fix() {
			$post_args = [
				'post_type'      => 'oae_election',
				'posts_per_page' => 500,
			];
			$elections = new WP_Query( $post_args );
			$progress = \WP_CLI\Utils\make_progress_bar( 'Convertings IDs to integers', $elections->post_count );
			while ( $elections->have_posts() ) {
				$elections->the_post();
				$candidates = OAE_Fields::get( 'candidates' );

				$candidate_ints = [];
				if ( $candidates ) {
					foreach ( $candidates as $candidate ) {
						$candidate_ints[] = absint( $candidate );
					}
					OAE_Fields::update( 'candidates', $candidate_ints );
				}

				$progress->tick();
			}
			$progress->finish();
		}

		public function elect_candidate( $args ) {
			foreach ( $args as $key => $value ) {
				if ( is_numeric( $value ) ) {
					wp_set_object_terms( $value, 'elected', 'oae_cand_status' );
					$candidate_name = get_the_title( $value );
					WP_CLI::success( $candidate_name . ' has been elected.' );
				}
			}
		}
	}

	WP_CLI::add_command( 'elections', 'OAE_CLI' );
} // End if().
