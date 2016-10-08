<?php
$candidates = OAE_Fields::get( 'candidates' );
?>
<div class="ballots">
	<h3>Tahosa Lodge Election Ballot</h3>
	<ul>
		<?php foreach ( $candidates as $candidate ) { ?>
			<li><input type="checkbox"><?php echo get_the_title( $candidate ); ?></li>
		<?php } ?>
	</ul>
</div>
