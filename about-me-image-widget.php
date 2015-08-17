<?php
/*
Plugin Name: About Me Image Widget
Plugin URI: http://webplantmedia.com/starter-themes/wordpresscanvas/features/widgets/wordpress-canvas-widgets/
Description: Add "About Me" image widget, with caption and link, to any widget area.
Author: Chris Baldelomar
Author URI: http://webplantmedia.com/
Version: 1.1
License: GPLv2 or later
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WPC_ABOUT_ME_IMAGE_WIDGET_VERSION', '1.1' );

function wpc_about_me_image_widget_enqueue_admin_scripts() {
	$screen = get_current_screen();

	if ( 'widgets' == $screen->id ) {
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

function wpc_about_me_image_widget_customize_enqueue() {
	wp_deregister_style( 'wpc-widgets-admin-style' );
	wp_deregister_script( 'wpc-widgets-admin-js' );

	wp_register_style( 'wpc-widgets-admin-style', plugin_dir_url( __FILE__ ) . 'css/admin.css', array(), WPC_ABOUT_ME_IMAGE_WIDGET_VERSION, 'all' );
	wp_enqueue_style( 'wpc-widgets-admin-style' );

	wp_enqueue_media();
	wp_register_script( 'wpc-widgets-admin-js', plugin_dir_url( __FILE__ ) . 'js/admin.js', array ( 'jquery' ), WPC_ABOUT_ME_IMAGE_WIDGET_VERSION, true );
	wp_enqueue_script( 'wpc-widgets-admin-js' );
}
add_action( 'customize_controls_enqueue_scripts', 'wpc_about_me_image_widget_customize_enqueue' );

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


		$url = ! empty( $instance['url'] ) ? esc_url( $instance['url'] ) : '';
		$image = esc_url( $instance['image'] );

		if ( ! empty( $image ) ) {
			if ( !empty( $url ) )
				echo '<a class="image-hover" href="'.$url.'">';

			echo '<img class="img-'.$class.'" src="'.$image.'" style="'.implode( ';', $style ).'" />';

			if ( !empty( $url ) )
				echo '</a>';
		}

		$allowed_html = array(
			'a' => array(
				'href' => array(),
				'title' => array(),
				'target' => array(),
			),
			'br' => array(),
			'em' => array(),
			'strong' => array(),
		);
		if ( !empty( $instance['description'] ) )
			echo '<p class="sidebar-caption">'.wp_kses( $instance['description'], $allowed_html ).'</p>';

		echo $args['after_widget'];
	}

	function update( $new_instance, $old_instance ) {
		$instance['title'] = strip_tags( stripslashes($new_instance['title']) );
		$instance['image'] = esc_url_raw( $new_instance['image'] );
		$instance['description'] = stripslashes( $new_instance['description'] );
		$instance['style'] = strip_tags( $new_instance['style'] );
		$instance['url'] = esc_url_raw( $new_instance['url'] );
		return $instance;
	}

	function form( $instance ) {
		$title = isset( $instance['title'] ) ? $instance['title'] : 'About Me!';
		$image = isset( $instance['image'] ) ? $instance['image'] : '';
		$imagestyle = '';
		if ( empty( $image ) )
			$imagestyle = ' style="display:none"';

		$description = isset( $instance['description'] ) ? $instance['description'] : '';
		$description = esc_textarea($description);
		$style = isset( $instance['style'] ) ? $instance['style'] : 'none';
		$url = isset( $instance['url'] ) ? $instance['url'] : '';
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" />
		</p>
		<div class="wpc-widgets-image-field">
			<input class="widefat" id="<?php echo $this->get_field_id( 'image' ); ?>" name="<?php echo $this->get_field_name( 'image' ); ?>" type="text" value="<?php echo $image; ?>" />
			<a class="wpc-widgets-image-upload button inline-button" data-target="#<?php echo $this->get_field_id( 'image' ); ?>" data-preview=".wpc-widgets-preview-image" data-frame="select" data-state="wpc_widgets_insert_single" data-fetch="url" data-title="Insert Image" data-button="Insert" data-class="media-frame wpc-widgets-custom-uploader" title="Add Media">Add Media</a>
			<a class="button wpc-widgets-delete-image" data-target="#<?php echo $this->get_field_id( 'image' ); ?>" data-preview=".wpc-widgets-preview-image">Delete</a>
			<div class="wpc-widgets-preview-image"<?php echo $imagestyle; ?>><img src="<?php echo esc_attr($image); ?>" /></div>
		</div>
		<p>
			<label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Description:') ?></label>
			<textarea class="widefat" rows="4" cols="20" id="<?php echo $this->get_field_id('description'); ?>" name="<?php echo $this->get_field_name('description'); ?>"><?php echo $description; ?></textarea>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('style'); ?>"><?php _e('Select Style:'); ?></label>
			<select id="<?php echo $this->get_field_id('style'); ?>" name="<?php echo $this->get_field_name('style'); ?>">
				<option value="none"<?php selected( $style, 'none' ); ?>>None</option>';
				<option value="circle"<?php selected( $style, 'circle' ); ?>>Circle</option>';
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('url'); ?>"><?php _e('URL:') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('url'); ?>" name="<?php echo $this->get_field_name('url'); ?>" value="<?php echo $url; ?>" />
		</p>
		<?php
	}
}
