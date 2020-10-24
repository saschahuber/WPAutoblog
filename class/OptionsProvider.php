<?php

class WPAB_OptionsProvider{
	/**
     * Start up
     */
    public function __construct(){
    }
	
	public function get_options_bool(){
		$options = array(
			#array('title' => '', 'option_key' => '', 'recommended' => false),
		);
		
		return $options;
	}
}

?>