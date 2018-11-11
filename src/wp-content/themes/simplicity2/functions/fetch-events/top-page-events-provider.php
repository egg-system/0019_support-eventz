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
			$is_premium = $post_meta['term_id'] === self::PLEMIUM_EVENT_TERM_ID;
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

	private function classify_post_metas($post_meta)
	{
		switch ($post_meta['term_id']) {
			case self::PLEMIUM_EVENT_TERM_ID:
				$max_show = self::MAX_SHOW_PLEMIUM_EVENT;
				$array = &$this->premium_event_metas;
				break;

			case self::CAFE_EVENT_TERM_ID:
				$max_show = self::MAX_SHOW_CAFE_EVENT;
				$array = &$this->cafe_event_metas;
				break;

			case self::SEMINER_EVENT_TERM_ID:
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
}