<?php

require('support-reward-table.php');

class SupportRewardsController {
    private $rewardTable = null;

    public function __construct()
    {
        $this->rewardTable = new supportRewardTable();
    }
    
    public function index()
    {
        $rewardTable = $this->rewardTable;

        // todo：ぺジネーション処理を追加
        $rewardTable->prepare_items();
        include(dirname(__DIR__) . '/view/reward.php'); 
    }
}