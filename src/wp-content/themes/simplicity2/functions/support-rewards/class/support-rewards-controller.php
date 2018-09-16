<?php

require('support-reward-table.php');

class SupportRewardsController {
    private $csvFilePath = null; 
    private $rewardTable = null;

    public function __construct()
    {
        $this->rewardTable = new supportRewardTable();
        $this->csvFilePath = dirname(__DIR__) . '/csv';
    }
    
    public function index()
    {
        $rewardTable = $this->rewardTable;

        // todo：ぺジネーション処理を追加
        $rewardTable->prepare_items();
		
        include(dirname(__DIR__) . '/view/reward.php'); 
    }
	
	public function export() {
        $this->rewardTable->prepare_items();
		
        $date = date('ymdHis');
        $fileName = "{$date}_reward.csv";
		
		header('Content-Type: text/csv');
		header("Content-Disposition: attachment; filename={$fileName}");
        
		$csvContent = implode($this->rewardTable->toCsvArray(), "\n");
		echo mb_convert_encoding($csvContent, "SJIS", "UTF-8");
		exit();
    }
}