<p>
	<label for="<?php echo $title_id; ?>"><?php _e('Title:'); ?></label>
	<input class="widefat" id="<?php echo  $title_id; ?>" name="<?php echo $title_field; ?>" type="text" value="<?php echo $title; ?>" />
	<small>
		<input id="<?php echo $profile_link_id; ?>" name="<?php echo $profile_link_field; ?>" type="checkbox" value="true" <?php if ($profile_link) echo 'checked="checked"'; ?> />
		<label for="<?php echo $profile_link_id; ?>"><?php _e('Link to profile'); ?></label>
	</small>
</p>
<p>
	<label for="<?php echo $screen_name_id; ?>"><?php _e('Screen name:'); ?></label>
	<input class="widefat" id="<?php echo $screen_name_id; ?>" name="<?php echo $screen_name_field; ?>" type="text" value="<?php echo $screen_name; ?>" />
</p>
<p>
	<label for="<?php echo $post_count_id; ?>"><?php _e('Post count:'); ?></label>
	<input class="widefat" id="<?php echo $post_count_id; ?>" name="<?php echo $post_count_field; ?>" type="text" value="<?php echo $post_count; ?>" />
</p>
