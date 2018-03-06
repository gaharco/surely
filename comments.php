<?php
/**
 * The template for displaying comments
 *
 * The area of the page that contains both current comments
 * and the comment form.
 *
 * 
 * @package Surely
 * @since Surely 1.0
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area">

	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
				$comments_number = get_comments_number();
				if ( 1 === $comments_number ) {
					/* translators: %s: post title */
					printf( esc_html_x( 'One comment', 'comments title', 'surely' ) );
				} else {
					printf(
						/* translators: 1: number of comments, 2: post title */
						_nx(
							'%1$s comment',
							'%1$s comments',
							$comments_number,
							'comments title',
							'surely'
						),
						number_format_i18n( $comments_number ),
						get_the_title()
					);
				}
			?>
		</h2>

		<?php //the_comments_navigation(); ?>

		<ol class="comment-list">
			<?php
				wp_list_comments( array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 42,
				) );
			?>
		</ol><!-- .comment-list -->

		<?php the_comments_navigation(); ?>

	<?php endif; // Check for have_comments(). ?>

	<?php
		// If comments are closed and there are comments, let's leave a little note, shall we?
		if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
	?>
		<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'surely' ); ?></p>
	<?php endif; ?>

	<?php
		$aria_req = "";
		$fields =  array(
			'author' =>
				'<p class="comment-form-author"><label for="author" class="screen-reader-text">' . esc_html__( 'Your Name', 'surely' ) . '</label> ' .
				( $req ? '<span class="required screen-reader-text">*</span>' : '' ) .
				'<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) .
				'" size="30"' . $aria_req . ' placeholder="' . esc_attr__('Your Name', 'surely') . ( $req ? '*' : '') . '" /></p>',

			'email' =>
				'<p class="comment-form-email"><label for="email" class="screen-reader-text">' . esc_html__( 'Your Email', 'surely' ) . '</label> ' .
				( $req ? '<span class="required screen-reader-text">*</span>' : '' ) .
				'<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) .
				'" size="30"' . $aria_req . ' placeholder="' . esc_attr__('Your Email', 'surely') . ( $req ? '*' : '') . '" /></p>',

			'url' =>
				'<p class="comment-form-url"><label for="url" class="screen-reader-text">' . esc_html__( 'Your Website', 'surely' ) . '</label>' .
				'<input id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) .
				'" size="30" placeholder="' . esc_attr__('Your Website', 'surely'). '" /></p>',
		);

		$comment_field = '<p class="comment-form-comment"><label for="comment" class="screen-reader-text">' . esc_html_x( 'Comment', 'noun', 'surely' ) .
						'</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true" placeholder="' . esc_attr__('Write your comment here', 'surely'). '" >' .
						'</textarea></p>';

		comment_form( array(
			'comment_field'      => $comment_field,
			'fields'             => $fields,
			'title_reply_before' => '<h2 id="reply-title" class="comment-reply-title">',
			'title_reply_after'  => '</h2>',
			'cancel_reply_link'	 => surely_svg_icon('close') . esc_html__('Cancel reply', 'surely'),
		) );
	?>

</div><!-- .comments-area -->
