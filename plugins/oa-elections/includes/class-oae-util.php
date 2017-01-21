<?php
class OAE_Util {

	function __construct() {

	}

	static function get_status( $post_id = null ) {
		if ( null === $post_id ) {
			$post_id = get_the_id();
		}
		$term = current( get_the_terms( $post_id, 'oae_status' ) );
		return $term->name;
	}

	static function get_cand_status( $post_id = null ) {
		if ( null === $post_id ) {
			$post_id = get_the_id();
		}
		$term = current( get_the_terms( $post_id, 'oae_cand_status' ) );
		return $term->name;
	}

	static function get_chapter( $post_id = null ) {
		if ( null === $post_id ) {
			$post_id = get_the_id();
		}
		$term = current( get_the_terms( $post_id, 'oae_chapter' ) );
		return $term->name;
	}

	static function get_chapter_term( $post_id = null ) {
		if ( null === $post_id ) {
			$post_id = get_the_id();
		}
		$term = current( get_the_terms( $post_id, 'oae_chapter' ) );
		return $term;
	}

	static function get_user_chapter( $user_id ) {
		$chapter = get_user_meta( $user_id, '_oa_election_user_chapter', true );
		return $term;
	}
}
