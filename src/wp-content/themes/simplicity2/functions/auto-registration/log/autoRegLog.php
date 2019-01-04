<?php
namespace AutoReg;

use AutoReg\Constant as Constant;
use AutoReg\Dao as Dao;

require_once(__DIR__ . "/../constant.php");
require_once(__DIR__ . "/dao.php");

class AutoRegLog {

  /**
   * IPアドレスNG通知
   *
   * @return void
   */
  private static function msgIpErr($email, $ipAddr) {
      $text = "IPアドレスエラー:" . $ipAddr . " user_mail:" . $email;
      self::_sendSlackMsg($text);
  }


  /**
   * 決済NG通知
   *
   * @return void
   */
  public static function msgPaymentErrLog($paymentType, $level, $email, $rel, $ipAddr) {
      $msgParams = $email . " 会員レベル:" . $level . " 結果可否(rel):" . $rel . " IPアドレス" . $ipAddr;
      $text = "";
      if ($paymentType == Constant::CONTINUE_PAY) {
        $text = 'TEST 継続決済でNG : user_mail ' . $msgParams;
      } else {
        $text = 'TEST 初回決済でNG : user_mail ' . $msgParams;
      }
      self::_sendSlackMsg($text);
  }


  /**
   * 初回決済NG通知
   *
   * @return void
   */
  public static function initPaymentErrLog($email, $errReason) {
      $text = 'TEST 初回決済処理エラー:' .  $errReason . ' user_mail:' . $email;
      self::_sendSlackMsg($text);
  }


  /**
   * IPアドレスNG通知
   *
   * @return void
   */
  public static function msgIpErrLog($email, $ipAddr) {
      $text = "IPアドレスエラー:" . $ipAddr . " user_mail:" . $email;
      self::_sendSlackMsg($text);
  }


  /**
   * Slack Web API
   *
   * @return void
   */
  private function _sendSlackMsg($msg) {
      // bot
      $slackApiKey = Constant::BOT_TOKEN;
      $text = urlencode($msg);
      $chName = '0019_support_log';
      $url = "https://slack.com/api/chat.postMessage?token=${slackApiKey}&channel=%23${chName}&text=${text}";
      file_get_contents($url);
  }

} // end of class


?>
