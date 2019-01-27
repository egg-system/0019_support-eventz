<?php
namespace AutoReg\Model;

use AutoReg\Constant as Constant;
use AutoReg\Dao as Dao;
use AutoReg\AutoRegUtils as AutoRegUtils;
use AutoReg\AutoRegLog as AutoRegLog;
use AutoReg\Mail as Mail;

require_once(__DIR__ . '/../constant.php');
require_once(__DIR__ . '/../lib/dao.php');
require_once(__DIR__ . '/../lib/autoRegUtils.php');
require_once(__DIR__ . '/../log/autoRegLog.php');
require_once(__DIR__ . '/../lib/mail.php');


/**
 * 初回決済クラス
 *
 */
class ReceiveTelecomResult{

  // 入力フォームデータ
  private $formData = null;
  // DBからデータを取得するオブジェクト
  private $dao = null;

  // テンプレートで使う変数
  public $email = "";
  public $money = "";
  public $rel = "";
  public $ipAddr = "";


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
      $this->email = $_GET['email'];
      $this->money = $_GET['money'];
      $this->rel = $_GET['rel'];
      $this->ipAddr = $_SERVER["REMOTE_ADDR"];
  }


  /**
   * メイン処理
   *
   * @return void
   */
  public function exec() {

    //IPアドレスでテレコムからのアクセスであることを確認
    $isTelecomAccess = AutoRegUtils::isTelecomIpAccessed($this->ipAddr);
    if (!$isTelecomAccess) {
      echo('不正なアクセスです。');
      // Slackへ通知
      AutoRegLog::msgIpErrLog($this->email, $this->ipAddr);
      return;
    }

    if ($this->_isPaymentSucceed($isTelecomAccess, $this->email, $this->money, $this->rel)) {

      // 会員取得
      $memberInfo = $this->dao->getMember($this->email);
      if (!array_key_exists('introducer_id', $memberInfo) || is_null($memberInfo['introducer_id'])) {
         Mail::sendErrEmailRegistration($this->email, $memberInfo);
         AutoRegLog::msgDaoErrLog($this->email, $memberInfo, "[ERROR]初回決済エラー：会員取得処理失敗(WP登録メアドとテレコム登録メアドの相違、または存在しない紹介者IDの入力)");
         return;
      }

      // 会員レベルの更新
      $updResult = $this->dao->updateMembershipLevel($this->email, $memberInfo);
      if (false === $updResult) {
         AutoRegLog::msgDaoErrLog($this->email,  $memberInfo, "[ERROR]初回決済エラー：会員レベル更新処理失敗");
         return;
      }

      // 紹介者報酬登録
      $memberInfo = $this->dao->insertIntroducedReward($this->email);

      // 決済成功日更新
      $updResult = $this->dao->updatePaymentDate($this->email, $memberInfo);
      if (false === $updResult) {
         AutoRegLog::msgDaoErrLog($this->email,  $memberInfo, "[ERROR]決済日更新処理失敗");
         return;
      }

      // 初回決済完了メール
      Mail::sendInitPaymentMail($this->email, $memberInfo);
      echo('決済認証成功');

      // 初回決済成功通知
      AutoRegLog::msgPaymentSucceedLog($this->email, $memberInfo, "[INFO]初回決済処理成功");

    } else {
      // TODO emailがない時の通知

      // Slackへの通知
      $memberInfo = $this->dao->getMember($this->email);
      if (isset($this->email)) AutoRegLog::msgPaymentErrLog(Constant::FIRST_PAY, $memberInfo['level'], $this->email, $this->money, $this->rel, $this->ipAddr);

      // 初回決済エラーのお知らせメール
      Mail::sendPaymentErrMail($this->email, $memberInfo);

      echo('決済認証失敗');
    }
  }

  /**
   * 決済成功判定
   *
   * @return boolean
   */
  private function _isPaymentSucceed($isTelecomAccess, $email, $money, $rel) {
      return $isTelecomAccess && isset($email) && isset($rel) && isset($money) && $rel == 'yes';
  }

} // end of class

?>
