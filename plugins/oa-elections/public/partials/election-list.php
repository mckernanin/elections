<?php
if ( ! is_user_logged_in() ) {
	echo 'You must be <a href="/wp-admin/">logged in</a> to view this page.';
} else {

	$args = array(
		'post_type' => 'oae_election',
		'posts_per_page' => 100,
	);
	if ( current_user_can( 'chapter-admin' ) ) {
		$chapter_id = current( get_user_meta( get_current_user_id(), '_oa_election_user_chapter' ) );
		$chapter = get_term( $chapter_id );
		$args['oae_chapter'] = $chapter->slug;
	}
	$elections = new WP_Query( $args );
	if ( $elections->have_posts() ) {
	?>
	<table id="election-list">
		<thead>
			<tr>
				<th>
					Unit
				</th>
				<th>
					Chapter
				</th>
				<th>
					Election Status
				</th>
				<th>
					Candidates
				</th>
				<th>
					Elected
				</th>
			</tr>
		</thead>
		<tbody>
			<?php while ( $elections->have_posts() ) {
				$elections->the_post(); ?>
				<tr>
					<td>
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</td>
					<td>
						<?php echo esc_html( OAE_Util::get_chapter( get_the_id() ) ); ?>
					</td>
					<td>
						<?php echo esc_html( OAE_Util::get_status( get_the_id() ) ); ?>
					</td>
					<td>
						<?php echo esc_html( OAE_Util::candidate_count( get_the_id() ) ); ?>
					</td>
					<td>
						<?php echo esc_html( OAE_Util::elected_candidate_count( get_the_id() ) ); ?>
					</td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
<?php
	} else {
		echo 'There are currently no elections for ' . esc_html( $chapter->name );
	}
}
