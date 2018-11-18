<?php

if (!class_exists('SupportEventQuery')) {
	require_once('support-event-query.php');
}

class TopPageEventsProvider
{
	const MAX_SHOW_PLEMIUM_EVENT = 100;

	const MAX_SHOW_CAFE_EVENT = 30;

	const MAX_SHOW_SEMINER_EVENT = 50;

	private $premium_event_metas = [];

	private $cafe_event_metas = [];

	private $seminer_event_metas = [];

	public function __construct()
	{
		global $wpdb;
		$query = new SupportEventQuery();

		$post_metas = $wpdb->get_results($query->get_query(), ARRAY_A);
		if (empty($post_metas)) {
			return;
		}

		foreach ($post_metas as $post_meta) {
			$is_premium = $post_meta['term_id'] === PLEMIUM_EVENT_TERM_ID;
			$in_two_week = $post_meta['event_date'] <= $query->two_week_later_time;
			
			if ($is_premium || $in_two_week) {
				$this->classify_post_metas($post_meta);
			}
		}
	}

	public function set_query_conditions($query)
	{
		$post_ids = $this->get_post_ids();
		if (empty($post_ids)) {
			add_filter('posts_pre_query', '');
			return;		
		}

		$query->set('post_type', 'event');
		$query->set('post__in', $this->get_post_ids());
		$query->set('posts_per_page', -1);
	}

	private function get_post_ids() 
	{
		return array_merge(
			array_keys($this->premium_event_metas),
			array_keys($this->cafe_event_metas),
			array_keys($this->seminer_event_metas)
		);
	}

	private function get_event_metas()
	{
		// キーの重複がない、かつindexの振り直しを避けるため、+演算子で結合
		return $this->premium_event_metas
			+ $this->cafe_event_metas
			+ $this->seminer_event_metas;
	}

	private function classify_post_metas($post_meta)
	{
		switch ($post_meta['term_id']) {
			case PLEMIUM_EVENT_TERM_ID:
				$max_show = self::MAX_SHOW_PLEMIUM_EVENT;
				$array = &$this->premium_event_metas;
				break;

			case CAFE_EVENT_TERM_ID:
				$max_show = self::MAX_SHOW_CAFE_EVENT;
				$array = &$this->cafe_event_metas;
				break;

			case SEMINER_EVENT_TERM_ID:
				$max_show = self::MAX_SHOW_SEMINER_EVENT;
				$array = &$this->seminer_event_metas;
				break;

			default: 
				return;
		}

		if (count($array) === $max_show) {
			return;
		}

		$post_id = $post_meta['post_id'];
		$array[$post_id] = $post_meta;
	}

	public function posts_results($posts)
	{
		if (empty($posts)) {
			return;
		}
	
		if ($posts[0]->post_type !== 'event') {
			return $posts;
		}

		usort($posts, [&$this, 'compare_events']);
		return $posts;
	}

	private function compare_events($post, $nextPost)
	{
		$event_metas = $this->get_event_metas();
		$post_meta = $event_metas[$post->ID];
		$next_post_meta = $event_metas[$nextPost->ID];

		if ($post_meta['term_id'] === $next_post_meta['term_id']) {
			$post_date = $post_meta['event_date'];
			$next_post_date = $next_post_meta['event_date'];

			return $post_date > $next_post_date ? 1 : -1;
		}

		return $post_meta['term_id'] > $next_post_meta['term_id'] ? 1 : -1;
	}

	public function have_events($arg)
	{
		$event_ids = $this->get_post_ids();
		return count($event_ids) > 0;
	}

	public function get_event_type($post_id)
	{
		$event_metas = $this->get_event_metas();
		if (array_key_exists($post_id, $event_metas)) {
			return intval($event_metas[$post_id]['term_id']);
		}

		return null;
	}
}