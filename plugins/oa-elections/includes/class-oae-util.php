<?php
class OAE_Util {

	function __construct() {

	}

	static function get_status( $post_id ) {
		$term = current( get_the_terms( get_the_id(), 'oae_status' ) );
		return $term->name;
	}

	static function get_cand_status( $post_id ) {
		$term = current( get_the_terms( $post_id, 'oae_cand_status' ) );
		return $term->name;
	}

	static function get_chapter( $post_id ) {
		$term = current( get_the_terms( get_the_id(), 'oae_chapter' ) );
		return $term->name;
	}
}
