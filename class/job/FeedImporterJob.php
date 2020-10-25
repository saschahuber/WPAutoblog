<?php

include('importer/WordpressFeedImporterJob.php');

class WPAB_FeedImporterJob{
    /**
     * Start up
     */
    public function __construct(){
		add_filter( 'cron_schedules', array($this, 'add_cron_schedules') );

		if ( ! wp_next_scheduled( 'wpab_importer_cron_action' ) ) {
			wp_schedule_event( time(), 'every_15_mins', 'wpab_importer_cron_action' );
		}
		
		add_action( 'wpab_importer_cron_action', array($this, 'import'));
    }
	
	public function import(){
		$importers = array(
			new WPAB_WordpressFeedImporterJob()
		);
		
		foreach($importers as $importer){
			$importer->startImport();
		}
	}
	
	function add_cron_schedules( $schedules ) {
		$schedules['every_15_mins'] = array(
			'interval' => 900, // 15 Minutes in Seconds
			'display'  => __( 'Every 15 Minutes', 'wp-autoblog'),
		);
	 
		return $schedules;
	}
}

$wpabFeedImporterJob = new WPAB_FeedImporterJob();

?>