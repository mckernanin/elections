<?php
$args = array(
	'post_type' => 'oa_election',
);
$elections = new WP_Query( $args );
?>
<table>
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
	</tr>
	<?php while ( $elections->have_posts() ) {
		$elections->the_post(); ?>
		<tr>
			<td>
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</td>
			<td>
				<?php echo OA_Elections_Util::get_chapter( get_the_id() ); ?>
			</td>
			<td>
				<?php echo OA_Elections_Util::get_status( get_the_id() ); ?>
			</td>
		</tr>
	<?php } ?>
</table>
