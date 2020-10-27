<?php

class WPAB_MetaBoxRegisterHook{
    /**
     * Start up
     */
    public function __construct(){
		$this->source_meta_provider = new WPAB_SourceMetaProvider();
		add_action( 'save_post', array($this, 'wpab_save_source_meta'), 1, 2 );
    }
	
	function wpab_add_source_metaboxes() {
		add_meta_box('wpab_source_url', __('Source URL', 'wp-autoblog'), array($this, 'wpab_source_url'), 'autoblog-source', 'advanced', 'default');
		add_meta_box('wpab_source_feed_url', __('Source Feed-URL', 'wp-autoblog'), array($this, 'wpab_source_feed_url'), 'autoblog-source', 'advanced', 'default');
		add_meta_box('wpab_source_keywords', __('Source keywords (separated by ";")', 'wp-autoblog'), array($this, 'wpab_source_keywords'), 'autoblog-source', 'advanced', 'default');
		add_meta_box('wpab_source_credit_message', __('Source credit', 'wp-autoblog'), array($this, 'wpab_source_credit_message'), 'autoblog-source', 'advanced', 'default');
		add_meta_box('wpab_source_is_active', __('Is active?', 'wp-autoblog'), array($this, 'wpab_source_is_active'), 'autoblog-source', 'advanced', 'default');
		add_meta_box('wpab_source_auto_publish', __('Auto publish?', 'wp-autoblog'), array($this, 'wpab_source_auto_publish'), 'autoblog-source', 'advanced', 'default');
		add_meta_box('wpab_source_links_noindex', __('Add noindex to links?', 'wp-autoblog'), array($this, 'wpab_source_links_noindex'), 'autoblog-source', 'advanced', 'default');
		add_meta_box('wpab_source_type', __('Source type', 'wp-autoblog'), array($this, 'wpab_source_type'), 'autoblog-source', 'advanced', 'default');
		add_meta_box('wpab_source_post_type', __('Source post type', 'wp-autoblog'), array($this, 'wpab_source_post_type'), 'autoblog-source', 'advanced', 'default');
		add_meta_box('wpab_source_author_id', __('Source author', 'wp-autoblog'), array($this, 'wpab_source_author_id'), 'autoblog-source', 'advanced', 'default');
	}
	
	function wpab_source_url() {
		global $post;
		wp_nonce_field( basename( __FILE__ ), 'wpab_source_url_field' );
		$source_url = esc_url(get_post_meta( $post->ID, 'wpab_source_url', true ));
		?>
			<p><?php echo __('What is the url to the sources homepage?', 'wp-autoblog'); ?></p>
			<input type="text" name="wpab_source_url" value="<?php echo esc_textarea( $source_url ); ?>" class="widefat">
		<?php
	}
	
	function wpab_source_feed_url() {
		global $post;
		wp_nonce_field( basename( __FILE__ ), 'wpab_source_feed_url_field' );
		$source_feed_url = esc_url(get_post_meta( $post->ID, 'wpab_source_feed_url', true ));
		?>
			<p><?php echo __('From which url should the new content be imported? For wordpress-sites this is usually "[WORDPRESS_URL]/feed" or "[WORDPRESS_URL]/[CATEGORY]/feed".', 'wp-autoblog'); ?></p>
			<input type="text" name="wpab_source_feed_url" value="<?php echo esc_textarea( $source_feed_url ); ?>" class="widefat">
		<?php
	}
	
	function wpab_source_keywords() {
		global $post;
		wp_nonce_field( basename( __FILE__ ), 'wpab_source_keywords_field' );
		$source_keywords = esc_textarea(get_post_meta( $post->ID, 'wpab_source_keywords', true ));
		?>
			<p><?php echo __('Give some keywords that the post must contain to be imported (separated by ";"). Leave blank, if every post of the feed should be imported.', 'wp-autoblog'); ?></p>
			<input type="text" name="wpab_source_keywords" value="<?php echo esc_textarea( $source_keywords ); ?>" class="widefat">
		<?php
	}
	
	function wpab_source_credit_message() {
		global $post;
		wp_nonce_field( basename( __FILE__ ), 'wpab_source_credit_message_field' );
		$source_credit = esc_textarea(get_post_meta( $post->ID, 'wpab_source_credit_message', true ));
		?>
			<p><?php echo __('Give credit to the content-source. Enter something like "This post was originally released on [SOURCE_NAME]". Can containe shortcodes.<br/>
			Leave blank if no credit-message should be displayed at the end of the post.', 'wp-autoblog'); ?></p>
			<input type="text" name="wpab_source_credit_message" value="<?php echo esc_textarea( $source_credit ); ?>" class="widefat">
		<?php
	}
	
	function wpab_source_is_active() {
		global $post;
		wp_nonce_field( basename( __FILE__ ), 'wpab_source_is_active_field' );
		$is_active = get_post_meta( $post->ID, 'wpab_source_is_active', true );
		?>
			<p><?php echo __('Should posts be currently imported from this source?', 'wp-autoblog'); ?></p>
			<input type="checkbox" name="wpab_source_is_active" <?php echo ($is_active?'checked':''); ?>>
		<?php
	}
	
	function wpab_source_auto_publish() {
		global $post;
		wp_nonce_field( basename( __FILE__ ), 'wpab_source_auto_publish_field' );
		$auto_publish = get_post_meta( $post->ID, 'wpab_source_auto_publish', true );
		?>
			<p><?php echo __('Should imported posts get the post status "published" automatically after importing or the status "pending"?', 'wp-autoblog'); ?></p>
			<input type="checkbox" name="wpab_source_auto_publish" <?php echo ($auto_publish?'checked':''); ?>>
		<?php
	}
	
	function wpab_source_links_noindex() {
		global $post;
		wp_nonce_field( basename( __FILE__ ), 'wpab_source_links_noindex_field' );
		$links_noindex = get_post_meta( $post->ID, 'wpab_source_links_noindex', true );
		?>
			<p><?php echo __('Should the noindex-tag be added to all links in an imported post that do not point to your websites url?', 'wp-autoblog'); ?></p>
			<input type="checkbox" name="wpab_source_links_noindex" <?php echo ($links_noindex?'checked':''); ?>>
		<?php
	}
	
	function wpab_source_type() {
		global $post;
		wp_nonce_field( basename( __FILE__ ), 'wpab_source_type_field' );
		$wpab_source_type = get_post_meta($post->ID, 'wpab_source_type', true);
		?>
			<select name="wpab_source_type">
				<?php foreach($this->source_meta_provider->get_source_types() as $source_type): ?>
					<option value="<?php echo $source_type; ?>" <?php selected($wpab_source_type, $source_type); ?>><? echo $this->source_meta_provider->get_source_type_label($source_type); ?></option>
				<?php endforeach; ?>
			</select>
		<?php
	}
	
	function wpab_source_post_type() {
		global $post;
		wp_nonce_field( basename( __FILE__ ), 'wpab_source_post_type_field' );
		$wpab_source_post_type = get_post_meta($post->ID, 'wpab_source_post_type', true);
		
		$post_types = get_post_types_by_support( array( 'title', 'editor'));
		
		?>
			<p><?php echo __('Which post type should imported posts be added to?', 'wp-autoblog'); ?></p>
			<select name="wpab_source_post_type">
				<?php foreach(array_keys($post_types) as $key): ?>
					<?php
						$post_type = get_post_types( array('name' => $post_types[$key]), 'objects' )[$post_types[$key]];
					?>
					<option value="<?php echo $post_type->name; ?>" <?php selected($wpab_source_post_type, $post_type->name); ?>><? echo $post_type->labels->singular_name; ?></option>
				<?php endforeach; ?>
			</select>
		<?php
	}
	
	function wpab_source_author_id() {
		global $post;
		wp_nonce_field( basename( __FILE__ ), 'wpab_source_author_id_field' );
		$wpab_source_author_id = get_post_meta($post->ID, 'wpab_source_author_id', true);
		
		$users = get_users();
		
		?>
			<p><?php echo __('Which author should the imported posts be attached to?', 'wp-autoblog'); ?></p>
			<select name="wpab_source_author_id">
				<option value="null" <?php selected($wpab_source_author_id, 'null'); ?>><? echo __('None', 'wp-autoblog'); ?></option>
				<?php foreach($users as $user): ?>
					<option value="<?php echo $user->id; ?>" <?php selected($wpab_source_author_id, $user->id); ?>><? echo $user->display_name; ?></option>
				<?php endforeach; ?>
			</select>
		<?php
	}
	
	function wpab_save_source_meta( $post_id, $post ) {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		// Check if not an autosave.
        if ( wp_is_post_autosave( $post_id ) ) {
            return;
        }
 
        // Check if not a revision.
        if ( wp_is_post_revision( $post_id ) ) {
            return;
        }

		$meta_fields = array(
			array('name' => 'wpab_source_url', 'type' => 'url'),
			array('name' => 'wpab_source_feed_url', 'type' => 'url'),
			array('name' => 'wpab_source_keywords', 'type' => 'text'),
			array('name' => 'wpab_source_credit_message', 'type' => 'html'),
			array('name' => 'wpab_source_is_active', 'type' => 'bool'),
			array('name' => 'wpab_source_auto_publish', 'type' => 'bool'),
			array('name' => 'wpab_source_links_noindex', 'type' => 'bool'),
			array('name' => 'wpab_source_type', 'type' => null),
			array('name' => 'wpab_source_post_type', 'type' => null),
			array('name' => 'wpab_source_author_id', 'type' => null)
		);

		$source_meta = array();
		
		foreach($meta_fields as $field){
			//Clean checkboxes
			if($field['type'] == 'bool'){
				delete_post_meta($post_id, $field['name']);
			}
			
			if ( ! isset( $_POST[$field['name']] ) || ! wp_verify_nonce( $_POST[$field['name'].'_field'], basename(__FILE__) ) ) {
				continue;
			}
			
			switch($field['type']){
				case 'url':
					$source_meta[$field['name']] = esc_url_raw($_POST[$field['name']]);
				case 'text':
					$source_meta[$field['name']] = sanitize_textarea_field( $_POST[$field['name']] );
				case 'bool':
					$source_meta[$field['name']] = sanitize_text_field($_POST[$field['name']]);
					break;
				case 'html':
					$source_meta[$field['name']] = sanitize_text_field($_POST[$field['name']]);
					break;
				default:
					$source_meta[$field['name']] = sanitize_text_field($_POST[$field['name']]);
					break;
			}
		}

		foreach ( $source_meta as $key => $value ){
			if ( get_post_meta( $post_id, $key, false ) ) {
				update_post_meta( $post_id, $key, $value );
			} else {
				add_post_meta( $post_id, $key, $value);
			}

			if ( ! $value ) {
				delete_post_meta( $post_id, $key );
			}
		}
	}
}

?>