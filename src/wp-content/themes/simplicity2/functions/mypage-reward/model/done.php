<?php
namespace Reward\Model;

use Reward\Constant as Constant;
use Reward\Dao as Dao;

class Done
{
    // メンバーID
    private $membersId = null;
    // DBからデータを取得するオブジェクト
    private $dao = null;

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
        error_log("done exec\n", 3, "/tmp/hikaru_error.log");
        $price = $this->getParam();
        error_log($price . "\n", 3, "/tmp/hikaru_error.log");
        $membersId = $this->getMembersId();
        // 出金データの登録
        $result = $this->dao->insertOutput($membersId, -$price);
        error_log(print_r($result,true)."\n", 3, "/tmp/hikaru_error.log");
        error_log(gettype($result)."\n", 3, "/tmp/hikaru_error.log");
        if ($result !== 0) {
            // 出金申請完了のメールを送る
            $this->sendDoneMail($price);
        }
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
    
    /**
     * 完了のメールを送る
     *
     * @param int $price
     * @return void
     */
    private function sendDoneMail($price)
    {
        // メールアドレスの取得
        $email = \SwpmMemberUtils::get_member_field_by_id($this->membersId, 'email');

        // メールの内容
        $subject = '出金申請完了しました';
        $message = "出金申請が完了しました。\n出金金額：${price}";

        // メール送信
        $result = wp_mail($email, $subject, $message);
        error_log(print_r($result,true)."\n", 3, "/tmp/hikaru_error.log");
    }
}
