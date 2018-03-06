<?php
/**
 * The template part for displaying content
 *
 * 
 * @package Surely
 * @since Surely 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( ); ?>>

	<header class="entry-header">
		<?php if ( is_sticky() && is_home() && ! is_paged() ) : ?>
			<span class="sticky-post"><span class="screen-reader-text"><?php esc_html_e( 'Sticky', 'surely' ); ?></span><?php echo surely_svg_icon('pin'); ?></span>
		<?php endif; ?>

		<div class="entry-meta">
			<?php surely_entry_meta(); ?>
			<?php
				edit_post_link(
					sprintf(
						'%1$s<span class="screen-reader-text"> "%2$s"</span>',
						esc_html__( 'Edit', 'surely' ),
						get_the_title()
					),
					'<span class="edit-link">',
					'</span>'
				);
			?>
		</div><!-- .entry-meta -->

		<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
	</header><!-- .entry-header -->

	<?php //surely_excerpt(); ?>

	<?php //surely_post_thumbnail(); ?>

	<div class="entry-content">
		<?php
			/* translators: %s: Name of current post */
			the_content( sprintf(
				'%1$s<span class="screen-reader-text"> "%2$s"</span>',
				esc_html__('Read more', 'surely'),
				get_the_title()
			) );

			wp_link_pages( array(
				'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'surely' ) . '</span>',
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
				'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'surely' ) . ' </span>%',
				'separator'   => '<span class="screen-reader-text">, </span>',
			) );
		?>
	</div><!-- .entry-content -->
	<footer class="entry-footer">
		<?php
		if ( function_exists( 'sharing_display' ) ) {
			sharing_display( '', true );
		}
		?>

		<div class="entry-meta">
			<?php 
			surely_entry_taxonomies();
			?>
		</div>

		<?php
		if ( class_exists( 'Jetpack_RelatedPosts' ) ) {
			echo do_shortcode( '[jetpack-related-posts]' );
		}		
		?>
	</footer>

</article><!-- #post-## -->
