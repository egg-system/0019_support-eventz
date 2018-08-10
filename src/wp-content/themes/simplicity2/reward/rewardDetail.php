<?php

class RewardDetail
{
    // 最大期間
    const MAX_TERM = 6;

    // ワードプレスのグローバル変数
    private $wpdb;
    private $tablePrefix;

    // テンプレートで使う変数
    public $start = "";
    public $end = "";
    public $results = [];
    public $allMonth = [];
    public $inputData = [];
    public $outputData = [];
    public $error = "";

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
        $this->setParam();
        $this->results = $this->getRewardData($this->start, $this->end);
        $this->setInputOutput($this->results);
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
            // エラーの場合はデフォル値をセット
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
        error_log($start."\n", 3, "/tmp/hikaru_error.log");
        error_log(gettype($start)."\n", 3, "/tmp/hikaru_error.log");
        error_log($end."\n", 3, "/tmp/hikaru_error.log");
        error_log(gettype($end)."\n", 3, "/tmp/hikaru_error.log");

        // 未設定の場合はエラーメッセージは出さない
        if ($start === null && $end === null) {
            return false;
        }

        // 数字以外はNG
        if (!ctype_digit($start) || !ctype_digit($end)) {
            $this->error = "開始と終了は数値を入れてください。";
            return false;
        }

        // 指定の長さ以外はNG
        if (strlen($start) !== 6 || strlen($end) !== 6) {
            $this->error = "開始と終了はYYYYMMの形式で入れてください。(例：201801)";
            return false;
        }

        // 日付の妥当性をチェック
        $startYear = substr($start, 0, 4);
        $startMonth = substr($start, 4, 2);
        $endYear = substr($end, 0, 4);
        $endMonth = substr($end, 4, 2);
        $day = '01';
        if (!checkdate($startMonth, $day, $startYear) || !checkdate($endMonth, $day, $endYear)) {
            $this->error = "開始と終了が存在しない日付です。";
            return false;
        }

        // 現在よりも先を設定したらNG
        if ($end > date("Ym")) {
            $this->error = "終了に未来の日付は設定できません。";
            return false;
        }

        // 最大期間より長い場合はNG
        $allMonth = $this->getMonth($end);
        if (!in_array($start, $allMonth)) {
            $this->error = "表示できる期間は最大${term}ヶ月です。";
            return false;
        }

        // 最大期間よりも短く設定してる場合は全体の期間を短くする
        $monthArray = [];
        // 終了月からループさせる
        rsort($allMonth);
        foreach ($allMonth as $month) {
            $monthArray[] = $month;
            // 開始月以前の月は無視する
            if ($start === $month) {
                break;
            }
        }
        // 開始月からに並び替え
        asort($monthArray);
        $this->allMonth = $monthArray;

        return true;
    }

    /**
     * 期間の取得
     *
     * @param int $end
     * @return array $allMonth
     */
    private function getMonth($end = null)
    {
        // 取得する期間
        if ($end === null) {
            // デフォルトは現在月
            $end = date("Ym");
        }
        // 1日を足してYYYYMMDDにする
        $maxDay = $end . "01";
        // 最大期間
        $term = self::MAX_TERM;
        // 最小月
        $minMonth = date("Ym", strtotime("${maxDay} -${term} month"));
        
        // ループして期間の全ての月を出す
        $allMonth = [];
        for ($i = 0; $i < $term; $i++) {
            $allMonth[] = date("Ym", strtotime("${maxDay} -${i} month"));
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
