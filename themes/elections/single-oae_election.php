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
		if ( 'add-candidate' === $section ) {
			echo do_shortcode( '[candidate-entry]' );
		} elseif ( 'edit-election' === $section ) {
			echo do_shortcode( '[unit-edit-form]' );
		} elseif ( 'chapter-edit-election' === $section ) {
			echo do_shortcode( '[unit-edit-form-chapter]' );
		} elseif ( 'report' === $section ) {
			echo do_shortcode( '[election-report]' );
		} else {
			$candidates = OAE_Fields::get( 'candidates' );
		?>
		<p>
			A short overview of the election will appear here, with actions. Currently, you can <a href="edit-election">edit an election</a> or <a href="add-candidate">add candidates.</a><br />
		</p>
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
		<?php if ( $candidates ) { ?>
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
		<?php } else { ?>
			<h4>This election does not currently have any candidates. <br>Before your election can take place, you must enter your candidates online.</h4>
			<a class="button" href="add-candidate">Add Candidate</a>
		<?php }
		}
		// End of the loop.
	endwhile;
	?>

	</main><!-- .site-main -->

</div><!-- .content-area -->
<?php get_footer(); ?>
