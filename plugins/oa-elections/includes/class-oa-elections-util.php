<?php
class OA_Elections_Util {

	function __construct() {

	}

	static function get_status( $post_id ) {
		$term = current( get_the_terms( get_the_id(), 'oa_election_status' ) );
		return $term->name;
	}

	static function get_chapter( $post_id ) {
		$term = current( get_the_terms( get_the_id(), 'oa_chapter' ) );
		return $term->name;
	}
}
