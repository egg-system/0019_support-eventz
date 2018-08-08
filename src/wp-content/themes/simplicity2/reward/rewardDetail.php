<?php

class RewardDetail
{
    // 最大期間
    const MAX_TERM = 6;

    // ワードプレスのグローバル変数
    private $wpdb;
    private $tablePrefix;

    // テンプレートで使う変数
    public $start;
    public $end;
    public $results;
    public $allMonth;
    public $inputData;
    public $outputData;
    public $error;

    /**
     * コンストラクタ
     *
     * @param object $wpdb
     * @param string $tablePrefix
     * @return void
     */
    public function __construct($wpdb, $tablePrefix)
    {
        $this->wpdb = $wpdb;
        $this->tablePrefix = $tablePrefix;
    }

    /**
     * メイン処理(テンプレートに必要なデータのセット)
     *
     * @return void
     */
    public function exec()
    {
        $this->allMonth = $this->getMonth();
        $this->setParam();
        //error_log($this->start."\n", 3, "/tmp/hikaru_error.log");
        //error_log($this->end."\n", 3, "/tmp/hikaru_error.log");
        $this->results = $this->getRewardData($this->start, $this->end);
        $this->setInputOutput($this->results);
        error_log(print_r($this->results,true)."\n", 3, "/tmp/hikaru_error.log");
        error_log(print_r($this->inputData,true)."\n", 3, "/tmp/hikaru_error.log");
        error_log(print_r($this->outputData,true)."\n", 3, "/tmp/hikaru_error.log");

    }

    /**
     * パラメータのセット
     *
     * @return void
     */
    private function setParam()
    {
        // パラメータの取得
        $this->start = $_GET['start'];
        $this->end = $_GET['end'];
        
        $check = $this->checkParam($this->start, $this->end);
        if (!$check) {
            $this->error = "開始と終了が不正です";
            // エラーの場合はデフォル値をセット
            // TODO:getMonthを2回呼んでる
            $this->allMonth = $this->getMonth();
            $this->start = $this->allMonth[self::MAX_TERM - 1];
            $this->end = $this->allMonth[0];
        }
    }

    /**
     * 引数のチェック
     *
     * @param int $start
     * @param int $end
     * @return boolean
     */
    private function checkParam($start, $end)
    {
        if (strlen($start) !== 6 && strlen($end) !== 6) {
            return false;
        }

        return true;
    }

    /**
     * 期間の取得
     *
     * @return void
     */
    private function getMonth()
    {
        // 取得する期間
        $maxMonth = date("Ym");
        $term = self::MAX_TERM;
        $minMonth = date("Ym",strtotime("-${term} month"));
        // ループして期間の全ての月を出す
        $allMonth = [];
        for ($i = 0; $i < $term; $i++) {
            $allMonth[] = date("Ym",strtotime("-${i} month"));
        }
        // 月を昇順でソート
        asort($allMonth);

        return $allMonth;
    }

    /**
     * DBから報酬データの取得
     *
     * @param int $start
     * @param int $end
     * @return array $results
     */
    private function getRewardData($start, $end)
    {
        // 必要なテーブルの定義
        $rewardDetailsTable = $this->tablePrefix . "reward_details";
        $membersTable = $this->tablePrefix . "swpm_members_tbl";
        $memberShipTable = $this->tablePrefix . "swpm_membership_tbl";
        
        // メンバーIDの取得
        $id = SwpmMemberUtils::get_logged_in_members_id();

        $bindSql = <<<SQL
SELECT rd.id,
       DATE_FORMAT(rd.date, '%Y%m') as date,
       rd.price,
       me.member_id,
       me.first_name,
       ms.alias
FROM ${rewardDetailsTable} rd
LEFT JOIN ${membersTable} me
    ON rd.introducer_id = me.member_id 
LEFT JOIN ${memberShipTable} ms
    ON rd.level = ms.id 
WHERE rd.member_id = %d
AND DATE_FORMAT(rd.date, '%Y%m') >= ${start}
AND DATE_FORMAT(rd.date, '%Y%m') <= ${end}
ORDER BY rd.date
SQL;
        $sql = $this->wpdb->prepare($bindSql, $id);
        error_log($sql."\n", 3, "/tmp/hikaru_error.log");
        $results = $this->wpdb->get_results($sql, ARRAY_A);

        return $results;
    }

    /**
     * 入金出金データをセット
     *
     * @param array $results
     * @return void
     */
    private function setInputOutput($results)
    {
        $inputData = [];
        $outputData = [];

        // 早期リターン
        if (empty($results)) {
            $this->inputData = $inputData;
            $this->outputData = $outputData;
            return true;
        }

        foreach ($results as $record) {
            // 入金データ
            if ($record['price'] > 0) {
                // 紹介者IDと月ごとでまとめる
                $inputData[$record['member_id']][$record['date']] = $record;

                // 1番最初のデータが1番はじめに登録されたデータなのでそれを代表のデータとしてまとめる
                if (!isset($inputData[$record['member_id']][0])) {
                    $inputData[$record['member_id']][0] = $record;
                }
            // 出金データ
            } else {
                // 月ごとでまとめる
                if (!isset($outputData[$record['date']])) {
                    $outputData[$record['date']] = $record['price'];
                } else {
                    $outputData[$record['date']] += $record['price'];
                }
            }
        }
        $this->inputData = $inputData;
        $this->outputData = $outputData;
    }
}
