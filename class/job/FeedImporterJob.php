<?php

include('importer/WordpressFeedImporterJob.php');

class WPAB_FeedImporterJob{
    /**
     * Start up
     */
    public function __construct(){
		$importer = array(
			new WPAB_WordpressFeedImporterJob()
		);
    }
}

$wp_autoblog_feed_importer_job = new WPAB_FeedImporterJob();

?>