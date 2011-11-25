<?php
/*
Plugin Name: Identica status
Plugin URI: https://github.com/gin/identica-status
Description: Displays last updates from identi.ca accounts.
Author: Moises brenes
Version: 1.0.0.alpha
Author URI: http://www.mbrenes.com/
*/

/* import settings module */
require_once('settings.php');

/* set constants related to this package */
if (!defined('IDENTICA_STATUS_PLUGIN_NAME'))
	define('IDENTICA_STATUS_PLUGIN_NAME', 'Identica status');
if (!defined('IDENTICA_STATUS_PLUGIN_VERSION'))
    define('IDENTICA_STATUS_PLUGIN_VERSION', '1.0.0.alpha');

/* main class */
class Identica_status_WP_Widget extends WP_Widget {
	/* constructor */
	function Identica_status_WP_Widget() {
		$widget_opts = array(
			'classname' => 'identica_status_widget',
			'description' => __('Displays last updates from identi.ca accounts', 'identica_status_widget')
		);
		$this->WP_Widget('identica-status', __('Identi.ca last updates', 'identica_status_widget'), $widget_opts);
	}

	/* displays the widget */
	function widget($args, $instance) {
		extract($args);

        $title = $instance['title'];
        $screen_name = $instance['screen_name'];
        $post_count = (int) $instance['post_count'];

        $content_data = $screen_name . " ($post_count)";

        /* render the widget */
		echo $before_widget
		. $before_title
		. $title
		. $after_title
		. $content_data
		. $widget_content
		. $after_widget;
	}

	/* updates data */
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		global $my_widget_settings;

		/* strip tags (if needed) */
        $title = empty($new_instance['title']) ? $my_widget_settings['title'] : strip_tags($new_instance['title']);
        $screen_name = empty($new_instance['screen_name']) ? $my_widget_settings['screen_name'] : strip_tags($new_instance['screen_name']);
        $post_count = empty($new_instance['post_count']) ? $my_widget_settings['min_post_count'] : (int) $new_instance['post_count'];

        if ($post_count > $my_widget_settings['max_post_count'])
        	$post_count = $my_widget_settings['max_post_count'];
        elseif ($post_count < $my_widget_settings['min_post_count'])
        	$post_count = $my_widget_settings['min_post_count'];

        /* update the widget settings */
		$instance['title'] = $title;
		$instance['screen_name'] = $screen_name;
		$instance['post_count'] = $post_count;

		return $instance;
    }

    /* displays form */
    function form($instance) {
    	$title = esc_attr($instance['title']);
    	$screen_name = esc_attr($instance['screen_name']);
    	$post_count = (int) $instance['post_count'];

    	$title_id = $this->get_field_id('title');
    	$screen_name_id = $this->get_field_id('screen_name');
    	$post_count_id = $this->get_field_id('post_count');

		$title_field = $this->get_field_name('title');
		$screen_name_field = $this->get_field_name('screen_name');
		$post_count_field = $this->get_field_name('post_count');

		/* import form */
		include('form.php');
    }
}

/* register widget */
function Identica_status_register()
{
	load_plugin_textdomain('identica-status', false, dirname(plugin_basename(__FILE__)) . '/lang');
	register_widget('Identica_status_WP_Widget');
}

add_action('widgets_init', 'Identica_status_register');
