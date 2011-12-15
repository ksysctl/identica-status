<?php
/*
Plugin Name: Identica status
Plugin URI: https://github.com/gin/identica-status
Description: Displays last updates from identi.ca accounts.
Author: Moises brenes
Version: 1.0.0.beta
Author URI: http://www.mbrenes.com/
*/

/* import settings module */
require_once('settings.php');
/* import api module */
require_once('api.php');

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
        $profile_link = $instance['profile_link'];

        $identica = new MyIdentica($screen_name);
        $statuses = $identica->get_user_timeline($post_count);
        $profile = $identica->get_user_profile();

        /* set title */
        $content = '';
		if (!empty($title)) {
        	$content .= $before_title;
        	$content .= ($profile_link == true) ? '<a title="' . $profile->name . '" href="' . MyIdentica::USER_URL . $profile->id . '">' . $title . '</a>' : $title;
        	$content .= $after_title;
        }

        /* set body */
        $content .= '<ul>';
        foreach ($statuses as $status) {
        	$content .= '<li>';
        	$content .= '<a class="identica-status-screen-name" title="' . $status->user->name . '" href="' . $status->user->statusnet_profile_url . '">@'
        	. $status->user->screen_name
        	. '</a>: ';
            if (is_null($status->in_reply_to_user_id) == false) {
	        	$content .= 'RT <a class="identica-status-screen-name" title="' . $status->in_reply_to_screen_name . '" href="' . MyIdentica::USER_URL . (string) $status->in_reply_to_user_id . '">@'
	        	. $status->in_reply_to_screen_name
	        	. '</a> ';
        	}
        	$content .= '<span class="identica-status-text">' . $status->text . '</span>';
        	$content .= '<a class="identica-status-date-time" title="' . $status->created_at . '" href="' . MyIdentica::NOTICE_URL . (string) $status->id . '"> at '
        	. $status->created_at
        	. '</a>';
			$content .= '</li>';
        }
        $content .= '</ul>';

        /* render the widget */
		echo $before_widget
		. $content
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
        $profile_link = ($new_instance['profile_link'] == 'true') ? true : false;

        if ($post_count > $my_widget_settings['max_post_count'])
        	$post_count = $my_widget_settings['max_post_count'];
        elseif ($post_count < $my_widget_settings['min_post_count'])
        	$post_count = $my_widget_settings['min_post_count'];

        /* update the widget settings */
		$instance['title'] = $title;
		$instance['screen_name'] = $screen_name;
		$instance['post_count'] = $post_count;
		$instance['profile_link'] = $profile_link;

		return $instance;
    }

    /* displays form */
    function form($instance) {
    	$title = esc_attr($instance['title']);
    	$screen_name = esc_attr($instance['screen_name']);
    	$post_count = (int) $instance['post_count'];
    	$profile_link = $instance['profile_link'];

    	$title_id = $this->get_field_id('title');
    	$screen_name_id = $this->get_field_id('screen_name');
    	$post_count_id = $this->get_field_id('post_count');
    	$profile_link_id = $this->get_field_id('profile_link');

		$title_field = $this->get_field_name('title');
		$screen_name_field = $this->get_field_name('screen_name');
		$post_count_field = $this->get_field_name('post_count');
		$profile_link_field = $this->get_field_name('profile_link');

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
