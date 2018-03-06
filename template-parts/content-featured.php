<?php
/**
 * The template part for displaying content
 *
 * @package Surely
 * @since Surely 1.0
 */
?>
<?php $display_class = 'overlay-thumbnail'; ?>
<article id="post-<?php the_ID(); ?>" <?php post_class( $display_class ); ?>>

	<header class="entry-header">
		<?php if ( is_sticky() && is_home() && ! is_paged() ) : ?>
			<span class="sticky-post"><?php _e( 'Featured', 'surely' ); ?></span>
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

	<?php surely_post_thumbnail(); ?>

	<div class="entry-content">
		<?php the_excerpt(); ?>
	</div><!-- .entry-content -->

</article><!-- #post-## -->