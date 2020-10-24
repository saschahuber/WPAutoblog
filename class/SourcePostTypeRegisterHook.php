<?php

class WPAB_SourcePostTypeRegisterHook{

    /**
     * Start up
     */
    public function __construct(){
		$this->metabox_register_hook = new WPAB_MetaBoxRegisterHook();
        add_action('init', array($this, 'register_source_post_type'), 0);
    }
	
	function register_source_post_type() {
		$labels = array(
			'name'                => _x( 'Autoblog sources', 'Post Type General Name', 'wp-autoblog' ),
			'singular_name'       => _x( 'Autoblog source', 'Post Type Singular Name', 'wp-autoblog' ),
			'menu_name'           => __( 'Autoblog sources', 'wp-autoblog' ),
			'parent_item_colon'   => __( 'Autoblog source', 'wp-autoblog' ),
			'all_items'           => __( 'All sources', 'wp-autoblog' ),
			'view_item'           => __( 'View source', 'wp-autoblog' ),
			'add_new_item'        => __( 'Add New source', 'wp-autoblog' ),
			'add_new'             => __( 'Add New', 'wp-autoblog' ),
			'edit_item'           => __( 'Edit source', 'wp-autoblog' ),
			'update_item'         => __( 'Update source', 'wp-autoblog' ),
			'search_items'        => __( 'Search source', 'wp-autoblog' ),
			'not_found'           => __( 'Not Found', 'wp-autoblog' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'wp-autoblog' ),
		);
		 
		$args = array(
			'label'               => __( 'autoblog-source', 'wp-autoblog' ),
			'description'         => __( 'Autoblog sources', 'wp-autoblog' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'thumbnail', ),
			#'taxonomies'          => array( '' ),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => true,
			'menu_position'       => 5,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'capability_type'     => 'post',
			'show_in_rest' => false,
			'register_meta_box_cb' => array($this->metabox_register_hook, 'wpab_add_source_metaboxes'),
	 
		);
		 
		register_post_type( 'autoblog-source', $args );
	}
}

$wp_autoblog_register_post_type = new WPAB_SourcePostTypeRegisterHook();

?>