<?php

class WPAB_SourceMetaProvider{
	/**
     * Start up
     */
    public function __construct(){}
	
	public function get_source_type_label($key){
		switch($key){
			case 'wordpress_feed':
				return __("Wordpress feed", 'wp-autoblog');
			default:
				return __('Unknown source type', 'wp-autoblog');
		}
	}
	
	public function get_source_types(){
		return array(
		'wordpress_feed'
		);
	}
}

?>