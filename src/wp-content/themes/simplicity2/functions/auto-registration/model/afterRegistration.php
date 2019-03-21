<?php
namespace AutoReg\Model;

use AutoReg\Constant as Constant;
use AutoReg\Dao as Dao;
use AutoReg\AutoRegUtils as AutoRegUtils;

require_once(__DIR__ . '/../constant.php');
require_once(__DIR__ . '/../lib/dao.php');
require_once(__DIR__ . '/../lib/autoRegUtils.php');

/**
 * 初回登録後処理クラス
 *
 */
class AfterRegistration{

  // 入力フォームデータ
  private $formData = null;
  // DBからデータを取得するオブジェクト
  private $dao = null;

  private $dir = __DIR__;

  /**
   * コンストラクタ
   *
   * @param object $wpdb
   * @param string $tablePrefix
   * @return void
   */
  public function __construct($wpdb, $tablePrefix, $formData)
  {
      $this->formData = $formData;
      $this->dao = new Dao($wpdb, $tablePrefix);
  }


  /**
   * メイン処理
   *
   * @return void
   */
  public function exec() {

      // 未決済会員レベル
      $memberLevel = $this->formData['membership_level'];
      // 紹介者ID
      $introducerId = $this->formData['company_name'];
      // 入力した紹介者IDが登録されているか確認
      $isIntroducerId = $this->dao->isExistIntroducer($introducerId);
      if (in_array(intval($memberLevel), Constant::MEMBER_LEVEL_ARY, true) && $isIntroducerId) {
         $email = $this->formData['email'];
         $tel = $this->formData['phone'];
         $money = AutoRegUtils::getMemberFee($memberLevel);
         // パラメーターの取得が出来ていない場合処理なし
         if (empty($email) || empty($tel) || is_null($money)) {
             return;
         }
         
         $redirectUrl = site_url().Constant::REDIRECT_URL;
         $clientIp = (site_url() == Constant::SITE_URL) ? Constant::PRODUCT_CLIENT_IP : Constant::TEST_CLIENT_IP;

         // プレミアム代理店&主催の場合、moneyパラメーターへ指定する金額が変化する
         $fee = $this->_getPaymentFee($memberLevel, $money);
         //$paymentUrl = Constant::TEST_URL.$clientIp."&money={$fee}&rebill_param_id=1day{$money}yen&usrmail={$email}&usrtel={$tel}&redirect_back_url={$redirectUrl}";
         $paymentUrl = Constant::TELECOM_CREDIT_FORM_URL.$clientIp."&money={$fee}&rebill_param_id=1month{$money}yen_end&usrmail={$email}&usrtel={$tel}&redirect_back_url={$redirectUrl}";

         // ログ
         error_log(print_r("---初回登録後処理---:".date("Y-m-d H:i:s"), true)."\n", 3, "{$this->dir}/../log/after-registration_init.log");
         error_log(print_r($this->formData, true)."\n", 3, "{$this->dir}/../log/after-registration_init.log");
 
         header("Location: {$paymentUrl}");
         exit;
      } else {
         // 会員登録失敗の会員を削除
         $this->dao->deleteIncorrectUser($this->formData['email']);
      }
  }


  /**
   * 当月(決済時点)の算出
   *
   * @return string
   */
  private function _getPaymentFee($memberLevel, $money) {
      $fee = "";
      if ($memberLevel == Constant::UNPAID_PREMIUM_AGENCY_ORGANIZER) {
        // 関東
        $fee = Constant::PREMIUM_AGENCY_ORGANIZER_URL_FEE;
      } else if ($memberLevel == Constant::UNPAID_PREMIUM_AGENCY_ORGANIZER_WEST) {
        // 関西
        $fee = Constant::PREMIUM_AGENCY_ORGANIZER_URL_FEE_WEST;
      } else {
        $fee = $money;
      }
      return $fee;
  }

} // end of class

?>
