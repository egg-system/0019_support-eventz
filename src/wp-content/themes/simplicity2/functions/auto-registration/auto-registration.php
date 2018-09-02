<?php

namespace AutoReg;

include_once(__DIR__ . "/constant.php");

class AutoRegistration {

    // ワードプレスのグローバル変数
    private $wpdb;
    private $tablePrefix;

    function __construct($wpdb, $tablePrefix) {
        $this->wpdb = $wpdb;
        $this->tablePrefix = $tablePrefix;
    }

    /**
     * 会員登録時パラメーターを取得してURLに設定
     *
     * @return void
     */
     public function after_registration($form_data){
         $member_level = $form_data['membership_level'];
       if (in_array($member_level, Constant::MEMBER_LEVEL_ARY)) {
          $email = $form_data['email'];
          $tel = $form_data['phone'];
          $money = $this->_getMemberFee($member_level);
          // パラメーターの取得が出来ていない場合処理なし
          if (empty($email) || empty($tel) || is_null($money)) {
            return;
          }
          $redirectUrl = site_url()."/register_complete";
          // $client_ip = (site_url() == 'http://www.c-lounge.club') ? PRODUCT_CLIENT_IP : TEST_CLIENT_IP;
          $client_ip = (site_url() == Constant::SITE_URL) ? Constant::TEST_CLIENT_IP : Constant::PRODUCT_CLIENT_IP; // テスト用

          // $paymentUrl = Constant::TELECOM_CREDIT_FORM_URL.$client_ip."&money={$money}&rebill_param_id=30day{$money}yen&usrmail={$email}&usrtel={$tel}&redirect_back_url={$redirectUrl}";
          $testPaymentUrl = Constant::TEST_URL.$client_ip."&money={$money}&rebill_param_id=1day{$money}yen&usrmail={$email}&usrtel={$tel}&redirect_back_url={$redirectUrl}";

          header("Location: {$testPaymentUrl}");
          exit;
       }
     }


    /**
     * テレコム初回決済
     *
     * @return void
     */
     public function receive_telecom_result() {
       //IPアドレスでテレコムからのアクセスであることを確認
         $is_telecom_access = $this->_isTelecomIpAccessed();
       if ($is_telecom_access && isset($_GET['email']) && isset($_GET['rel']) && isset($_GET['money']) && $_GET['rel'] == 'yes') {
          $email = $_GET['email'];
          $fee = $_GET['money'];
          $level = $this->_getMemberLevel($fee);
          // 会員レベルの取得が出来ていない場合処理なし
          if (is_null($level)) return;

          // フォームにて入力された分のレコードの更新
          $member_table = $this->tablePrefix."swpm_members_tbl";
          $this->wpdb->update($member_table, array('membership_level' => $level), array('email' => $_GET['email']));

          $this->_insertIntroducedReward($email);
          $this->_sendInitPaymentMail($email);
          echo('決済認証成功');
        } else {
          echo('決済認証失敗');
        }
    }


    /**
     * 会員登録完了メール送信
     *
     * @return void
     */
    public function _sendInitPaymentMail($email) {
        // 件名
        $subject = "【サポートカフェ】会員登録完了";
        // 本文
        $message = "サポートカフェ事務局です。<br><br>会員登録が全て完了いたしました。<br>
                 <br>今後ともサポートカフェをよろしくお願いいたします。<br>
                 <br>株式会社 トレイス<br>
                 <br>MAIL:cafesuppo@gmail.com<br>";
        // ヘッダー
        $headers = ['From: サポートカフェ <cafesuppo@gmail.com>', 'Content-Type: text/html; charset=UTF-8',];
        wp_mail($email, $subject, $message, $headers);
    }


    /**
     * 継続決済
     *
     * @return void
     */
    public function receive_telecom_result_continue() {
        //IPアドレスでテレコムからのアクセスであることを確認
        $is_telecom_access = $this->_isTelecomIpAccessed();
        if (!$is_telecom_access) {
            echo('不正なアクセスです。');
            return;
        }

        if ($is_telecom_access && isset($_GET['email']) && isset($_GET['rel']) && $_GET['rel'] == 'no') {
            //global $wpdb; $this->tablePrefix
            //$member_table = "{$wpdb->prefix}swpm_members_tbl";
            $member_table = $this->tablePrefix . "swpm_members_tbl";
            $this->wpdb->update($member_table, array('membership_level' => Constant::UNPAID_MEMBER_LEVEL), array('email' => $_GET['email']));

            echo('継続決済失敗データを受信しました。');
            return;
        }

        if ($is_telecom_access && isset($_GET['email'])) {
            $email = $_GET['email'];
            $this->_insertIntroducedReward($email);
        }
        echo('決済データを受信しました。');
    }


    /**
     * テレコムアクセスチェック
     *
     * @return boolean
     */
    private function _isTelecomIpAccessed() {
        $remoteIp = $_SERVER["REMOTE_ADDR"];
        return in_array($remoteIp, Constant::TELECOM_IP_FROM_TO);
    }

    /**
     * 会員料金取得
     *
     * @return int
     */
    private function _getMemberFee($member_level) {
        if ($member_level == Constant::UNPAID_PREMIUM_MEMBER) return Constant::PREMIUM_MEMBER_FEE;
        if ($member_level == Constant::UNPAID_PREMIUM_AGENCY) return Constant::PREMIUM_AGENCY_FEE;
        if ($member_level == Constant::UNPAID_PREMIUM_AGENCY_ORGANIZER) return Constant::PREMIUM_AGENCY_ORGANIZER_FEE;
        return null;
    }

    /**
     * 会員レベル取得
     *
     * @return int
     */
    private function _getMemberLevel($fee) {
        if ($fee == Constant::PREMIUM_MEMBER_FEE) return Constant::PREMIUM_MEMBER_LEVEL;
        if ($fee == Constant::PREMIUM_AGENCY_FEE) return Constant::PREMIUM_AGENCY_LEVEL;
        return null;
    }

    function _insertIntroducedReward($email) {
      $member = $this->_getMember($email);

      // 紹介者IDが取得できない場合、
      if (!array_key_exists('introducer_id', $member) && is_null($member['introducer_id'])) {
        return;
      }

      // 報酬がない場合も処理なし
      $rewardPrice = $this->_getRewardPrice($member['level']);
      if (is_null($rewardPrice)) {
        return;
      }

      global $wpdb;
      $rewardTable = "{$wpdb->prefix}reward_details";
      $insertSql = "
          INSERT INTO {$rewardTable} (`member_id`, `introducer_id`, `date`, `level`, `price`)
          VALUES ({$member['member_id']}, {$member['introducer_id']}, CURRENT_TIMESTAMP(), {$member['level']}, {$rewardPrice});";
    }

    private function _getRewardPrice($level) {
      switch ($level) {
        case Constant::PREMIUM_MEMBER_LEVEL:
          return Constant::PREMIUM_MEMBER_INTRODUCE_FEE;
        case Constant::PREMIUM_AGENCY_LEVEL:
        case Constant::PREMIUM_AGENCY_ORGANIZER_LEVEL:
          return Constant::PREMIUM_AGENCY_ORGANIZER_LEVEL;
        default:
          return null;
        }
    }

    private function _getMember($email) {
      global $wpdb;
      $memberTable = "{$wpdb->prefix}swpm_members_tbl";

      return  $wpdb->get_row("
        SELECT
          {$memberTable}.member_id AS member_id,
          introducerTable.member_id AS introducer_id,
          {$memberTable}.membership_level AS level
        FROM {$memberTable}
        LEFT JOIN (SELECT * FROM {$memberTable}) as introducerTable
        ON {$memberTable}.company_name = introducerTable.member_id
        WHERE {$memberTable}.email = '{$email}'
        ", 'ARRAY_A');
    }

} // end of class

?>
