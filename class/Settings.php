<?php

class WPAB_Settings{
	/**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct(){
		$this->options_provider = new WPAB_OptionsProvider();
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }
	
	public function add_plugin_page() {
		//create new top-level menu
		add_menu_page('WP Autoblog', 'WP Autoblog', 'administrator', __FILE__, array($this, 'settings_page_display') , plugins_url('/images/icon.png', __FILE__) );
	}
	
	public function page_init() {
		$options_bool = $this->options_provider->get_options_bool();
		foreach($options_bool as $option){
			$args = array(
				'type' => 'bool',
				'default' => false,
			);
			register_setting( 'wp-autoblog-settings-group', 'wp_autoblog_'.$option['option_key'], $args ); 
		}
	}
	
	private function display_checkbox_options(){
		$options = $this->options_provider->get_options_bool();
		
		foreach($options as $option){
			?>
				<tr valign="top">
				<th scope="row"><? echo __($option['title'], 'wp-autoblog'); ?><? echo $option['recommended'] ? ' ('.__('Recommended', 'wp-autoblog').')':''; ?></th>
				<td><input type="checkbox" name="wp_autoblog_<? echo $option['option_key']; ?>" <?php echo get_option('wp_autoblog_'.$option['option_key'])?'checked':''; ?> /></td>
				</tr>
			<?
		}
	}

	private function get_plugin_info(){
		?>
		
		<?php echo __('Check the features, that should be disabled.', 'wp-autoblog'); ?>
		
		<?
	}

	public function settings_page_display() {
		?>
		<div class="wrap">
			<h1>WP Autoblog</h1>

			<?php $this->get_plugin_info(); ?>

			<form method="post" action="options.php">
				<?php settings_fields( 'wp-autoblog-settings-group' ); ?>
				<?php do_settings_sections( 'wp-autoblog-settings-group' ); ?>
				<table class="form-table">
					<?php $this->display_checkbox_options(); ?>
				</table>
				
				<?php submit_button(); ?>
			</form>
		</div>
		<?php 
	}
}

if( is_admin() ){
    $wp_autoblog_settings = new WPAB_Settings();
}

?>