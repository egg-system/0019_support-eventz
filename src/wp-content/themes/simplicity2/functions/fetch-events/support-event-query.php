<?php

class SuppotEventQuery {

	private $today_time;

	private $two_week_later_time;
	
	private $next_month_time;
	
	public function __constract()
	{
		$today = date("Y-m-d");
		$this->today_time = "{$today} 00:00:00";
	
		$two_week_later = date("Y-m-d",strtotime($today . "+2 week"));
		$this->two_week_later_time = "{$twoWeekLater} 23:59:59";
	
		$next_month = date("Y-m-d",strtotime($today . "+1 month"));
		$this->next_month_time = "${nextMonth} 23:59:59";
	}
	
	public function get_query()
	{
		$query = [];
		$query[] = '
			select 
				wp9_postmeta.post_id,
				wp9_term_taxonomy.term_id,
				wp9_postmeta.meta_key AS event_date
			FROM wp9_postmeta
			LEFT JOIN wp9_term_taxonomy
			ON wp9_postmeta.post_id = wp9_term_taxonomy.object_id
		';

		$query[] = $this->get_where_query();
		$query[] = 'ORDER BY wp9_term_taxonomy.term_id wp9_postmeta.meta_value';

		return implode(PHP_EOL, $query);
	}

	private function get_where_query()
	{
		return "
			WEHRE wp9_postmeta.meta_key = '_eventorganiser_schedule_start_start'
			AND wp9_postmeta.meta_velue BETWEEN '{$this->today_time}' AND '{$this->next_month_time}'
		";
	}
}

