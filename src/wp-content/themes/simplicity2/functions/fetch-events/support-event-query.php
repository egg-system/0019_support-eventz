<?php

class SupportEventQuery {

	public $today_time;

	public $two_week_later_time;
	
	public $next_month_time;
	
	public function __construct()
	{
		$today = date("Y-m-d");
		$this->today_time = "{$today} 00:00:00";
	
		$two_week_later = date("Y-m-d", strtotime($today . "+2 week"));
		$this->two_week_later_time = "{$two_week_later} 23:59:59";
	
		$next_month = date("Y-m-d", strtotime($today . "+1 month"));
		$this->next_month_time = "${next_month} 23:59:59";
	}
	
	public function get_query()
	{
		$query = [];
		$query[] = "
			select 
				wp9_postmeta.post_id,
				wp9_term_relationships.term_taxonomy_id AS term_id,
				wp9_postmeta.meta_value AS event_date
			FROM wp9_postmeta
			LEFT JOIN wp9_term_relationships
			ON wp9_postmeta.post_id = wp9_term_relationships.object_id
			AND wp9_term_relationships.term_taxonomy_id in ({$this->get_term_ids()})
		";

		$query[] = $this->get_where_query();
		$query[] = 'ORDER BY wp9_term_relationships.term_taxonomy_id, wp9_postmeta.meta_value';

		return implode(PHP_EOL, $query);
	}

	private function get_term_ids() 
	{
		$term_ids = [
			PLEMIUM_EVENT_TERM_ID,
			CAFE_EVENT_TERM_ID,
			SEMINER_EVENT_TERM_ID,
		];
		return implode(',', $term_ids);
	}

	private function get_where_query()
	{
		return "
			WHERE wp9_postmeta.meta_key = '_eventorganiser_schedule_start_start'
			AND wp9_postmeta.meta_value BETWEEN '{$this->today_time}' AND '{$this->next_month_time}'
		";
	}
}

