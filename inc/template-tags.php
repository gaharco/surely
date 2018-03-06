<?php
/**
 * Custom Surely template tags
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * 
 * @package Surely
 * @since Surely 1.0
 */

if ( ! function_exists( 'surely_entry_meta' ) ) :
/**
 * Prints HTML with meta information for the categories, tags.
 *
 * Create your own surely_entry_meta() function to override in a child theme.
 *
 * @since Surely 1.0
 */
function surely_entry_meta() {


	if ( 'post' === get_post_type() ) {
		$author_avatar_size = apply_filters( 'surely_author_avatar_size', 50 );
		printf( '<span class="byline"><span class="author vcard">%1$s<span class="screen-reader-text">%2$s </span> <a class="url fn n" href="%3$s">%4$s</a></span></span>',
			get_avatar( get_the_author_meta( 'user_email' ), $author_avatar_size ),
			esc_html_x( 'Author', 'Used before post author name.', 'surely' ),
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			get_the_author()
		);
	}

	if ( in_array( get_post_type(), array( 'post', 'attachment' ) ) ) {
		surely_entry_date();
	}

	if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
		echo '<span class="comments-link">';
		comments_popup_link( sprintf( '%1$s<span class="screen-reader-text"> %2$s</span>',
			esc_html__('No comment', 'surely'),
			get_the_title() ) );
		echo '</span>';
	}
}
endif;

if ( ! function_exists( 'surely_entry_date' ) ) :
/**
 * Prints HTML with date information for current post.
 *
 * Create your own surely_entry_date() function to override in a child theme.
 *
 * @since Surely 1.0
 */
function surely_entry_date() {
	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';

	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
	}

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		get_the_date(),
		esc_attr( get_the_modified_date( 'c' ) ),
		get_the_modified_date()
	);

	printf( '<span class="posted-on"><span class="screen-reader-text">%1$s </span><a href="%2$s" rel="bookmark">%3$s</a></span>',
		esc_html_x( 'Posted on', 'Used before publish date.', 'surely' ),
		esc_url( get_permalink() ),
		$time_string
	);
}
endif;

if ( ! function_exists( 'surely_entry_taxonomies' ) ) :
/**
 * Prints HTML with category and tags for current post.
 *
 * Create your own surely_entry_taxonomies() function to override in a child theme.
 *
 * @since Surely 1.0
 */
function surely_entry_taxonomies() {
	$categories_list = get_the_category_list( _x( ', ', 'Used between list items, there is a space after the comma.', 'surely' ) );
	if ( $categories_list && surely_categorized_blog() ) {
		printf( '<span class="cat-links"><span>%1$s </span>%2$s</span>',
			esc_html_x( 'Categories: ', 'Used before category names.', 'surely' ),
			$categories_list
		);
	}

	$tags_list = get_the_tag_list( '', esc_html_x( ', ', 'Used between list items, there is a space after the comma.', 'surely' ) );
	if ( $tags_list ) {
		printf( '<span class="tags-links"><span>%1$s </span>%2$s</span>',
			esc_html_x( 'Tags: ', 'Used before tag names.', 'surely' ),
			$tags_list
		);
	}
}
endif;

if ( ! function_exists( 'surely_post_thumbnail' ) ) :
/**
 * Displays an optional post thumbnail.
 *
 * Wraps the post thumbnail in an anchor element on index views, or a div
 * element when on single views.
 *
 * Create your own surely_post_thumbnail() function to override in a child theme.
 *
 * @since Surely 1.0
 */
function surely_post_thumbnail() {
	if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
		return;
	}

	if ( is_singular() ) :
	?>

	<div class="post-thumbnail">
		<?php the_post_thumbnail(); ?>
	</div><!-- .post-thumbnail -->

	<?php else : ?>

	<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true">
		<?php the_post_thumbnail( 'post-thumbnail', array( 'alt' => the_title_attribute( 'echo=0' ) ) ); ?>
	</a>

	<?php endif; // End is_singular()
}
endif;

if ( ! function_exists( 'surely_excerpt' ) ) :
	/**
	 * Displays the optional excerpt.
	 *
	 * Wraps the excerpt in a div element.
	 *
	 * Create your own surely_excerpt() function to override in a child theme.
	 *
	 * @since Surely 1.0
	 *
	 * @param string $class Optional. Class string of the div element. Defaults to 'entry-summary'.
	 */
	function surely_excerpt( $class = 'entry-summary' ) {
		$class = esc_attr( $class );

		if ( has_excerpt() || is_search() ) : ?>
			<div class="<?php echo esc_attr( $class ); ?>">
				<?php the_excerpt(); ?>
			</div>
		<?php endif;
	}
endif;

if ( ! function_exists( 'surely_excerpt_more' ) && ! is_admin() ) :
/**
 * Replaces "[...]" (appended to automatically generated excerpts) with ... and
 * a 'Continue reading' link.
 *
 * Create your own surely_excerpt_more() function to override in a child theme.
 *
 * @since Surely 1.0
 *
 * @return string 'Continue reading' link prepended with an ellipsis.
 */
function surely_excerpt_more() {
	$link = sprintf( '<a href="%1$s" class="more-link">%2$s</a>',
		esc_url( get_permalink( get_the_ID() ) ),
		/* translators: %s: Name of current post */
		sprintf( '%1$s<span class="screen-reader-text"> "%2$s"</span>', esc_html__('Read More', 'surely'), get_the_title( get_the_ID() ) )
	);
	return ' &hellip; ' . $link;
}
add_filter( 'excerpt_more', 'surely_excerpt_more' );
endif;

if ( ! function_exists( 'surely_excerpt_length' ) && ! is_admin() ) :
/**
 * Change the excerpt number
 *
 * @since Surely 1.0
 *
 * @return number Number for excerpt.
 */
function surely_excerpt_length( $length ) {
	return 30;
}
add_filter( 'excerpt_length', 'surely_excerpt_length', 999 );
endif;

if ( ! function_exists( 'surely_categorized_blog' ) ) :
/**
 * Determines whether blog/site has more than one category.
 *
 * Create your own surely_categorized_blog() function to override in a child theme.
 *
 * @since Surely 1.0
 *
 * @return bool True if there is more than one category, false otherwise.
 */
function surely_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'surely_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'surely_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so surely_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so surely_categorized_blog should return false.
		return false;
	}
}
endif;

/**
 * Flushes out the transients used in surely_categorized_blog().
 *
 * @since Surely 1.0
 */
function surely_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( 'surely_categories' );
}
add_action( 'edit_category', 'surely_category_transient_flusher' );
add_action( 'save_post',     'surely_category_transient_flusher' );

if ( ! function_exists( 'surely_the_custom_logo' ) ) :
/**
 * Displays the optional custom logo.
 *
 * Does nothing if the custom logo is not available.
 *
 * @since Surely 1.0
 */
function surely_the_custom_logo() {
	if ( $secondary_logo = get_theme_mod( 'secondary_logo', false ) ) {
		?>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="secondary-logo"><img src="<?php echo esc_url( $secondary_logo); ?>" alt="<?php esc_attr( bloginfo( 'name' ) ); ?>" /></a>
		<?php
	}

	if ( function_exists( 'the_custom_logo' ) ) {
		the_custom_logo();
	}
}
endif;

/**
 * Print markup for SVG icon.
 *
 * @since Surely 1.0
 * @param string $icon keyword for icon name
 */
function surely_svg_icon ( $icon ) {
	$symbol = '<svg class="icon icon-' . esc_attr( $icon ) . '"><use xlink:href="' . esc_url( get_template_directory_uri() . '/svg/symbol-defs.svg#icon-' . $icon ) . '"></use></svg>';

	return $symbol;
}

/**
 * Custom function to retrieve the archive title based on the queried object.
 *
 * @since Surely 1.0
 *
 * @return string Archive title.
 */
function surely_archive_title() {
	if ( is_category() ) {
		$title = sprintf(
			'<div class="archive-title-pre">%1$s</div><h1 class="page-title">%2$s</h1><div class="taxonomy-description">%3$s</div>',
			esc_html__( 'Category:', 'surely' ),
			single_cat_title( '', false ),
			term_description()
		);
	} elseif ( is_tag() ) {
		$title = sprintf(
			'<div class="archive-title-pre">%1$s</div><h1 class="page-title">%2$s</h1><div class="taxonomy-description">%3$s</div>',
			esc_html__( 'Tagged As:', 'surely' ),
			single_tag_title( '', false ),
			term_description()
		);
	} elseif ( is_author() ) {
		$title = sprintf(
			'<div class="author-avatar">%1$s</div><div class="archive-title-pre">%2$s</div><h1 class="page-title">%3$s</h1><div class="taxonomy-description">%4$s</div>',
			get_avatar( get_the_author_meta( 'user_email' ), 80 ),
			esc_html__( 'Author by:', 'surely' ),
			get_the_author(),
			get_the_author_meta( 'description' )
		);
	} elseif ( is_year() ) {
		$title = sprintf(
			'<div class="archive-title-pre">%1$s</div><h1 class="page-title">%2$s</h1>',
			esc_html__( 'Posted in year:', 'surely' ),
			get_the_date( _x( 'Y', 'yearly archives date format', 'surely' ) )
		);
	} elseif ( is_month() ) {
		$title = sprintf(
			'<div class="archive-title-pre">%1$s</div><h1 class="page-title">%2$s</h1>',
			esc_html__( 'Posted in month:', 'surely' ),
			get_the_date( _x( 'F Y', 'monthly archives date format', 'surely' ) )
		);
	} elseif ( is_day() ) {
		$title = sprintf(
			'<div class="archive-title-pre">%1$s</div><h1 class="page-title">%2$s</h1>',
			esc_html__( 'Posted in:', 'surely' ),
			get_the_date( _x( 'F j, Y', 'daily archives date format', 'surely' ) )
		);
	} elseif ( is_tax( 'post_format' ) ) {
		if ( is_tax( 'post_format', 'post-format-aside' ) ) {
			$title = esc_html_x( 'Asides', 'post format archive title', 'surely' );
		} elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) {
			$title = esc_html_x( 'Galleries', 'post format archive title', 'surely' );
		} elseif ( is_tax( 'post_format', 'post-format-image' ) ) {
			$title = esc_html_x( 'Images', 'post format archive title', 'surely' );
		} elseif ( is_tax( 'post_format', 'post-format-video' ) ) {
			$title = esc_html_x( 'Videos', 'post format archive title', 'surely' );
		} elseif ( is_tax( 'post_format', 'post-format-quote' ) ) {
			$title = esc_html_x( 'Quotes', 'post format archive title', 'surely' );
		} elseif ( is_tax( 'post_format', 'post-format-link' ) ) {
			$title = esc_html_x( 'Links', 'post format archive title', 'surely' );
		} elseif ( is_tax( 'post_format', 'post-format-status' ) ) {
			$title = esc_html_x( 'Statuses', 'post format archive title', 'surely' );
		} elseif ( is_tax( 'post_format', 'post-format-audio' ) ) {
			$title = esc_html_x( 'Audio', 'post format archive title', 'surely' );
		} elseif ( is_tax( 'post_format', 'post-format-chat' ) ) {
			$title = esc_html_x( 'Chats', 'post format archive title', 'surely' );
		}
	} elseif ( is_post_type_archive() ) {
		$title = sprintf( esc_html__( 'Archives: %s' , 'surely'), post_type_archive_title( '', false ) );
	} elseif ( is_tax() ) {
		$tax = get_taxonomy( get_queried_object()->taxonomy );
		/* translators: 1: Taxonomy singular name, 2: Current taxonomy term */
		$title = sprintf( esc_html__( '%1$s: %2$s', 'surely' ), $tax->labels->singular_name, single_term_title( '', false ) );
	} else {
		$title = esc_html__( 'Archives', 'surely' );
	}

	echo apply_filters( 'surely_archive_title', $title );
}

/**
 * Breadcrumbs
 *
 * @since Surely 1.0
 */
function surely_breadcrumbs() { 
	if ( !is_front_page() ) {

		printf( 
			'<div class="site-breadcrumbs" ><span>%1$s</span><a href="%2$s">%3$s</a>',
			esc_html__('You are here:', 'surely'),
			home_url(),
			esc_html__( 'Home', 'surely' )
		);

		echo surely_svg_icon('pointer');
	}

	if ( (is_category() || is_single()) && !is_attachment() ) {
		$category = get_the_category();
		if (count($category) > 0){
			$ID = $category[0]->cat_ID;
			if ( $ID ) {
				echo get_category_parents($ID, TRUE, ' ', FALSE );
				echo surely_svg_icon('pointer');
			}
		}
	}

	if(is_single() || is_page()) {
		if ( !is_front_page() ){
			echo '<span>';
			the_title();
			echo '</span>';
		}
	}

	if (is_tag()){ echo '<span>' . esc_html__('Tag: ', 'surely') . single_tag_title('',FALSE). '</span>'; }
	if (is_404()){ echo '<span>' . esc_html__('404 - Page not Found', 'surely') . '</span>'; }
	if (is_search()){ echo '<span>' . esc_html__('Search', 'surely'). '</span>'; }
	if (is_year()){ echo '<span>' . get_the_time('Y'). '</span>'; }
	if (is_month()){ echo '<span>' . get_the_time('F Y'). '</span>'; }
	if (is_author()){ echo '<span>' . esc_html__('Posts by ', 'surely') . get_the_author(). '</span>'; }


	if ( !is_front_page() ) {
		echo "</div>";	
	}
}

/**
 * Pagination for custom query.
 *
 * @since Surely 1.0
 *
 * @param WP_Query $query, the custom query
 * @param Array @args, the same arguments as the_posts_pagination() function 
 * @return string HTML Markup for.
 */
function surely_custom_query_pagination( $query, $args = array()) {
	$navigation = '';

	// Don't print empty markup if there's only one page.
	if ( $query->max_num_pages > 1 ) {
		$args = wp_parse_args( $args, array(
				'mid_size'		   => 1,
				'prev_text'		  => esc_html__( 'Previous', 'surely' ),
				'next_text'		  => esc_html__( 'Next', 'surely' ),
				'screen_reader_text' => esc_html__( 'Posts navigation', 'surely' ),
		) );

		// Make sure we get a string back. Plain is the next best thing.
		if ( isset( $args['type'] ) && 'array' == $args['type'] ) {
				$args['type'] = 'plain';
		}

		// Set up paginated links.
		$links = paginate_links( $args );

		if ( $links ) {
				$navigation = _navigation_markup( $links, 'pagination', $args['screen_reader_text'] );
		}
	}

	return $navigation;
}

/**
 * Render the footer credit, print from the footer_credit options, or default.
 *
 * @since Surely 1.0
 *
 * @return void
 */
function surely_footer_credit( $echo = false ) {

	if ( $footer_credit = get_theme_mod( 'footer_credit', false ) ) {
		$credit = surely_sanitize_footer_credit( $footer_credit );
	} else {
		$credit = surely_sanitize_footer_credit( sprintf(
			'<a href="%1$s" rel="home">%2$s</a> %3$s <a href="%4$s">%5$s</a>',
			esc_url( home_url('/') ),
			get_bloginfo('name'),
			__('Proudly powered by', 'surely'),
			esc_url( __( 'https://wordpress.org/', 'surely' ) ),
			__('WordPress', 'surely')
		) );
	}
	if ( $echo ) {
		echo $credit;
	}
	return $credit;
}


