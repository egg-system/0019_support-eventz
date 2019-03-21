<?php
namespace AutoReg;

use AutoReg\Constant as Constant;

require_once(__DIR__ . "/../constant.php");

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
  public static function msgPaymentErrLog($paymentType, $memberInfo, $email, $money, $rel, $ipAddr) {
      $msgParams = $email . " 会員情報:" . var_export($memberInfo , true). " 月額:" . $money . " 結果可否(rel):" . $rel . " IPアドレス:" . $ipAddr;
      $text = "";
      $env = '本番';
      if ($paymentType == Constant::CONTINUE_PAY) {
        $text = $env . ' [WARN]継続決済でNG : user_mail ' . $msgParams;
      } else {
        $text = $env . ' [WARN]初回決済でNG : user_mail ' . $msgParams;
      }
      self::_sendSlackMsg($text);
  }


  /**
   * DAO決済NG通知
   *
   * @return void
   */
  public static function msgDaoErrLog($email, $memberInfo, $errReason) {
    $text = self::getParamsMsg($email, $memberInfo, $errReason);
    self::_sendSlackMsg($text);
  }


  /**
   * IPアドレスNG通知
   *
   * @return void
   */
  public static function msgIpErrLog($email, $ipAddr) {
      $text = "[WARN]IPアドレスエラー:" . $ipAddr . " user_mail:" . $email;
      self::_sendSlackMsg($text);
  }


  /**
   * 決済成功
   *
   * @return void
   */
  public static function msgPaymentSucceedLog($email, $memberInfo, $msg) {
      $text = self::getParamsMsg($email, $memberInfo, $msg);
      self::_sendSlackMsg($text);
  }


   /**
   * Slack Web API
   *
   * @return void
   */
  private static function getParamsMsg($email, $memberInfo, $msg) {
    $name = $memberInfo['kanji'] . ' ' . $memberInfo['kana'];
    $member_id = $memberInfo['member_id'];
    $level = $memberInfo['level'];
    $account_state = $memberInfo['account_state'];
    $payment_date = $memberInfo['payment_date'];
    $introducer_id = $memberInfo['introducer_id'];
    $introducer_level = $memberInfo['introducer_level'];
    $env = '本番';
    $text = $env . ' ' . $msg . ': member_id:' . $member_id . ' ,email:' . $email . ' ,level:' .$level . ' ,name:' . $name . ' ,state:' . $account_state . ' ,date:' . $payment_date . ' ,introducer_id:' . $introducer_id . ' ,introducer_level:' . $introducer_level;
    return $text;
  }


  /**
   * Slack Web API
   *
   * @return void
   */
  private static function _sendSlackMsg($msg) {
      // bot
      $slackApiKey = Constant::BOT_TOKEN;
      $text = urlencode($msg);
      $chName = '0019_support_log';
      $url = "https://slack.com/api/chat.postMessage?token=${slackApiKey}&channel=%23${chName}&text=${text}";
      file_get_contents($url);
  }

} // end of class


?>
