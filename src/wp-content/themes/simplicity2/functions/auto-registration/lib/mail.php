<?php
namespace AutoReg;

use AutoReg\Constant as Constant;
use AutoReg\MailConstant as MailConstant;

include_once(__DIR__ . "/../mailConstant.php");

class Mail {

  /**
   * 会員登録完了メール送信
   *
   * @return void
   */
   public static function sendInitPaymentMail($email, $memberInfo) {

       // XX様
       $headName = $memberInfo['kanji'] . " 様<br>";
       
       // 会員レベル毎に異なる表示内容を取得
       $eachMemberContents = self::getEachMemberLevelMsg($email, $memberInfo);
       
       // 会員レベル毎の文面
       $subject = ""; // 件名
       $message = "";
       if ($memberInfo['level'] == Constant::PREMIUM_MEMBER_LEVEL) {
         // プレミアム会員
         $subject = "【重要】【サポートイベント】プレミアム会員 決済登録確認完了のお知らせ";
         $premiumMsg01 = MailConstant::PREMIUM_MEMBER_MAIL01;
         $premiumMsg02 = MailConstant::PREMIUM_MEMBER_MAIL02;
         $message = $premiumMsg01 . $eachMemberContents . $premiumMsg02;
       } else if($memberInfo['level'] == Constant::PREMIUM_MEMBER_LEVEL_WEST) {
         // プレミアム関西会員
         $subject = "【重要】【サポートイベント】プレミアム関西会員 決済登録確認完了のお知らせ";
         $premiumMsg01 = MailConstant::PREMIUM_MEMBER_MAIL01;
         $premiumMsg02 = MailConstant::PREMIUM_MEMBER_MAIL02;
         $message = $premiumMsg01 . $eachMemberContents . $premiumMsg02;
       } else if($memberInfo['level'] == Constant::PREMIUM_AGENCY_LEVEL || $memberInfo['level'] == Constant::PREMIUM_AGENCY_ORGANIZER_LEVEL) {
         // プレミアム代理店会員、代理店&主催会員
         $subject= "【重要】【サポートイベント】プレミアム代理店会員 決済登録確認完了のお知らせ";
         $agenciesMsg01 = MailConstant::PREMIUM_AGENCIES_MEMBER_MAIL01;
         $agenciesMsg02 = MailConstant::PREMIUM_AGENCIES_MEMBER_MAIL02;
         $message = $agenciesMsg01 . $eachMemberContents . $agenciesMsg02;
       } else {
         // プレミアム代理店関西会員、代理店&主催関西会員
         $subject= "【重要】【サポートイベント】プレミアム代理店関西会員 決済登録確認完了のお知らせ";
         $agenciesMsg01 = MailConstant::PREMIUM_AGENCIES_MEMBER_MAIL01;
         $agenciesMsg02 = MailConstant::PREMIUM_AGENCIES_MEMBER_MAIL02;
         $message = $agenciesMsg01 . $eachMemberContents . $agenciesMsg02;
       }

       // 全文
       $allMessage = $headName . $message;

       // ヘッダー
       $headers = ['From: サポートイベント <cafesuppo@gmail.com>', 'Content-Type: text/html; charset=UTF-8',];
       wp_mail($email, $subject, $allMessage, $headers);
  }


  /**
   * 初回メールアドレスエラーのお知らせメール
   *
   * @return void
   */
   public static function sendErrEmailRegistration($email, $memberInfo) {
       // XX様
       $headName = "会員様<br>";

       // タイトル
       $subject = "【重要】【サポートイベント】メールアドレスご登録エラーのお知らせ";

       // 本文
       $message = MailConstant::ERR_EMAIL_REGISTRATION_MSG;

       // 全文
       $allMessage = $headName . $message;

       // ヘッダー
       $headers = ['From: サポートイベント <cafesuppo@gmail.com>', 'Content-Type: text/html; charset=UTF-8',];
       wp_mail($email, $subject, $allMessage, $headers);
   }


   /**
    * 初回決済エラーのお知らせメール
    *
    * @return void
    */
    public static function sendPaymentErrMail($email, $memberInfo) {
        // XX様
        $headName = $memberInfo['kanji'] . " 様<br>";

        // タイトル
        $subject = "【重要】【サポートイベント】クレジットカードご決済エラーのお知らせ";

        // 本文
        $message = MailConstant::INIT_PAYMENT_ERR_MSG;

        // 全文
        $allMessage = $headName . $message;

        // ヘッダー
        $headers = ['From: サポートイベント <cafesuppo@gmail.com>', 'Content-Type: text/html; charset=UTF-8',];
        wp_mail($email, $subject, $allMessage, $headers);
    }


    /**
     * 会員レベル毎に、会員登録完了メールに表示する内容を変更
     *
     * @return String
     */
    private static function getEachMemberLevelMsg($email, $memberInfo) {
        // 会員情報
        $commonMsg = "<br>=========================
                <br>　　会員情報
                <br>=========================
                <br>会員ID: {$memberInfo['member_id']}
                <br>ログインID: {$memberInfo['login_id']}
                <br>メールアドレス: {$email}
                <br>電話番号: {$memberInfo['tel']}
                <br>氏名(漢字): {$memberInfo['kanji']}
                <br>氏名(かな): {$memberInfo['kana']}
                <br>紹介者コード（紹介者の会員ID）: {$memberInfo['introducer_id']}";

        $msg = "";
        switch($memberInfo['level']) {
        case Constant::PREMIUM_MEMBER_LEVEL:
            $msg = "<br>利用規約 (https://support.eventz.jp/kiyaku/) に同意しました
                    <br>会員レベル: プレミアム会員
                    <br>=========================
                    <br>
                    <br>";
            break;
        case Constant::PREMIUM_AGENCY_LEVEL:
            $msg = "<br>利用規約 (https://support.eventz.jp/syusai-kiyaku/) に同意しました
                    <br>会員レベル: プレミアム代理店会員
                    <br>=========================
                    <br>
                    <br>";
            break;
        case Constant::PREMIUM_AGENCY_ORGANIZER_LEVEL:
            $msg = "<br>主催者利用規約 ( https://support.eventz.jp/syusai-kiyaku ) に同意しました
                    <br>会員レベル: プレミアム代理店会員&主催
                    <br>=========================
                    <br>
                    <br>";
            break;
        case Constant::PREMIUM_MEMBER_LEVEL_WEST:
            $msg = "<br>利用規約 (https://support.eventz.jp/kiyaku/) に同意しました
                    <br>会員レベル: プレミアム関西会員
                    <br>=========================
                    <br>
                    <br>";
            break;
        case Constant::PREMIUM_AGENCY_LEVEL_WEST:
            $msg = "<br>利用規約 (https://support.eventz.jp/syusai-kiyaku/) に同意しました
                    <br>会員レベル: プレミアム代理店関西会員
                    <br>=========================
                    <br>
                    <br>";
            break;
        case Constant::PREMIUM_AGENCY_ORGANIZER_LEVEL_WEST:
            $msg = "<br>主催者利用規約 ( https://support.eventz.jp/syusai-kiyaku ) に同意しました
                    <br>会員レベル: プレミアム代理店関西会員&主催
                    <br>=========================
                    <br>
                    <br>";
            break;
        default:
              return $msg;
        }

        return $commonMsg . $msg;
    }

} // end of class


?>
