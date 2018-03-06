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
	<?php if ( is_sticky() && is_home() && ! is_paged() ) : ?>
		<span class="sticky-post"><span class="screen-reader-text"><?php esc_html_e( 'Sticky', 'surely' ); ?></span><?php echo surely_svg_icon('pin'); ?></span>
	<?php endif; ?>

	<?php if ( has_post_thumbnail() ) : ?>
		<a href="<?php the_permalink(); ?>" class="post-thumbnail"><?php the_post_thumbnail('large'); ?></a>
	<?php endif; ?>

	<header class="entry-header">

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

	<div class="entry-content">
		<?php
			/* translators: %s: Name of current post */
			the_excerpt();
		?>
	</div><!-- .entry-content -->

</article><!-- #post-## -->
