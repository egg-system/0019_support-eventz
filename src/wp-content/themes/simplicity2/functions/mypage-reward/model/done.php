<?php
namespace Reward\Model;

use Reward\Constant as Constant;
use Reward\Dao as Dao;

class Done
{
    // DBからデータを取得するオブジェクト
    private $dao = null;
    
    // テンプレートで使う変数
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
        $price = $this->getParam();
        
        $membersId = \SwpmMemberUtils::get_logged_in_members_id();
        
        // 出金データの登録
        $result = $this->insertOutput($membersId, $price);
        if ($result) {
            // 出金申請完了のメールを送る
            $mailDone = $this->sendDoneMail($membersId, $price);
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
     * 出金データの登録
     *
     * @param int $membersId
     * @param int $price
     * @return bool $result
     */
    private function insertOutput($membersId, $price)
    {
        // 出金データの登録
        $result = $this->dao->insertOutput($membersId, -$price);

        if ($result === 0) {
            $this->error = "出金申請に失敗しました。";
            return false;
        }

        return true;
    }
    
    /**
     * 完了のメールを送る
     *
     * @param int $membersId
     * @param int $price
     * @return bool $result
     */
    private function sendDoneMail($membersId, $price)
    {
        // 必要なユーザーデータの取得
        $memberInfo = $this->dao->getMemberInfo($membersId);

        // 本文に必要なデータ
        $name = $memberInfo['first_name'];
        $nowDate = date('Y/m/d');
        $price = number_format($price);
        $siteMail = Constant::SITE_MAIL;

        // メールの内容
        $subject = '[サポートイベント運営事務局]出金申請完了しました';
        $message = <<<TEXT
${name} 様

サポートイベント運営事務局です。

以下の内容で出金申請を受け付けました。

お振込まで、いましばらくお待ちください。


==============================
　出金申請内容
==============================
・出金申請日
${nowDate}

・出金申請額
${price}円


---
サポートイベント運営事務局
${siteMail}
TEXT;

        // メール送信
        $result = wp_mail($memberInfo['email'], $subject, $message);

        if ($result === false) {
            $this->error = "メールの送信に失敗しました。";
            return false;
        }

        return true;
    }
}
