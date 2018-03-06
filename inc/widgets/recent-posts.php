<?php
/**
 * Widget API: Surely_Widget_Recent_Posts class
 *
 * @package Surely
 * @since 1.0.0
 */

/**
 * Core class used to implement a Recent Posts widget.
 *
 * @since 1.0.0
 *
 * @see WP_Widget
 */
class Surely_Widget_Recent_Posts extends WP_Widget {

	/**
	 * Sets up a new Recent Posts widget instance.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
		$widget_ops = array(
			'classname' => 'widget-recent-posts',
			'description' => esc_html__( "Your site&#8217;s most recent Posts.", 'surely'),
			'customize_selective_refresh' => true,
		 );
		parent::__construct('surely-recent-posts', esc_html__('Surely - Recent Posts', 'surely'), $widget_ops);
		$this->alt_option_name = 'widget_recent_posts';
	}

	/**
	 * Outputs the content for the current Recent Posts widget instance.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current Recent Posts widget instance.
	 */
	public function widget( $args, $instance ) {
		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}

		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : esc_html__( 'Recent Posts', 'surely' );

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
		if ( ! $number )
			$number = 5;
		$sort = isset( $instance['sort'] ) ? $instance['sort'] : 'date';
		$category = isset( $instance['category'] ) ? $instance['category'] : '';
		$tags = !empty( $instance['tags'] ) ? explode(',', $instance['tags']) : false;
		$animation = ( isset($instance['animation']) && in_array( $instance['animation'], array( 'fade', 'horizontal', 'vertical') ) ) ? $instance['animation'] : 'horizontal';

		$slider_opt = json_encode ( array (
			'slideshow'			=> ( isset($instance['slideshow']) && $instance['slideshow'] == 'slideshow' ) ? true : false,
			'slideshow_time'	=> isset( $instance['slideshow_time'] ) ? $instance['slideshow_time'] * 1000 : 5000,
			'animation'			=> ( $animation == 'fade' ) ? 'fade' : 'slide',
			'direction'			=> ( $animation !== 'fade') ? $animation : null,
			'prevText'			=> sprintf(
										'<span class="screen-reader-text">%1$s</span>%2$s',
										esc_html__('Previous', 'surely'),
										surely_svg_icon('arrow-left')
									 ),
			'nextText'			=> sprintf(
										'<span class="screen-reader-text">%1$s</span>%2$s',
										esc_html__('Next', 'surely'),
										surely_svg_icon('arrow-right')
									 ),
		) );

		$query_args = array(
			'posts_per_page'		=> $number,
			'cat'					=> $category,
			'tag_slug__in'			=> $tags,
			'no_found_rows'			=> true,
			'post_status'			=> 'publish',
			'orderby'				=> $sort,
			'ignore_sticky_posts'	=> true
		);

		$presented = isset( $instance['presentation'] ) ? $instance['presentation'] : 'thumbnail';

		//if ( $presented == '')
		echo $args['before_widget'];
		if ( ( $args['id'] == 'header-widget-full-width' || $args['id'] == 'footer-widget-full-width' ) && $presented == 'featured' ) {
			$this->featured_posts_big( $query_args );
		} elseif ( ( $args['id'] == 'header-widget-full-width' || $args['id'] == 'footer-widget-full-width' ) && $presented == 'slider' ) {
			$this->featured_posts_slider( $query_args, $slider_opt );
		} else {
			
		 	if ( $title ) {
				echo $args['before_title'] . $title . $args['after_title'];
			}

			if  ( $presented == 'thumbnail' )
				$this->featured_posts_list( $query_args, $sort, 'medium');
			elseif ( $presented == 'small-thumbnail' )
				$this->featured_posts_list( $query_args, $sort, 'thumbnail');

		}
		echo $args['after_widget'];
	}

	/**
	 * Handles updating the settings for the current Recent Posts widget instance.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Updated settings to save.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title']			= sanitize_text_field( $new_instance['title'] );
		$instance['number']			= absint( $new_instance['number'] );
		$instance['sort']			= $new_instance['sort'];
		$instance['category']		= $new_instance['category'];
		$instance['tags']			= sanitize_text_field( $new_instance['tags'] );
		$instance['presentation']	= sanitize_text_field( $new_instance['presentation'] );
		$instance['slideshow']		= $new_instance['slideshow'];
		$instance['slideshow_time']	= absint( $new_instance['slideshow_time'] );
		$instance['animation']		= $new_instance['animation'];
		return $instance;
	}

	/**
	 * Outputs the settings form for the Recent Posts widget.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$title			= isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$number			= isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		$show_date		= isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : false;
		$sort			= isset( $instance['sort'] ) ? esc_attr( $instance['sort'] ) : 'date';
		$category		= isset( $instance['category'] ) ? $instance['category'] : '';
		$tags			= isset( $instance['tags'] ) ? esc_attr( $instance['tags'] ) : '';
		$presentation	= isset( $instance['presentation'] ) ? esc_attr( $instance['presentation'] ) : 'thumbnail';
		$slideshow		= isset( $instance['slideshow'] ) ? (bool) $instance['slideshow'] : false;
		$slideshow_time	= isset( $instance['slideshow_time'] ) ? absint( $instance['slideshow_time'] ) : 5;
		$animation		= isset( $instance['animation'] ) ? esc_attr( $instance['animation'] ) : 'fade';

?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'surely' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'category' ); ?>"><?php esc_html_e( 'Category:', 'surely' ); ?></label>
			<?php wp_dropdown_categories( 
					array (
						'show_option_all' => esc_html__('All Categories', 'surely'),
						'name'            => $this->get_field_name( 'category' ),
						'id'              => $this->get_field_id( 'category' ),
						'selected'        => $category,
						'class'			  => 'widefat',
					) ); 
			?>
		</p>

		<p><label for="<?php echo $this->get_field_id( 'tags' ); ?>"><?php esc_html_e( 'Tags:', 'surely' ); ?></label>
		<input class="widefat" id="<?php $this->get_field_id( 'tags' ); ?>" name="<?php echo $this->get_field_name( 'tags' ); ?>" type="text" value="<?php echo $tags; ?>" />
		</p>

		<p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php esc_html_e( 'Number of posts to show:', 'surely' ); ?></label>
		<input class="tiny-text" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" /></p>

		<p><label for="<?php echo $this->get_field_id( 'sort' ); ?>"><?php esc_html_e( 'Sort By:', 'surely' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'sort' ); ?>" name="<?php echo $this->get_field_name( 'sort' ); ?>" >
				<option value="date" <?php selected( $sort, 'date' ); ?> > <?php esc_html_e('Date', 'surely'); ?> </option>
				<option value="comment_count" <?php selected( $sort, 'comment_count' ); ?> > <?php esc_html_e('Comments Number', 'surely'); ?> </option>
			</select>
		</p>
		<p>
			<span><?php esc_html_e('Presented as', 'surely'); ?></span><br>
			<input type="radio" id="<?php echo $this->get_field_id( 'presentation' ) . '-1'; ?>" name="<?php echo $this->get_field_name( 'presentation' ); ?>" value="thumbnail" <?php checked( $presentation, 'thumbnail'); ?> />
			<label for="<?php echo $this->get_field_id( 'presentation' ) . '-1'; ?>"><?php esc_html_e( 'Thumbnail list', 'surely'); ?></label><br>
			
			<input type="radio" id="<?php echo $this->get_field_id( 'presentation' ) . '-2'; ?>" name="<?php echo $this->get_field_name( 'presentation' ); ?>" value="small-thumbnail" <?php checked( $presentation, 'small-thumbnail'); ?>/>
			<label for="<?php echo $this->get_field_id( 'presentation' ) . '-2'; ?>"><?php esc_html_e( 'Small thumbnail list', 'surely'); ?></label><br>
			
			<input type="radio" id="<?php echo $this->get_field_id( 'presentation' ) . '-3'; ?>" name="<?php echo $this->get_field_name( 'presentation' ); ?>" value="featured" class="presentation-featured-opt" <?php checked( $presentation, 'featured'); ?>/>
			<label for="<?php echo $this->get_field_id( 'presentation' ) . '-3'; ?>"><?php esc_html_e( 'Featured posts', 'surely'); ?></label><br>
			
			<input type="radio" id="<?php echo $this->get_field_id( 'presentation' ) . '-4'; ?>" name="<?php echo $this->get_field_name( 'presentation' ); ?>" value="slider" class="presentation-slider-opt" <?php checked( $presentation, 'slider'); ?>/>
			<label for="<?php echo $this->get_field_id( 'presentation' ) . '-4'; ?>"><?php esc_html_e( 'Featured posts slider', 'surely'); ?></label><br>

			<span class="slider-options">
				<input type="checkbox" id="<?php echo $this->get_field_id( 'slideshow' ); ?>" name="<?php echo $this->get_field_name( 'slideshow' ); ?>" value="slideshow" <?php checked( $slideshow, 'slideshow'); ?> /><label for="<?php echo $this->get_field_id( 'slideshow' ); ?>"><?php esc_html_e('Slideshow', 'surely'); ?></label><br>
				<label for="<?php echo $this->get_field_id( 'slideshow_time' ); ?>"><?php esc_html_e( 'Slideshow time:', 'surely' ); ?></label><br>
				<input class="tiny-text" id="<?php echo $this->get_field_id( 'slideshow_time' ); ?>" name="<?php echo $this->get_field_name( 'slideshow_time' ); ?>" type="number" step="1" min="1" value="<?php echo $slideshow_time; ?>" size="3" /> 
				<?php esc_html_e('in seconds', 'surely'); ?><br>
				<label for="<?php echo $this->get_field_id( 'animation' ); ?>"><?php esc_html_e( 'Slide Animation:', 'surely' ); ?></label>
				<select id="<?php echo $this->get_field_id( 'animation' ); ?>" name="<?php echo $this->get_field_name( 'animation' ); ?>" >
					<option value="fade" <?php selected($animation, 'fade'); ?> ><?php esc_html_e('Fade In/Out', 'surely') ?></option>
					<option value="horizontal" <?php selected($animation, 'horizontal'); ?> ><?php esc_html_e('Slide Horizontal', 'surely') ?></option>
					<option value="vertical" <?php selected($animation, 'vertical'); ?> ><?php esc_html_e('Slide Vertical', 'surely') ?></option>
				</select>
			</span>
		</p>
<?php
	}


	/**
	 * Display featured posts on with a big background image.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args WP_Query Arguments.
	 */
	public function featured_posts_big( $args ) {
		$featured = new WP_Query( $args ); 

		if ( $featured->have_posts() ) :
			$featured->the_post();
			?>
			<section id="featured-posts" class="site-featured-posts <?php echo esc_attr( get_theme_mod( 'featured_posts_type', 'carousel' ) ); ?>" >
				<div class="featured-background">
					<?php the_post_thumbnail( 'full', array( 'alt' => the_title_attribute( 'echo=0' ) ) ); ?>
				</div>
				<div class="featured-wrapper">
					<div class="featured-post featured-big-post">
							<div class="entry-meta">
								<?php surely_entry_meta(); ?>
							</div><!-- .entry-meta -->

							<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
					</div>
					<?php
					/* If still have another posts display as lists */ 
					if ( $featured->have_posts() ) : ?>
						<h2 class="more-featured-title">
							<?php esc_html_e( 'More Featured Posts', 'surely' ); ?>
						</h2>
						<div class="more-featured-posts">
						<?php while ( $featured->have_posts() ) : $featured->the_post(); ?>
							<div class="featured-post featured-small-post">
									<div class="entry-meta">
										<?php //surely_entry_meta(); ?>
									</div><!-- .entry-meta -->

									<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
							</div>
						<?php endwhile; ?>
						</div>
					<?php	
					endif;
					wp_reset_postdata();
					?>
				</div>
			</section>

		<?php
		endif; 

	}

	/**
	 * Show the featured posts on slider
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args WP_Query Arguments.
	 */
	public function featured_posts_slider( $args, $opt ) {
		$featured = new WP_Query( $args ); 

		if ( $featured->have_posts() ) :
			?>
			<section class="featured-slider" data-slider-options="<?php echo esc_attr($opt); ?>">
				<div class="slides active-slide-1 clear"  >
					<?php
					while ( $featured->have_posts() ) :
						$featured->the_post();
						?>
						<article>
							<a href="<?php the_permalink(); ?>" class="post-thumbnail">
								<?php the_post_thumbnail( 'full', array( 'alt' => the_title_attribute( 'echo=0' ) ) ); ?>
							</a>

							<header class="entry-header">

								<div class="entry-meta">
									<?php surely_entry_meta(); ?>
								</div><!-- .entry-meta -->

								<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

								<div class="entry-index">
									<?php
									printf( esc_html_x('%1$d of %2$d', 'Index for slider, %1$d(is number for the current slide) of %2$d(is number for the total slider items)', 'surely'),
										$featured->current_post + 1,
										$featured->post_count
										)
									?>
								</div>
							</header><!-- .entry-header -->

						</article><!-- #post-## -->
						<?php
					endwhile;
					?>
				</div>
			</section>
			<?php
			wp_reset_postdata();
		endif; // ( $featured->have_posts() )
	}

	/**
	 * Show the featured posts with thumbnailed list
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args WP_Query Arguments.
	 */
	public function featured_posts_list( $args, $sort = 'date', $thumb = 'medium' ) {
		$featured = new WP_Query( $args ); 

		if ($featured->have_posts()) :
		?>
		<ul class="<?php echo esc_attr( 'image-' . $thumb .  ' sort-' . $sort )  ; ?>">
		<?php while ( $featured->have_posts() ) : $featured->the_post(); ?>
			<li>
			<?php if ( has_post_thumbnail() ) : ?>
				<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true">
					<?php the_post_thumbnail( $thumb, array( 'alt' => the_title_attribute( 'echo=0' ) ) ); ?>
				</a>
			<?php endif; ?>
			<?php if ( $sort == 'date' ) : ?>
				<span class="entry-meta"><?php echo get_the_date(); ?></span>
			<?php endif; ?>
			<?php if ( $sort == 'comment_count' ) : ?>
				<span class="entry-meta"><?php comments_popup_link( esc_html__( 'No comment', 'surely' ) ); ?></span>
			<?php endif; ?>

				<span class="entry-title"><a href="<?php the_permalink(); ?>"><?php get_the_title() ? the_title() : the_ID(); ?></a></span>
			</li>
		<?php endwhile; ?>
		</ul>
		<?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();

		endif;
	}
}

function surely_widget_recent_posts() {
	return register_widget("Surely_Widget_Recent_Posts"); 
}
add_action('widgets_init', 'surely_widget_recent_posts');
