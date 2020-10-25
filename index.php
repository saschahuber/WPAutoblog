<?
/**
 * Plugin Name: WP Autoblog
 * Plugin URI: https://sascha-huber.com/projekte/wp-autoblog
 * Text Domain: wp-autoblog
 * Domain Path: /languages
 * Description: Aggregator plugin to import content from various sources
 * Version: 0.1
 * Author: Sascha Huber
 * Author URI: https://sascha-huber.com
*/

function wpab_load_plugin_textdomain() {
    load_plugin_textdomain( 'wp-autoblog', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'wpab_load_plugin_textdomain' );

include('class/SourceMetaProvider.php');
#include('class/OptionsProvider.php');
#include('class/Settings.php');

include('class/MetaBoxPostListRegisterHook.php');
include('class/MetaBoxRegisterHook.php');
include('class/SourcePostTypeRegisterHook.php');

include('class/job/FeedImporterJob.php');

?>