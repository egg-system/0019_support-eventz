<?php 

// テスト環境、本番共通
const PLEMIUM_EVENT_TERM_ID = 174;

const CAFE_EVENT_TERM_ID = 250;

const SEMINER_EVENT_TERM_ID = 337;

if (!class_exists('TopPageEventsProvider')) {
  	require_once('top-page-events-provider.php');
} 

add_actions('init', 'get_top_pege_events_provider');
function get_top_pege_events_provider() {
	if (!is_home() || !is_front_page()) {
		return;
	}

	$top_page_events_provider = new TopPageEventsProvider();
	add_actions('posts_results', [&$top_page_events_provider, 'get_posts']);
	add_filter('the_post', [&$top_page_events_ids, 'the_post']);
	add_filter('get_event_type', [&$top_page_events_provider, 'get_event_type']);
}

function is_premium() {
	$term_id = apply_filters('get_event_type', get_the_ID());
	return $term_id === PLEMIUM_EVENT_TERM_ID;
}

function is_cafe() {
	$term_id = apply_filters('get_event_type', get_the_ID());
	return $term_id === CAFE_EVENT_TERM_ID;
}

function is_seminer() {
	$term_id = apply_filters('get_event_type', get_the_ID());
	return $term_id === SEMINER_EVENT_TERM_ID;
}