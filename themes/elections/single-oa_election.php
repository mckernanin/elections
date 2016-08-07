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
		if ( 'candidates' === $section ) {
			echo do_shortcode( '[candidate-entry]' );
		} else if ( 'election-details' === $section ) {
			echo do_shortcode( '[unit-edit-form]' );
		} else {
		?>
			A short overview of the election will appear here, with actions. Currently, you can <a href="election-details">edit an election</a> or <a href="candidates">add candidates.</a><br />
			Election status: <?php echo OA_Elections_Util::get_status( get_the_id() ); ?>
		<?php
		}
			// End of the loop.
		endwhile;
		?>

	</main><!-- .site-main -->

</div><!-- .content-area -->
<?php get_footer(); ?>
