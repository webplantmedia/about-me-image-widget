<?php
/*
Plugin Name: About Me Image Widget by Angie Makes
Plugin URI: http://angiemakes.com/feminine-wordpress-blog-themes-women/
Description: Add "About Me" image widget, with caption and link, to any widget area.
Author: Chris Baldelomar
Author URI: http://angiemakes.com/
Version: 1.4.3
License: GPLv2 or later
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WPC_ABOUT_ME_IMAGE_WIDGET_VERSION', '1.4.3' );

function wpc_about_me_image_widget_enqueue_admin_scripts( $hook ) {
	if ( $hook == 'post-new.php' || $hook == 'post.php' || $hook == 'widgets.php' ) {
		wp_deregister_style( 'wpc-widgets-admin-style' );
		wp_deregister_script( 'wpc-widgets-admin-js' );

		wp_register_style( 'wpc-widgets-admin-style', plugin_dir_url( __FILE__ ) . 'css/admin.css', array(), WPC_ABOUT_ME_IMAGE_WIDGET_VERSION, 'all' );
		wp_enqueue_style( 'wpc-widgets-admin-style' );

		wp_enqueue_media();
		wp_register_script( 'wpc-widgets-admin-js', plugin_dir_url( __FILE__ ) . 'js/admin.js', array ( 'jquery' ), WPC_ABOUT_ME_IMAGE_WIDGET_VERSION, true );
		wp_enqueue_script( 'wpc-widgets-admin-js' );
	}
}
add_action('admin_enqueue_scripts', 'wpc_about_me_image_widget_enqueue_admin_scripts' );

function wpc_about_me_image_widget_widgets_init() {
	register_widget('WPC_About_Me_Image_Widget');
}
add_action('widgets_init', 'wpc_about_me_image_widget_widgets_init');

class WPC_About_Me_Image_Widget extends WP_Widget {
	function __construct() {
		$widget_ops = array( 'description' => __('Add and customize your "About Me" information.') );
		parent::__construct( 'wpc_about_me_image', __('About Me Image'), $widget_ops );
	}

	function widget($args, $instance) {
		$instance['title'] = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		echo $args['before_widget'];

		if ( !empty($instance['title']) )
			echo $args['before_title'] . $instance['title'] . $args['after_title'];

		$class = !empty( $instance['style'] ) ? $instance['style'] : 'none';

		$style = array();
		if ( 'circle' == $class ) {
			$style[] = 'border-radius:50%';
		}


		$url = ! empty( $instance['url'] ) ? $instance['url'] : '';
		$image = $instance['image'];
		$image2x = isset( $instance['image2x'] ) ? $instance['image2x'] : '';
		$text_align = isset( $instance['text_align'] ) ? $instance['text_align'] : '';

		$d_style = '';
		if ( ! empty( $text_align ) ) {
			$text_align = $this->sanitize_text_align( $text_align );
			$d_style = ' style="text-align:'.$text_align.';"';
		}

		$output = $srcset = '';
		if ( ! empty( $image ) ) {
			if ( !empty( $url ) )
				$output .= '<a class="image-hover" href="'.esc_url( $url ).'">';

			if ( '' != $image2x )
				$srcset = 'srcset="' . esc_url( $image ) . ' 1x, ' . esc_url( $image2x ) . ' 2x" ';

			$output .= '<img class="img-'.esc_attr( $class ).'" src="'.esc_url( $image ).'" '.$srcset.'style="'.implode( ';', $style ).'" />';

			if ( !empty( $url ) )
				$output .= '</a>';

			$output = '<div class="wpc-widget-img-container" style="text-align: center;">' . $output . '</div>';
		}

		echo $output;

		if ( !empty( $instance['description'] ) )
			echo '<div class="sidebar-caption"'.$d_style.'>'.wpautop( $instance['description'] ).'</div>';

		echo $args['after_widget'];
	}

	function update( $new_instance, $old_instance ) {
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['image'] = esc_url_raw( $new_instance['image'] );
		$instance['image2x'] = esc_url_raw( $new_instance['image2x'] );
		$instance['description'] = wp_kses_post( $new_instance['description'] );
		$instance['style'] = sanitize_text_field( $new_instance['style'] );
		$instance['text_align'] = $this->sanitize_text_align( $new_instance['text_align'] );
		$instance['url'] = esc_url_raw( $new_instance['url'] );
		return $instance;
	}

	function sanitize_text_align( $text_align ) {
		$whitelist = array( 'left', 'center', 'right' );
		if ( ! in_array( $text_align, $whitelist ) )
			$text_align = 'center';

		return $text_align;
	}

	function form( $instance ) {
		$title = isset( $instance['title'] ) ? $instance['title'] : 'About Me!';
		$image = isset( $instance['image'] ) ? $instance['image'] : '';
		$image2x = isset( $instance['image2x'] ) ? $instance['image2x'] : '';
		$imagestyle = '';
		if ( empty( $image ) )
			$imagestyle = ' style="display:none"';

		$description = isset( $instance['description'] ) ? $instance['description'] : '';
		$style = isset( $instance['style'] ) ? $instance['style'] : 'none';
		$text_align = isset( $instance['text_align'] ) ? $this->sanitize_text_align( $instance['text_align'] ) : 'center';
		$url = isset( $instance['url'] ) ? $instance['url'] : '';

		?>
		<div class="wpc-image-wrapper">
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:') ?></label>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr( $title ); ?>" />
			</p>
			<div class="wpc-widgets-image-field">
				<label for="<?php echo $this->get_field_id( 'image' ); ?>"><?php echo _e( 'Image URL:' ); ?>
					<input class="widefat" id="<?php echo $this->get_field_id( 'image' ); ?>" name="<?php echo $this->get_field_name( 'image' ); ?>" type="text" value="<?php echo esc_url( $image ); ?>" />
				</label>
				<a class="wpc-widgets-image-upload button inline-button" data-target="#<?php echo $this->get_field_id( 'image' ); ?>" data-preview=".wpc-widgets-preview-image" data-frame="select" data-state="wpc_widgets_insert_single" data-fetch="url" data-title="Insert Image" data-button="Insert" data-class="media-frame wpc-widgets-custom-uploader" title="Add Media">Add Media</a>
				<a class="button wpc-widgets-delete-image" data-target="#<?php echo $this->get_field_id( 'image' ); ?>" data-preview=".wpc-widgets-preview-image">Delete</a>
				<div class="wpc-widgets-preview-image"<?php echo $imagestyle; ?>><img src="<?php echo esc_url($image); ?>" /></div>
			</div>
			<div class="wpc-widgets-image-field">
				<label for="<?php echo $this->get_field_id( 'image2x' ); ?>"><?php echo _e( 'Image 2x URL (Retina Displays):' ); ?>
					<input class="widefat" id="<?php echo $this->get_field_id( 'image2x' ); ?>" name="<?php echo $this->get_field_name( 'image2x' ); ?>" type="text" value="<?php echo esc_url( $image2x ); ?>" />
				</label>
				<a class="wpc-widgets-image-upload button inline-button" data-target="#<?php echo $this->get_field_id( 'image2x' ); ?>" data-preview=".wpc-widgets-preview-image" data-frame="select" data-state="wpc_widgets_insert_single" data-fetch="url" data-title="Insert Image" data-button="Insert" data-class="media-frame wpc-widgets-custom-uploader" title="Add Media">Add Media</a>
				<a class="button wpc-widgets-delete-image" data-target="#<?php echo $this->get_field_id( 'image2x' ); ?>" data-preview=".wpc-widgets-preview-image">Delete</a>
				<div class="wpc-widgets-preview-image"<?php echo $imagestyle; ?>><img src="<?php echo esc_url($image2x); ?>" /></div>
			</div>
			<p>
				<label for="<?php echo $this->get_field_id('style'); ?>"><?php _e('Select Style:'); ?></label>
				<select id="<?php echo $this->get_field_id('style'); ?>" name="<?php echo $this->get_field_name('style'); ?>">
					<option value="none"<?php selected( $style, 'none' ); ?>>None</option>';
					<option value="circle"<?php selected( $style, 'circle' ); ?>>Circle</option>';
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Description:') ?></label>
				<textarea class="widefat" rows="4" cols="20" id="<?php echo $this->get_field_id('description'); ?>" name="<?php echo $this->get_field_name('description'); ?>"><?php echo esc_textarea( $description ); ?></textarea>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('text_align'); ?>"><?php _e('Text Align:'); ?></label>
				<select id="<?php echo $this->get_field_id('text_align'); ?>" name="<?php echo $this->get_field_name('text_align'); ?>">
					<option value="left"<?php selected( $text_align, 'left' ); ?>>Left</option>';
					<option value="center"<?php selected( $text_align, 'center' ); ?>>Center</option>';
					<option value="right"<?php selected( $text_align, 'right' ); ?>>Right</option>';
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('url'); ?>"><?php _e('URL:') ?></label>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id('url'); ?>" name="<?php echo $this->get_field_name('url'); ?>" value="<?php echo esc_url( $url ); ?>" />
			</p>
		</div>
		<?php
	}
}
