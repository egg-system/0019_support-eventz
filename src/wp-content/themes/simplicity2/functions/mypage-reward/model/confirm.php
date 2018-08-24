<?php
namespace Reward\Model;

use Reward\Constant as Constant;
use Reward\Dao as Dao;

class Confirm
{
    // メンバーID
    private $membersId = null;
    // DBからデータを取得するオブジェクト
    private $dao = null;

    // テンプレートで使う変数
    public $price = 0;
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
        $this->price = $this->getParam();
        $result = $this->checkParam($this->price);
    }
    
    /**
     * パラメータの取得
     *
     * @return void
     */
    private function getParam()
    {
        return $_POST['price'];
    }
    
    /**
     * 引数のチェック
     *
     * @param int $price
     * @return boolean
     */
    private function checkParam($price)
    {
        // 未設定の場合はエラーメッセージは出さない
        if ($price === null || $price === '') {
            $this->error = "出金金額を入力して下さい。";
            return false;
        }
        
        // 数字以外はNG
        if (!ctype_digit($price)) {
            $this->error = "出金金額は数値を入れてください。";
            return false;
        }

        // 指定単位で入力されているか
        if ($price % Constant::OUTPUT_UNIT !== 0) {
            $this->error = "出金金額は" . number_format(Constant::OUTPUT_UNIT) . "円単位で入力して下さい。";
            return false;
        }
        
        // 最低出金金額より少ない場合はエラー
        if ($price < Constant::MINIMUM_OUTPUT_PRICE) {
            $this->error = "出金金額は" . number_format(Constant::MINIMUM_OUTPUT_PRICE) . "円以上を入力して下さい。";
            return false;
        }
        
        $membersId = $this->getMembersId();
        $totalPrice = $this->dao->getTotalRewardPrice($membersId);
        // 自分の最大報酬金額以上の入力の場合はNG
        if ($price > $totalPrice) {
            $this->error = "出金できる金額は最大" . number_format($totalPrice) . "円です。";
            return false;
        }
    }

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
}
