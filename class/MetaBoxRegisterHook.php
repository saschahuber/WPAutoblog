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
		add_meta_box('wpab_source_keywords', __('Source keywords (separated by ";")', 'wp-autoblog'), array($this, 'wpab_source_keywords'), 'autoblog-source', 'advanced', 'default');
		add_meta_box('wpab_source_credit_message', __('Source credit', 'wp-autoblog'), array($this, 'wpab_source_credit_message'), 'autoblog-source', 'advanced', 'default');
		add_meta_box('wpab_source_is_active', __('Is active?', 'wp-autoblog'), array($this, 'wpab_source_is_active'), 'autoblog-source', 'advanced', 'default');
		add_meta_box('wpab_source_auto_publish', __('Auto publish?', 'wp-autoblog'), array($this, 'wpab_source_auto_publish'), 'autoblog-source', 'advanced', 'default');
		add_meta_box('wpab_source_type', __('Source type', 'wp-autoblog'), array($this, 'wpab_source_type'), 'autoblog-source', 'advanced', 'default');
	}
	
	function wpab_source_url() {
		global $post;
		wp_nonce_field( basename( __FILE__ ), 'wpab_source_url_field' );
		$source_url = get_post_meta( $post->ID, 'wpab_source_url', true );
		?>
			<p><?php echo __('From which url should the new content be imported? For wordpress-sites this is usually "[WORDPRESS_URL]/feed" or "[WORDPRESS_URL]/[CATEGORY]/feed".', 'wp-autoblog'); ?></p>
			<input type="text" name="wpab_source_url" value=" <?php echo esc_textarea( $source_url ); ?>" class="widefat">
		<?php
	}
	
	function wpab_source_keywords() {
		global $post;
		wp_nonce_field( basename( __FILE__ ), 'wpab_source_keywords_field' );
		$source_keywords = get_post_meta( $post->ID, 'wpab_source_keywords', true );
		?>
			<p><?php echo __('Give some keywords that the post must contain to be imported (separated by ";"). Leave blank, if every post of the feed should be imported.', 'wp-autoblog'); ?></p>
			<input type="text" name="wpab_source_keywords" value=" <?php echo esc_textarea( $source_keywords ); ?>" class="widefat">
		<?php
	}
	
	function wpab_source_credit_message() {
		global $post;
		wp_nonce_field( basename( __FILE__ ), 'wpab_source_credit_message_field' );
		$source_credit = get_post_meta( $post->ID, 'wpab_source_credit_message', true );
		?>
			<p><?php echo __('Give credit to the content-source. Enter something like "This post was originally released on [SOURCE_NAME]". Can containe shortcodes.<br/>
			Leave blank if no credit-message should be displayed at the end of the post.', 'wp-autoblog'); ?></p>
			<input type="text" name="wpab_source_credit_message" value=" <?php echo esc_textarea( $source_credit ); ?>" class="widefat">
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
			array('field' => 'wpab_source_url', 'type' => 'text'),
			array('field' => 'wpab_source_keywords', 'type' => 'text'),
			array('field' => 'wpab_source_credit_message', 'type' => 'text'),
			array('field' => 'wpab_source_is_active', 'type' => 'bool'),
			array('field' => 'wpab_source_auto_publish', 'type' => 'bool'),
			array('field' => 'wpab_source_type', 'type' => null)
		);

		$source_meta = array();
		
		foreach($meta_fields as $field){
			if ( ! isset( $_POST[$field['field']] ) || ! wp_verify_nonce( $_POST[$field['field'].'_field'], basename(__FILE__) ) ) {
				return $post_id;
			}
			
			switch($field['type']){
				case "text":
					$source_meta[$field['field']] = esc_textarea( $_POST[$field['field']] );
					break;
				default:
					$source_meta[$field['field']] = $_POST[$field['field']];
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