<?php
namespace Reward\Model;

use Reward\Constant as Constant;
use Reward\Dao as Dao;

class Detail
{
    // メンバーID
    private $membersId = null;
    // DBからデータを取得するオブジェクト
    private $dao = null;

    // テンプレートで使う変数
    public $start = "";
    public $end = "";
    public $results = [];
    public $allMonth = [];
    public $inputData = [];
    public $outputData = [];
    public $totalPrice = 0;
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
        $this->dao = new Dao($wpdb, $tablePrefix);
    }

    /**
     * メイン処理(テンプレートに必要なデータのセット)
     *
     * @return void
     */
    public function exec()
    {
        $this->setParam();

        // メンバーIDの取得
        $membersId = \SwpmMemberUtils::get_logged_in_members_id();

        $this->results = $this->dao->getRewardData($this->start, $this->end, $membersId);
        $this->setInputOutput($this->results);
        $this->totalPrice = $this->dao->getTotalRewardPrice($membersId);
        
    }

    /**
     * パラメータのセット
     *
     * @return void
     */
    private function setParam()
    {
        // パラメータの取得
        $this->start = isset($_GET['start']) ? $_GET['start'] : null;
        $this->end = isset($_GET['end']) ? $_GET['end'] : null;

        $check = $this->checkParam($this->start, $this->end);
        if (!$check) {
            // エラーの場合はデフォル値をセット
            $this->allMonth = $this->getMonth();
            $this->start = $this->allMonth[Constant::MAX_TERM - 1];
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

        // 現在よりも未来を設定したらNG
        if ($end > date("Ym")) {
            $this->error = "終了に未来の日付は設定できません。";
            return false;
        }

        // 最大期間より長い場合はNG
        $allMonth = $this->getMonth($end);
        if (!in_array($start, $allMonth)) {
            $this->error = "表示できる期間は最大" . Constant::MAX_TERM ."ヶ月です。";
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
        $term = Constant::MAX_TERM;
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
<<<<<<< Updated upstream
=======

    /**
     * 個人のIDを取得
     *
     * @return int $membersId
     */
    private function getMembersId()
    {
        // メンバーIDの取得
        if ($this->membersId === null) {
            $this->membersId = \SwpmMemberUtils::get_logged_in_members_id();
        }

        return $this->membersId;
    }
>>>>>>> Stashed changes
}
