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

  /**
   * CSV出力
   * @param $csvFileName
   * @param $outputArray
   * @param $resultString
   *
   * @return
  */
  public static function outputMemberInfoCSV($csvFileName, $memberInfo, $resultString, $isContinue) {
      $outputString = "";
      // ファイルがまだ生成されていなければ、先頭の文字を入れる
      $csvFileContents = file_get_contents($csvFileName);
      if (empty($csvFileContents)) {
          $outputString .= "会員ID,氏名,紹介者ID,会員レベル,会員レベル(名称),決済結果,決済日(年月日),決済年,決済月,決済日\r\n";
      }
      // $memberInfo を書き込む
      {
          // 継続決済の場合でも現在時刻を入れる
          if ($isContinue || empty($memberInfo['payment_date'])) {
              $paymentDate = date("Y-m-d");
          } else {
              $tmp = explode(" ", $memberInfo['payment_date']);
              $paymentDate = $tmp[0];
          }
          // 現在時刻 を年、月、日に分解
          $paymentDateArray = explode("-", $paymentDate);
          
          $outputString .= $memberInfo['member_id'] . ",";
          $outputString .= $memberInfo['kanji'] . ",";
          $outputString .= $memberInfo['introducer_id'] . ",";
          $outputString .= $memberInfo['level'] . ",";
          $outputString .= self::getMemberLevelName($memberInfo['level']) . ",";
          $outputString .= $resultString . ",";
          $outputString .= $paymentDate . ",";
          $outputString .= $paymentDateArray[0] . ",";
          $outputString .= $paymentDateArray[1] . ",";
          $outputString .= $paymentDateArray[2];
          $outputString .= "\r\n";
      }

      file_put_contents($csvFileName, $outputString, FILE_APPEND | LOCK_EX);
  }
    
  /**
   * 会員レベル名称の取得
   * @param $memberLebel
   *
   * @return string
   */
  public static function getMemberLevelName($memberLebel) {
      switch ($memberLebel) {
          // 関東会員レベル
          case Constant::UNPAID_PREMIUM_MEMBER:
              return "関東プレミアム会員（決済未済）";
          case Constant::UNPAID_PREMIUM_AGENCY:
              return "関東プレミアム代理店会員（決済未済）";
          case Constant::UNPAID_PREMIUM_AGENCY_ORGANIZER:
              return "関東プレミアム代理店会員＆主催（決済未済）";
          case Constant::PREMIUM_MEMBER_LEVEL:
              return "関東プレミアム会員";
          case Constant::PREMIUM_AGENCY_LEVEL:
              return "関東プレミアム代理店会員";
          case Constant::PREMIUM_AGENCY_ORGANIZER_LEVEL:
              return "関東プレミアム代理店会員＆主催";
          // 関西会員レベル
          case Constant::UNPAID_PREMIUM_MEMBER_WEST:
              return "関西プレミアム会員（決済未済）";
          case Constant::UNPAID_PREMIUM_AGENCY_WEST:
              return "関西プレミアム代理店会員（決済未済）";
          case Constant::UNPAID_PREMIUM_AGENCY_ORGANIZER_WEST:
              return "関西プレミアム代理店会員＆主催（決済未済）";
          case Constant::PREMIUM_MEMBER_LEVEL_WEST:
              return "関西プレミアム会員";
          case Constant::PREMIUM_AGENCY_LEVEL_WEST:
              return "関西プレミアム代理店会員";
          case Constant::PREMIUM_AGENCY_ORGANIZER_LEVEL_WEST:
              return "関西プレミアム代理店会員＆主催";
      }
      return "定義されていないレベル:" . $memberLebel;
  }

} // end of class


?>
