<?php
class OAE_Util {

	function __construct() {

	}

	static function get_status( $post_id = null ) {
		if ( null === $post_id ) {
			$post_id = get_the_id();
		}

		$terms = get_the_terms( $post_id, 'oae_status' );
		if ( ! $terms ) {
			return 'No status defined';
		}
		$term = current( $terms );
		return $term->name;
	}

	static function get_cand_status( $post_id = null ) {
		if ( null === $post_id ) {
			$post_id = get_the_id();
		}
		$terms = get_the_terms( $post_id, 'oae_cand_status' );
		if ( ! $terms ) {
			return 'No status defined';
		}
		$term = current( $terms );
		return $term->name;
	}

	static function get_nom_status( $post_id = null ) {
		if ( null === $post_id ) {
			$post_id = get_the_id();
		}
		$terms = get_the_terms( $post_id, 'oae_nom_status' );
		if ( ! $terms ) {
			return 'No status defined';
		}
		$term = current( $terms );
		return $term->name;
	}

	static function get_chapter( $post_id = null ) {
		if ( null === $post_id ) {
			$post_id = get_the_id();
		}
		$terms = get_the_terms( $post_id, 'oae_chapter' );
		if ( ! $terms ) {
			return 'No chapter defined';
		}
		$term = current( $terms );
		return $term->name;
	}

	static function get_chapter_term( $post_id = null ) {
		if ( null === $post_id ) {
			$post_id = get_the_id();
		}
		$terms = get_the_terms( $post_id, 'oae_chapter' );
		if ( ! $terms ) {
			return 'No chapter defined';
		}
		$term = current( $terms );
		return $term;
	}

	static function get_user_chapter( $user_id ) {
		$chapter = get_user_meta( $user_id, '_oa_election_user_chapter', true );
		return $chapter;
	}

	static function user_election_rights() {
		if ( current_user_can( 'chapter_admin' ) || current_user_can( 'administrator' ) ) {
			return true;
		}
		return false;
	}

	static function chapter_name_from_slug( $string ) {
		$chapter = str_replace( '-', ' ', $string );
		return ucwords( $chapter );
	}

	static function candidate_count( $post_id = null ) {
		if ( null === $post_id ) {
			$post_id = get_the_id();
		}
		$candidates = OAE_Fields::get( 'candidates' );
		if ( $candidates ) {
			$count = count( $candidates );
		} else {
			$count = 0;
		}
		return $count;
	}

	static function elected_candidate_count( $post_id = null ) {
		$count = 0;
		if ( null === $post_id ) {
			$post_id = get_the_id();
		}
		$candidates = OAE_Fields::get( 'candidates' );
		if ( ! is_array( $candidates ) ) {
			return $count;
		}
		$candidates = implode( array_map( 'intval', $candidates ), ',' );

		global $wpdb;
		$query = $wpdb->prepare(
			"SELECT count(*) as elected_count
			FROM wp_terms t
			LEFT JOIN wp_term_taxonomy tt ON tt.term_id = t.term_id
			LEFT JOIN wp_term_relationships tr ON tr.term_taxonomy_id = tt.term_taxonomy_id
			WHERE t.slug = 'elected'
			AND tt.taxonomy = 'oae_cand_status'
			AND tr.object_id IN ( {$candidates} );",
			$candidates
		);
		$results = $wpdb->get_results( $query );
		$count = current( $results )->elected_count;
		return $count;
	}
}
