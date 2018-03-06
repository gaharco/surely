<?php
/**
 * Template Name: Grid
 *
 * 
 * @package Rise
 * @since Rise 1.0
 */

get_header(); ?>
	<?php if ( is_active_sidebar( 'header-widget-full-width' )  ) : ?>
		<div class="header-widget widget-area-full" >
			<?php dynamic_sidebar( 'header-widget-full-width' ); ?>
		</div><!-- .header-full .widget-area -->
	<?php endif; ?>

	<?php $list_class = 'grid-view'; ?>
	<div id="primary" class="content-area <?php echo esc_attr($list_class); ?>">
		<main id="main" class="site-main">
		<?php
		$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
		$query = new WP_Query( array( 'paged' => $paged, 'posts_per_page' => 14 ) );

		if ( $query->have_posts() ) : 

			// Start the loop.
			while ( $query->have_posts() ) : $query->the_post();
				get_template_part( 'template-parts/content-list', get_post_format() );

			// End the loop.
			endwhile;

			// Previous/next page navigation.
			echo surely_custom_query_pagination( $query, array(
				'prev_text'          => esc_html__( 'Previous', 'surely' ),
				'next_text'          => esc_html__( 'Next', 'surely' ),
				'before_page_number' => '<span class="meta-nav screen-reader-text">' . esc_html__( 'Page', 'surely' ) . ' </span>',
				'current'			 => max( 1, get_query_var('paged') ),
				'total'				 => $query->max_num_pages,
			) );

		// If no content, include the "No posts found" template.
		else :
			get_template_part( 'template-parts/content', 'none' );

		endif;
		wp_reset_postdata();
		?>
		</main><!-- .site-main -->
	</div><!-- .content-area -->

<?php 
	get_sidebar();
	get_footer();