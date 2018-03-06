<?php
/**
 * Widget API: Surely_Widget_Twitter class
 * This widget is dependent to the plugin "OAuth Twitter feed for developers" 
 * https://wordpress.org/plugins/oauth-twitter-feed-for-developers/
 *
 * @package Surely
 * @since 1.0.0
 */

/**
 * Core class used to implement a Twitter widget.
 *
 * @since 1.0.0
 *
 * @see WP_Widget
 */
class Surely_Widget_Twitter extends WP_Widget {
	/** constructor */
	public function __construct() {
		$widget_ops = array('classname' => 'widget-twitter', 'description' => esc_html__( "Twitter Feed.", 'surely') );
		parent::__construct('surely-twitter', esc_html__('Surely - Twitter', 'surely'), $widget_ops);
		$this->alt_option_name = 'widget_twitter';
	}
	
	function widget($args, $instance) {		
	extract( $args );
		$default = array ( 'widget_title'=> esc_html__('Latest Tweet', 'surely'), 'id'=>'', 'qty'=>5 );
		$instance = wp_parse_args($instance, $default);			
		$widget_title = apply_filters('widget_title', $instance['widget_title']);
		$id = $instance['id'];
		$qty = $instance['qty'];
		// WIDGET OUTPUT
		echo $before_widget;
		if(!empty($widget_title)){ echo $before_title . $widget_title . $after_title; }
		if ( function_exists('getTweets') ) :
			$tweets = getTweets( $id, $qty);

			if (empty($tweets['errors']) ){
				echo '<div class="twitter-update-list">';
				
				foreach( $tweets as $tweet ){
					$text = $this->autolink($tweet['text']);
					$text = preg_replace('/(^|\s)@(\w+)/', '\1<a href="http://www.twitter.com/\2">@\2</a>', $text);
					$text = preg_replace('/(^|\s)#(\w+)/', '\1<a href="http://search.twitter.com/search?q=%23\2">#\2</a>', $text);
					printf( '<div class="tweet"><div class="entry-meta"><a href="%1$s">%2$s</a></div><p class="icon-twitter">%3$s</p></div>',
						esc_url( 'http://twitter.com/' . $id . '/statuses/' . $tweet['id_str'] ),
						$this->relative_time( strtotime( $tweet['created_at'] ) ),
						$text );	
				}
				echo '</div>';
			}else{
				echo '<ul class="twitter_update_list"><li>' . esc_html__('Cannot fetch tweets', 'surely') . '</li></ul>';
			}
			?>
			<div class="twitter-account">
				<a  rel="nofollow" href="<?php echo esc_url('http://www.twitter.com/'.  $id) ?>/"><?php esc_html_e('Follow Me', 'surely'); ?></a>
			</div>
		<?php
		else :
		?>
			<p><?php esc_html_e('This widget is dependent to "OAuth Twitter feed for developers" plugin. Please install it for using this widget', 'surely'); ?></p>
		<?php
		endif;
		echo $after_widget;		
	}

	function update($new_instance, $old_instance) {				
		$instance = $old_instance;
		$instance['widget_title'] = sanitize_text_field( $new_instance['widget_title'] );
		$instance['id'] = sanitize_text_field( $new_instance['id'] );
		$instance['qty'] = absint( $new_instance['qty'] );

		return $instance;
	}

	function form($instance) {	
		$default = array ( 'widget_title'=> esc_html__('Latest Tweet', 'surely'), 'id'=>'', 'qty'=>5 );
		$instance = wp_parse_args( $instance, $default );			
		$widget_title = esc_attr( $instance['widget_title'] );
		$id = esc_attr( $instance['id'] );
		$qty = esc_attr( absint( $instance['qty'] ) );
	?>
		<p>
			<?php esc_html_e('Widget title:', 'surely'); ?>
			<input class="widefat" type="text" name="<?php echo $this->get_field_name('widget_title'); ?>" value="<?php echo $widget_title; ?>" />
		</p>
		<p>
			<?php esc_html_e('Enter ID of your twitter account', 'surely'); ?>
			<input class="widefat" type="text" name="<?php echo $this->get_field_name('id'); ?>" value="<?php echo $id; ?>" />
		</p>
		<p>
			<?php esc_html_e('Number of tweets', 'surely'); ?>
			<input type="number" min="0" max="10" step="1" name="<?php echo $this->get_field_name('qty'); ?>" value="<?php echo $qty; ?>" />
		</p>

	<?php
	}

	/*
	 * Function relative time
	 *
	 */
	function relative_time($time = false, $limit = 86400, $format = 'g:i A M jS') {
		if (empty($time) || (!is_string($time) && !is_numeric($time))) $time = time();
		elseif (is_string($time)) $time = strtotime($time);

		$now = time();
		$relative = '';

		if ($time === $now) $relative = esc_html__('now', 'surely');
		elseif ($time > $now) $relative = esc_html__('in the future', 'surely');
		else {
			$diff = $now - $time;

			if ($diff < 60) {
				$relative = esc_html_x('Less than one minute ago', 'Text for tweet time which posted less than a minute ago', 'surely');
			} elseif (($minutes = ceil($diff/60)) < 60) {
				if ( (int)$minutes === 1 ) {
					$relative = esc_html_x( 'A Minute ago', 'Text for tweet time which posted a minute ago', 'surely');
				} else {
					$relative = sprintf( esc_html_x('%1$d minutes ago', 'Text for tweet time which posted few minutes ago, %1$d is for number of minutes that larger than 1 and less than 60', 'surely'), $minutes );	
				}
			} elseif ( $diff < (24*60*60) ){
				$hours = ceil($diff/3600);
				if ( (int)$hours === 1 ) {
					$relative = esc_html_x( 'An Hour ago', 'Text for tweet time which posted an hour ago', 'surely');
				} else {
					$relative = sprintf( esc_html_x('%1$d minutes ago', 'Text for tweet time which posted few hours ago, %1$d is for number of minutes that larger than 1 and less than 24', 'surely'), $hours );	
				}
			}elseif ( $diff < (48*60*60) ){
				$hours = ceil($diff/3600);
				$relative = esc_html_x('1 Day ago', 'Text for tweet time which posted a day ago', 'surely');
			}else{
				$relative = sprintf( esc_html_x('%1$d days ago', 'Text for tweet time which posted days ago, %1$d is for number of days that larger than 1', 'surely'), ceil($diff / 86400) ); 
			}
		}

		return $relative;
	}

	function autolink($str, $attributes=array()) {
		$attrs = '';
		foreach ($attributes as $attribute => $value) {
			$attrs .= " {$attribute}=\"{$value}\"";
		}

		$str = ' ' . $str;
		$str = preg_replace(
			'`([^"=\'>])((http|https|ftp)://[^\s<]+[^\s<\.)])`i',
			'$1<a href="$2"'.$attrs.'>$2</a>',
			$str
		);
		$str = substr($str, 1);
		
		return $str;
	}

	
}

function surely_register_widget_twitter() {
	return register_widget("Surely_Widget_Twitter");	
}
add_action('widgets_init', 'surely_register_widget_twitter');