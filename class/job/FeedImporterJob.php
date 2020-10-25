<?php

include('importer/WordpressFeedImporterJob.php');

class WPAB_FeedImporterJob{
    /**
     * Start up
     */
    public function __construct(){}
	
	public function import(){
		$importers = array(
			new WPAB_WordpressFeedImporterJob()
		);
		
		foreach($importers as $importer){
			$importer->import();
		}
	}
}

function wpab_import_job(){
	$wpab_feed_importer_job = new WPAB_FeedImporterJob();
	$wpab_feed_importer_job->import();
}

function wpab_add_cron_schedules( $schedules ) {
    $schedules['every_15_mins'] = array(
        'interval' => 900,
		'display'  => __( 'Every 15 Minutes', 'wp-autoblog'),
    );
    return $schedules;
}
add_filter( 'cron_schedules', 'wpab_add_cron_schedules' );

if ( ! wp_next_scheduled( 'wpab_do_import_job' ) ) {
    wp_schedule_event( time(), 'every_15_mins', 'wpab_do_import_job' );
}
add_action( 'wpab_do_import_job', 'wpab_import_job' );

?>