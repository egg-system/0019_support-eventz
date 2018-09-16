<?php

require('support-reward.php');

class supportRewardTable  extends WP_List_Table {
    
    private $date;
    private $isOnlyWithdrawal;

    public function __construct()
    {
        $this->date = empty($_GET['search_date']) ? null : $_GET['search_date'];
        $this->isOnlyWithdrawal = $_GET['is_only_withdrawal'] === 'ON';
		
        parent::__construct([
			'plural' => 'support-rewards',
			'screen' => isset( $args['screen'] ) ? $args['screen'] : null,
        ]);
    }

	protected function get_primary_column_name() {
        return 'id';
    }

    public function get_columns()
    {
        $columns = [
            'member_id' => '会員ID',
            'member_name' => '会員氏名',
            'introducer_id' => '紹介して加入した人の会員ID',
            'introducer_name' => '加入した会員氏名',
            'date' => '年月日',
			'introducer_category' => '紹介して加入した人の会員レベル',
            'price' => '入出金金額',
        ];

        return $columns;
    }

    public function column_default($item, $column)
    {
        if (array_key_exists($column, $item)) {
            return $item[$column];
        }

        return null;
    }

    public function prepare_items($items = null)
    {
        if (is_null($items)) {
            $rewards = SupportReward::getRewards(
                $this->date, 
                $this->isOnlyWithdrawal
            );
			
            foreach ($rewards as $reward) {
                $this->items[] = $reward->toArray();
            }
        }
    }
	
	public function getCsvItems() {
		$resultCsvItems = [];
		
		foreach ($this->items as $item) {
			$itemValues = array_values($item);
			$resultCsvItems[] = implode($itemValues, ',');
		}
		
		return $resultCsvItems;
	}
    
    public function toCsvArray()
    {
        $resultCsvArray = $this->getCsvItems();
		
		$header = implode($this->get_columns(), ',');
        array_unshift($resultCsvArray, $header);
		
		return $resultCsvArray;
    }
}