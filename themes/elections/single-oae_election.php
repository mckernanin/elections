<?php
/**
 * The template for displaying pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other "pages" on your WordPress site will use a different template.
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */

$section = get_query_var( 'editing_section' );
get_header(); ?>

<div id="primary" class="content-area" style="width: 100%;">
	<main id="main" class="site-main" role="main">
	<?php
	// Start the loop.
	while ( have_posts() ) : the_post(); ?>
		<h1><?php the_title(); ?></h1>
		<?php
		if ( current_user_can( 'chapter-admin' ) || current_user_can( 'administrator' ) ) { ?>
		<ul class="single-election-nav">
			<li><strong>Actions:</strong></li>
			<li><a href="<?php the_permalink(); ?>">Election Home</a></li>
			<li><a href="<?php the_permalink(); ?>/edit-election">Edit Unit Information</a></li>
			<li><a href="<?php the_permalink(); ?>/add-candidate">Add Candidates</a></li>
			<li><a href="<?php the_permalink(); ?>/add-nomination">Add Adult Nomination</a></li>
			<li><a href="<?php the_permalink(); ?>/chapter-edit-election">Team & Camp Promotion</a></li>
			<li><a href="<?php the_permalink(); ?>/report">Election report</a></li>
			<li><a href="<?php the_permalink(); ?>/ballots">Print ballots</a></li>
		</ul>
		<?php
		} elseif ( current_user_can( 'unit-leader' ) ) {
		?>
		<ul class="single-election-nav">
			<li><strong>Actions:</strong></li>
			<li><a href="<?php the_permalink(); ?>">Election Home</a></li>
			<li><a href="<?php the_permalink(); ?>/add-candidate">Add Candidates</a></li>
			<li><a href="<?php the_permalink(); ?>/add-nomination">Add Adult Nomination</a></li>
			<li><a href="<?php the_permalink(); ?>/ballots">Print ballots</a></li>
		</ul>
		<?php
		} // End if().
		if ( 'add-candidate' === $section ) {
			echo do_shortcode( '[candidate-entry]' );
		} elseif ( 'bulk-add' === $section ) {
			echo do_shortcode( '[candidate-entry]' );
		} elseif ( 'edit-election' === $section ) {
			echo do_shortcode( '[unit-edit-form]' );
		} elseif ( 'chapter-edit-election' === $section ) {
			echo do_shortcode( '[unit-edit-form-chapter]' );
		} elseif ( 'report' === $section ) {
			echo do_shortcode( '[election-report]' );
		} elseif ( 'ballots' === $section ) {
			echo do_shortcode( '[ballots]' );
		} elseif ( 'add-nomination' === $section ) {
			echo do_shortcode( '[nominate-adult]' );
		} else {
			$candidates = OAE_Fields::get( 'candidates' );
			$nominations = OAE_Fields::get( 'nominations' );
		?>
			<table>
				<tr>
					<td>Election Status:</td>
					<td><?php echo esc_html( OAE_Util::get_status( get_the_id() ) ); ?></td>
				</tr>
				<tr>
					<td>
						Election Date:
					</td>
					<td>
						<?php echo esc_html( OAE_Fields::get( 'selected_date' ) ); ?>
					</td>
				</tr>
			</table>
			<?php
			if ( $candidates ) { ?>
				<table>
					<tr>
						<th>
							Candidate
						</th>
						<th>
							Status
						</th>
					</tr>
					<?php foreach ( $candidates as $candidate ) { ?>
						<tr>
							<td>
								<a href="<?php the_permalink( $candidate ); ?>"><?php echo get_the_title( $candidate ); ?></a>
							</td>
							<td>
								<?php echo esc_html( OAE_Util::get_cand_status( $candidate ) ); ?>
							</td>
						</tr>
					<?php } ?>
				</table>
				<a class="button" href="add-candidate">Add Candidate</a>
			<?php
			} else {
			?>
				<h4>This election does not currently have any candidates. <br>Before your election can take place, you must enter your candidates online.</h4>
				<a class="button" href="add-candidate">Add Candidate</a>
				<a class="button" href="/bulk-candidate-upload/">Bulk Upload Candidates</a>
			<?php
			}
			if ( $nominations ) { ?>
				<table>
					<tr>
						<th>
							Nominee
						</th>
						<th>
							Status
						</th>
					</tr>
					<?php foreach ( $nominations as $nomination ) { ?>
						<tr>
							<td>
								<a href="<?php the_permalink( $nomination ); ?>"><?php echo get_the_title( $nomination ); ?></a>
							</td>
							<td>
								<?php echo esc_html( OAE_Util::get_nom_status( $nomination ) ); ?>
							</td>
						</tr>
					<?php } ?>
				</table>
				<a class="button" href="add-nomination">Add Nomination</a>
			<?php
			} else {
			?>
				<br /><br />
				<h4>This election does not currently have any adult nominations. <br></h4>
				<a class="button" href="add-nomination">Add Nomination</a>
			<?php
			}
		} // End if().

			// End of the loop.
	endwhile;
	?>

	</main><!-- .site-main -->

</div><!-- .content-area -->
<?php get_footer(); ?>
