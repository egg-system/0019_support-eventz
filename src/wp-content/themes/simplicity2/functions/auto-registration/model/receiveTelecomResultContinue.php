<?php
namespace AutoReg\Model;

use AutoReg\Constant as Constant;
use AutoReg\Dao as Dao;
use AutoReg\AutoRegUtils as AutoRegUtils;
use AutoReg\AutoRegLog as AutoRegLog;

require_once(__DIR__ . '/../constant.php');
require_once(__DIR__ . '/../lib/dao.php');
require_once(__DIR__ . '/../lib/autoRegUtils.php');
require_once(__DIR__ . '/../log/autoRegLog.php');

/**
 * 初回決済クラス
 *
 */
class ReceiveTelecomResultContinue {

  // DBからデータを取得するオブジェクト
  private $dao = null;

  // テンプレートで使う変数
  public $email = "";
  public $money = "";
  public $rel = "";


  /**
   * コンストラクタ
   *
   * @param object $wpdb
   * @param string $tablePrefix
   * @return void
   */
  public function __construct($wpdb, $tablePrefix) {
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

    // 継続決済成功後、紹介者報酬登録へ
    // 会員取得
    $memberInfo = $this->dao->getMember($this->email);
    if ($this->_isPaymentSucceed($isTelecomAccess)) {

      // 報酬テーブルに登録
      $insResult = $this->dao->insertIntroducedReward($this->email, $memberInfo);
      if (false === $insResult) {
        return;
      }

      // 継続決済実行日を登録
      $updResult = $this->dao->updatePaymentDate($this->email, $memberInfo);
      if (false === $updResult) {
        return;
      }

      // 会員レベルが未決済の状態なら、決済会員レベルに復活
      $updResult = $this->dao->updateMembershipLevel($this->email, $memberInfo);
      if (false === $updResult) {
        return;
      }

    // 継続決済失敗時の処理
    } else {

      // Slack API 通知
      AutoRegLog::msgPaymentErrLog(Constant::CONTINUE_PAY, $memberInfo['level'], $this->email, $this->rel, $this->ipAddr);
      if (!array_key_exists('level', $memberInfo) || is_null($memberInfo['level'])) {
         return;
      }

      // 会員レベルを未決に戻し、stateをinactiveにする
      $updResult = $this->dao->updateMembershipLevelReturns($this->email, $memberInfo)
      if (false === $updResult) {
        return;
      }

      return;
    }

  }


  // /**
  //  * 継続決済成功判定
  //  *
  //  * @return boolean
  //  */
  // private function _isPaymentError($isTelecomAccess, $email, $money, $rel) {
  //     return $isTelecomAccess && isset($email) && isset($money) && $rel == 'no';
  // }


  /**
   * 決済成功判定
   *
   * @return boolean
   */
  private function _isPaymentSucceed($isTelecomAccess) {
      return $isTelecomAccess && isset($this->email) && isset($this->rel) && isset($this->money) && $this->rel == 'yes';
  }


} // end of class

?>
