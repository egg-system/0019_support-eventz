<?php

require('support-reward.php');

class supportRewardTable  extends WP_List_Table {

    const DEFALUT_DISPLAY_MONTH = 6;

    private $startMonth;
    private $endMonth;

    public function __construct()
    {
        $this->endMonth = new DateTime('now');
        
        $defaultDisplayMonth = self::DEFALUT_DISPLAY_MONTH;
        
        $this->startMonth = clone $this->endMonth;
        $this->startMonth->modify("-{$defaultDisplayMonth} month");

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
            'id' => '会員ID',
            'member_name' => '会員',
            'member_created_at' => '登録月',
            'member_category_chage' => '区分変更',
            'member_category' => '区分（会員、代理店）',
        ];

        for ($month = clone $this->startMonth; $month <= $this->endMonth; $month->modify('+1 month')) {
            $columns[$month->format('Y-m')] = $month->format('Y年m月');
        }

        return $columns;
    }

    private $rowIndex = 0;

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
            $rewards = SupportReward::getRewards($this->startMonth, $this->endMonth);
            
            foreach ($rewards as $reward) {
                $this->items[] = $reward->toArray();
            }
        }
    }
}