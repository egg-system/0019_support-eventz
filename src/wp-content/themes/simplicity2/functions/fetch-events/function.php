<?php 

if (!class_exists('TopPageEventsProvider')) {
  	require_once('top-page-events-provider.php');
} 

add_actions('init', 'get_top_pege_events_provider');
function get_top_pege_events_provider() {
	if (!is_home() || !is_front_page()) {
		return;
	}

	$top_page_events_provider = new TopPageEventsProvider();
	add_actions('pre_get_posts', [&$top_page_events_provider, 'get_posts']);
} 
function get_top_page_events() {
	$top_page_events_ids = 
	
	add_filter('the_post', [&$top_page_events_ids, 'the_post']);
}