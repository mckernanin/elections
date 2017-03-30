<?php
if ( ! is_user_logged_in() ) {
	echo 'You must be <a href="/wp-admin/">logged in</a> to view this page.';
} else {

	$args = array(
		'post_type'      => 'oae_nomination',
		'posts_per_page' => 100,
		'oae_nom_status' => 'submitted',
	);
	$nominations = new WP_Query( $args );
	if ( $nominations->have_posts() ) {
	?>
	<table id="adult-nominations">
		<thead>
			<tr>
				<th>
					Name
				</th>
				<th>
					BSA ID
				</th>
				<th colspan="2">
					District / Chapter
				</th>
				<th colspan="3">
					Status
				</th>
			</tr>
		</thead>
		<tbody>
			<?php while ( $nominations->have_posts() ) {
				$nominations->the_post();
			?>
				<tr>
					<td>
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</td>
					<td>
						<?php echo esc_html( OAE_Fields::nomination_get( 'bsa_id' ) ); ?>
					</td>
					<td colspan="2">
						<?php echo esc_html( OAE_Util::get_chapter() ); ?>
					</td>
					<td colspan="3">
						<button class="status-button" data-id="<?php the_id(); ?>" data-status="approved">Approved</button>
						<button class="status-button" data-id="<?php the_id(); ?>" data-status="non-member">Non-member</button>
						<button class="status-button" data-id="<?php the_id(); ?>" data-status="council-issue">Council Issue</button>
					</td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
<?php
	} else {
		echo 'There are not currently any nominations requiring approval.';
	} // End if().
} // End if().
