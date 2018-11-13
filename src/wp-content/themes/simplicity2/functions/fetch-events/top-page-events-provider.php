<?php

if (!class_exists('SupportEventQuery')) {
	require_once('support-event-query');
}

class TopPageEventsProvider
{
	const MAX_SHOW_PLEMIUM_EVENT = 100;

	const MAX_SHOW_CAFE_EVENT = 30;

	const MAX_SHOW_SEMINER_EVENT = 50;

	private $premium_event_metas = [];

	private $cafe_event_metas = [];

	private $seminer_event_metas = [];

	public function __constract()
	{
		global $wpdb;
		$query = new SupportEventQuery();

		$post_metas = $wpdb->get_row($query->get_query(), ARRAY_A);
		foreach ($post_metas as $post_meta) {
			$is_premium = $post_meta['term_id'] === PLEMIUM_EVENT_TERM_ID;
			$in_two_week = $post_meta['event_date'] <= $this->two_week_later_time;
			
			if ($is_premium || $in_two_week) {
				$this->classify_post_metas($postmeta);
			}
		}
	}

	public function get_posts($query)
	{
		if (!$query->is_main_query()) {
			return;
		}

		$query->set('post_type', 'event');
		$query->set('post__in ', $this->get_post_ids());
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
		return array_merge(
			$this->premium_event_metas,
			$this->cafe_event_metas,
			$this->seminer_event_metas
		);
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

	public function posts_results($wp_query)
	{
		usort($wp_query->posts, [&$this, 'compare_events']);
	}

	private function compare_events($post, $nextPost)
	{
		$event_metas = $this->get_event_metas();
		$post_meta = $event_metas[$post->ID];
		$next_post_meta = $event_metas[$nextPost->ID];

		if ($post_meta['term_id'] === $next_post_meta['term_id']) {
			$post_date = $post_meta['_eventorganiser_schedule_start_start'];
			$next_post_date = $next_post_meta['_eventorganiser_schedule_start_start'];

			return $post_date < $next_post_date ? 1 : -1;
		}

		return $post_meta['term_id'] < $next_post_meta['term_id'] ? 1 : -1;
	}

	public function get_event_type($post_id)
	{
		$event_metas = $this->get_event_metas();
		if (array_key_exists($post_id, $event_metas)) {
			return $event_metas[$post_id]['term_id'];
		}

		return null;
	}
}