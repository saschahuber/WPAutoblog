<?php

class WPAB_MetaBoxPostListRegisterHook{
    /**
     * Start up
     */
    public function __construct(){
		$this->source_meta_provider = new WPAB_SourceMetaProvider();
		add_filter('manage_autoblog-source_posts_columns', array($this, 'create_table_head'));
		add_action('manage_autoblog-source_posts_custom_column', array($this, 'create_table_content'), 10, 2);
    }
	
	function create_table_head( $columns ) {
		$columns['wpab_source_url']  = __('Source URL', 'wp-autoblog');
		$columns['wpab_source_type']  = __('Source type', 'wp-autoblog');
		$columns['wpab_source_is_active']  = __('Is active?', 'wp-autoblog');
		$columns['wpab_source_auto_publish']  = __('Auto publish?', 'wp-autoblog');
		$columns['wpab_source_links_noindex']  = __('Add noindex to links??', 'wp-autoblog');
		return $columns;
	}

	function create_table_content( $column_name, $post_id ) {
		if( $column_name == 'wpab_source_type' ) {
			$source_type = get_post_meta( $post_id, 'wpab_source_type', true );
			echo $this->source_meta_provider->get_source_type_label($source_type);
		}
		if( $column_name == 'wpab_source_url' ) {
			$source_url = get_post_meta( $post_id, 'wpab_source_url', true );
			echo $source_url;
		}
		if( $column_name == 'wpab_source_is_active' ) {
			$source_is_active = get_post_meta( $post_id, 'wpab_source_is_active', true );
			if( $source_is_active == 'on' ){ echo __('Yes', 'wp-autoblog'); } else { echo __('No', 'wp-autoblog'); }
		}
		if( $column_name == 'wpab_source_auto_publish' ) {
			$source_auto_publish = get_post_meta( $post_id, 'wpab_source_auto_publish', true );
			if( $source_auto_publish == 'on' ){ echo __('Yes', 'wp-autoblog'); } else { echo __('No', 'wp-autoblog'); }
		}
		if( $column_name == 'wpab_source_links_noindex' ) {
			$source_auto_publish = get_post_meta( $post_id, 'wpab_source_links_noindex', true );
			if( $source_auto_publish == 'on' ){ echo __('Yes', 'wp-autoblog'); } else { echo __('No', 'wp-autoblog'); }
		}
	}
}

if( is_admin() ){
    $wp_autoblog_metabox_post_list_register_hook = new WPAB_MetaBoxPostListRegisterHook();
}

?>