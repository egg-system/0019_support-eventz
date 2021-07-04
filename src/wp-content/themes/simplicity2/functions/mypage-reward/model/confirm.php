<?php
namespace Reward\Model;

use Reward\Constant as Constant;

class Confirm
{
    // DBからデータを取得するオブジェクト
    private $dao = null;

    // nonce
    private $nonce = "";

    // テンプレートで使う変数
    public $price = 0;
    public $error = "";

    /**
     * コンストラクタ
     *
     * @param object $dao
     * @return void
     */
    public function __construct($dao)
    {
        $this->dao = $dao;
    }

    /**
     * メイン処理(テンプレートに必要なデータのセット)
     *
     * @return void
     */
    public function exec()
    {
        $this->setParam();
        $this->checkParam($this->price, $this->nonce);
    }
    
    /**
     * パラメータのセット
     *
     * @return void
     */
    private function setParam()
    {
        $this->nonce = isset($_POST['nonce']) ? $_POST['nonce'] : '';
        $this->price = isset($_POST['price']) ? $_POST['price'] : '';
    }
    
    /**
     * 引数のチェック
     *
     * @param int $price
     * @param string $nonce
     * @return boolean
     */
    private function checkParam($price, $nonce)
    {
        // nonceのチェック
        if (!wp_verify_nonce($nonce, Constant::NONCE_DETAIL_PAGE)) {
            $this->error = "不正な遷移です。";
            return false;
        }

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
        
        $membersId = \SwpmMemberUtils::get_logged_in_members_id();
        $totalPrice = $this->dao->getTotalRewardPrice($membersId);
        // 自分の最大報酬金額以上の入力の場合はNG
        if ($price > $totalPrice) {
            $this->error = "出金できる金額は最大" . number_format($totalPrice) . "円です。";
            return false;
        }
    }
}
