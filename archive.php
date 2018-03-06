<?php
/**
 * The template for displaying archive pages
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * If you'd like to further customize these archive views, you may create a
 * new template file for each one. For example, tag.php (Tag archives),
 * category.php (Category archives), author.php (Author archives), etc.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * 
 * @package Surely
 * @since Surely 1.0
 */

get_header(); ?>

	<?php surely_breadcrumbs(); ?>

	<?php $list_class = get_theme_mod( 'archive_blog_list_view', 'list') . '-view'; ?>
	<div id="primary" class="content-area <?php echo esc_attr($list_class); ?>">
		<main id="main" class="site-main">

		<?php if ( have_posts() ) : ?>

			<header class="page-header">
				<?php
				surely_archive_title();
				?>
			</header><!-- .page-header -->

			<?php
			// Start the Loop.
			while ( have_posts() ) : the_post();

				if ( 'classic' == get_theme_mod( 'archive_blog_list_view', 'list') )
					get_template_part( 'template-parts/content', get_post_format() );
				else
					get_template_part( 'template-parts/content-list', get_post_format() );

			// End the loop.
			endwhile;
			if ( get_theme_mod( 'pagination_load_more', false ) ) {
					echo '<div class="load-more">';
					next_posts_link( esc_html__( 'Load More', 'surely' ) );
					echo '</div>';
			} else {
				// Previous/next page navigation.
				the_posts_pagination( array(
					'prev_text'          => esc_html__( 'Previous page', 'surely' ),
					'next_text'          => esc_html__( 'Next page', 'surely' ),
					'before_page_number' => '<span class="meta-nav screen-reader-text">' . esc_html__( 'Page', 'surely' ) . ' </span>',
				) );
			}
		// If no content, include the "No posts found" template.
		else :
			get_template_part( 'template-parts/content', 'none' );

		endif;
		?>

		</main><!-- .site-main -->
	</div><!-- .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
