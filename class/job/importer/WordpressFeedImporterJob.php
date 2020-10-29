<?php

class WPAB_WordpressFeedImporterJob{
    /**
     * Start up
     */
    public function __construct(){
    }
	
	public function import(){
		$feeds = $this->get_feeds_by_type('wordpress_feed');
		foreach($feeds as $feed){
			$this->import_from_feed($feed);
		}
	}
	
	public function get_feeds_by_type($type){
		$args = array(
			'numberposts' => -1,
			'post_type' => 'autoblog-source',
			'meta_query' => array(
				array(
					'key' => 'wpab_source_type',
					'value' => $type,
					'comapare' => '='
				),
				array(
					'key' => 'wpab_source_is_active',
					'value' => 'on',
					'comapare' => '='
				)
			)
		);
		return get_posts( $args );
	}
	
	public function get_keywords($feed_source){
		$keyword_data = get_post_meta($feed_source->ID, 'wpab_source_keywords', true);
		$keywords = array();
		foreach(explode(';', $keyword_data) as $data_item){
			$keyword = trim($data_item);
			if(!empty($keyword)){
				array_push($keywords, $keyword);
			}
		}
		return $keywords;
	}
	
	public function import_from_feed($feed_source){
		$feed_url = get_post_meta($feed_source->ID, 'wpab_source_feed_url', true);
		$feed_items = $this->get_feed($feed_url);
		
		$keywords = $this->get_keywords($feed_source);
		
		foreach($feed_items as $feed_item){
			if(!$this->post_exists($feed_item['link'])){
				if(empty($keywords) || $this->contains_keywords($feed_item, $keywords)){
					$this->create_post($feed_item, $feed_source);
				}
			}
		}
	}
	
	/**
	 *Checks if the item's content contains at least one of the given keywords (case insensitive)
	*/
	public function contains_keywords($feed_item, $keywords){
		$content = $feed_item['content'];
		foreach($keywords as $keyword){
			if(stripos($content, $keyword) !== false){
				return true;
			}
		}
		return false;
	}
	
	function get_feed($feed_url){		
		$response = wp_remote_get($feed_url);
		$content = wp_remote_retrieve_body($response);

		print_r($body);

		$x = new SimpleXmlElement($content);
		
		$posts = array();
		
		if(isset($x->channel)){
			foreach($x->channel->item as $entry) {		
				$title = (string) $entry->title;
				$link = (string) $entry->link;
				//$content = (string) $entry->encoded;
				$content = (string) $entry->children("content", true);
				
				$date = date_create_from_format('D, d M Y H:i:s O', $entry->pubDate);
				
				$item = array(
					'title' => $title,
					'link' => $link,
					'content' => $content,
					'date' => $date->getTimeStamp()
				);
				array_push($posts, $item);
			}
		}
		else if(isset($x->entry)){		
			foreach($x->entry as $entry) {		
				$title = (string) $entry->title;
				$link = (string) $entry->id;
				$content = (string) $entry->summary;
				$date = strtotime($entry->published);
				
				$item = array(
					'title' => $title,
					'link' => $link,
					'content' => $content,
					'date' => $date,
				);		
				array_push($posts, $item);
			}
		}
		return $posts;
	}

	
	function create_post($feed_item, $feed_source){	
		$content = $feed_item['content'];		
		
		$post_type = get_post_meta($feed_source->ID, 'wpab_source_post_type', true);
		
		$auto_publish = get_post_meta($feed_source->ID, 'wpab_source_auto_publish', true);
		$credit_message = get_post_meta($feed_source->ID, 'wpab_source_credit_message', true);
		$links_noindex = get_post_meta($feed_source->ID, 'wpab_source_links_noindex', true);
		$keywords = get_post_meta($feed_source->ID, 'wpab_source_keywords', true);
		
		$post_status = $auto_publish ? 'publish' : 'pending';
		
		$author_id = get_post_meta($feed_source->ID, 'wpab_source_author_id', true);
		
		if($author_id == 'null'){
			$author_id = null;
		}
		
		if($credit_message){
			$content .= '<br><br>'.$credit_message;
		}
		
		if($links_noindex){
			#$content = $this->save_rseo_nofollow($content)
		}
		$date = date('Y-m-d H:i:s', $feed_item['date']);
		
		
		$content = apply_filters('wpab_imported_content' , $content);
		
		$post = array(
			'post_title' => $feed_item['title'],
			'post_content' => $content,
			'post_type' => $post_type,
			'post_status' => $post_status,
			'post_author' => $author_id,
			'post_date' => $date,
			'post_date_gmt' => get_gmt_from_date($date),
			'meta_input' => array(
				'wpab_imported_post_url' => $feed_item['link']
			)
		);
		$post_id = wp_insert_post($post);
	}
	
	function save_rseo_nofollow($content) {
		$content = preg_replace_callback('~<(a\s[^>]+)>~isU', array($this, 'replace_link_nofollow'), $content);
		return $content;
	}

	function replace_link_nofollow($match) { 
		list($original, $tag) = $match;
		$my_folder =  "/";
		$blog_url = get_site_url();

		if (strpos($tag, "nofollow")) {
			return $original;
		}
		elseif (strpos($tag, $blog_url) && (!$my_folder || !strpos($tag, $my_folder))) {
			return $original;
		}
		else {
			return "<$tag rel='nofollow'>";
		}
	}
	
	function post_exists($external_url){
		$args = array(
		   'meta_query' => array(
			   array(
				   'key' => 'wpab_imported_post_url',
				   'value' => $external_url,
				   'compare' => '='
			   )
		   ),
		   'post_status' => array('publish', 'draft', 'trash', 'pending', 'auto-draft')
		);
		$query = new WP_Query($args);
		return $query->have_posts();
	}
}

?>