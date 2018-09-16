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
         // 未決済会員レベル
         $member_level = $form_data['membership_level'];
         // 紹介者ID
         $introducer_id = $form_data['company_name'];
         // 入力した紹介者IDが登録されているか確認
         $is_introducer_id = $this->_isExistIntroducer($introducer_id);
         if (in_array(intval($member_level), Constant::MEMBER_LEVEL_ARY, true) && $is_introducer_id) {
            $email = $form_data['email'];
            $tel = $form_data['phone'];
            $money = $this->_getMemberFee($member_level);
            // パラメーターの取得が出来ていない場合処理なし
            if (empty($email) || empty($tel) || is_null($money)) {
                return;
            }

            $redirectUrl = site_url().Constant::REDIRECT_URL;
            // $client_ip = (site_url() == 'http://www.c-lounge.club') ? PRODUCT_CLIENT_IP : TEST_CLIENT_IP;
            $client_ip = (site_url() == Constant::SITE_URL) ? Constant::PRODUCT_CLIENT_IP : Constant::TEST_CLIENT_IP;

            // プレミアム代理店主催の場合、moneyパラメーターへ指定する金額が変化する
            $fee = ($member_level == Constant::UNPAID_PREMIUM_AGENCY_ORGANIZER) ? Constant::PREMIUM_AGENCY_ORGANIZER_URL_FEE : $money;
            $paymentUrl = Constant::TELECOM_CREDIT_FORM_URL.$client_ip."&money={$fee}&rebill_param_id=30day{$money}yen&usrmail={$email}&usrtel={$tel}&redirect_back_url={$redirectUrl}";
            // $paymentUrl = Constant::TEST_URL.$client_ip."&money={$fee}&rebill_param_id=1day{$money}yen&usrmail={$email}&usrtel={$tel}&redirect_back_url={$redirectUrl}";

            header("Location: {$paymentUrl}");
            exit;
         } else {
            // 会員登録失敗の会員を削除
            $this->_deleteIncorrectUser($form_data['email']);
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

           // 会員取得
           $member_info = $this->_getMember($email);
           if (!array_key_exists('introducer_id', $member_info) || is_null($member_info['introducer_id'])) {
              return;
           }
           // 会員レベルの取得
           $membership_level = $this->_getMemberLevel($member_info['level']);
           // 会員レベルの取得が出来ていない場合処理なし
           if (is_null($membership_level)) {
              return;
           }

           // フォームにて入力された分のレコードの更新
           $member_table = $this->tablePrefix."swpm_members_tbl";
           $upd_result = $this->wpdb->update($member_table, array('membership_level' => $membership_level), array('email' => $_GET['email']));
           if (false === $upd_result) {
              return;
           }

           $ins_result = $this->_insertIntroducedReward($email);
           if (false === $ins_result) {
              return;
           }

           $this->_sendInitPaymentMail($email, $member_info);
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
     public function _sendInitPaymentMail($email, $member_info) {
        // 件名
        $subject = "【サポートイベント】会員登録完了";
        // 共通
        $common_msg = "サポートイベント運営事務局です。<br>
                 <br>会員登録が全て完了いたしました。<br>
                 <br>ログイン後、マイページのご利用が可能となります。
                 <br>https://support.eventz.jp/membership-login/membership-profile<br>
                 <br>※マイページでは「登録情報の変更」「報酬金額の確認」「出金申請」を行うことができます。<br>
                 <br>=========================
                 <br>　　会員情報
                 <br>=========================
                 <br>会員ID: {$member_info['member_id']}
                 <br>ログインID: {$member_info['login_id']}
                 <br>メールアドレス: {$email}
                 <br>電話番号: {$member_info['tel']}
                 <br>氏名（漢字）: {$member_info['kanji']}
                 <br>氏名(かな）: {$member_info['kana']}
                 <br>紹介者コード（紹介者の会員ID）: {$member_info['introducer_id']}
                 <br>利用規約 (https://support.eventz.jp/kiyaku/) に同意しました";

        // 会員レベル毎に異なる表示内容を取得
        $each_member_msg = $this->_getCompMailContents($member_info);

        // フッター
        $footer_msg = "<br>今後ともサポートイベントをよろしくお願いいたします。
                 <br>---
                 <br>サポートイベント運営事務局(https://support.eventz.jp)
                 <br>cafesuppo@gmail.com<br>";
        $message = $common_msg . $each_member_msg . $footer_msg;
        // ヘッダー
        $headers = ['From: サポートイベント <cafesuppo@gmail.com>', 'Content-Type: text/html; charset=UTF-8',];
        wp_mail($email, $subject, $message, $headers);
    }

    /**
     * 会員レベル毎に、会員登録完了メールに表示する内容を変更
     *
     * @return String
     */
    private function _getCompMailContents($member_info) {
        $msg = "";
        switch($member_info['level']) {
        case Constant::UNPAID_PREMIUM_MEMBER:
            $msg = "<br>会員レベル: プレミアム会員<br>";
            break;
        case Constant::UNPAID_PREMIUM_AGENCY:
            $msg = "<br>会員レベル: プレミアム代理店会員<br>";
            break;
        case Constant::UNPAID_PREMIUM_AGENCY_ORGANIZER:
            $msg = "<br>主催者利用規約 ( https://support.eventz.jp/syusai-kiyaku ) に同意しました
                　  <br>会員レベル: プレミアム代理店会員&主催<br>";
            break;
        default:
              return $msg;
        }
        return $msg;
    }

    /**
     * 継続決済
     *
     * @return void
     */
    public function receive_telecom_result_continue() {
        //IPアドレスでテレコムからのアクセスであることを確認
        $is_telecom_access = $this->_isTelecomIpAccessed();
        $is_telecom_access = true;
        if (!$is_telecom_access) {
          echo('不正なアクセスです。');
          return;
        }

        // 継続決済失敗時の処理
        if ($is_telecom_access && isset($_GET['email']) && isset($_GET['rel']) && $_GET['rel'] == 'no') {
            $email = $_GET['email'];
            // 会員取得
            $member_info = $this->_getMember($email);
            if (!array_key_exists('level', $member_info) || is_null($member_info['level'])) {
               return;
            }
            // 未決済会員レベル取得
            $unpaid_member_level = $this->_getUnpaidMemberLevel($member_info['level']);
            // 会員レベルを未決済を戻す
            $member_table = $this->tablePrefix."swpm_members_tbl";
            $upd_result = $this->wpdb->update($member_table, array('membership_level' => $unpaid_member_level), array('email' => $_GET['email']));
            if (false === $upd_result) {
              return;
            }

            return;
        }

        //  継続決済成功後、紹介者報酬登録へ
        if ($is_telecom_access && isset($_GET['email'])) {
            $email = $_GET['email'];
            $ins_result = $this->_insertIntroducedReward($email);
            if (false === $ins_result) {
              return;
            }
        }
    }


    /**
     * テレコムアクセスチェック
     *
     * @return boolean
     */
    private function _isTelecomIpAccessed() {
        $remoteIp = $_SERVER["REMOTE_ADDR"];
        return in_array($remoteIp, Constant::TELECOM_IP_FROM_TO, true);
    }

    /**
     * 会員料金取得
     *
     * @return int
     */
    private function _getMemberFee($memberLevel) {
        // 5,6,7のいずれかの場合、5000,8000,8000
        if ($memberLevel == Constant::UNPAID_PREMIUM_MEMBER) return Constant::PREMIUM_MEMBER_FEE;
        if ($memberLevel == Constant::UNPAID_PREMIUM_AGENCY) return Constant::PREMIUM_AGENCY_FEE;
        if ($memberLevel == Constant::UNPAID_PREMIUM_AGENCY_ORGANIZER) return Constant::PREMIUM_AGENCY_ORGANIZER_FEE;
        return null;
    }

    /**
     * 会員レベル取得
     *
     * @return int
     */
    private function _getMemberLevel($memberLevel) {
        // 5,6,7のいずれかの場合、8,9,10
        if ($memberLevel == Constant::UNPAID_PREMIUM_MEMBER) return Constant::PREMIUM_MEMBER_LEVEL;
        if ($memberLevel == Constant::UNPAID_PREMIUM_AGENCY) return Constant::PREMIUM_AGENCY_LEVEL;
        if ($memberLevel == Constant::UNPAID_PREMIUM_AGENCY_ORGANIZER) return Constant::PREMIUM_AGENCY_ORGANIZER_LEVEL;
        return null;
    }

    /**
     * 決済未済会員レベル取得
     *
     * @return int
     */
    private function _getUnpaidMemberLevel($level) {
        // 2回以上決済失敗の場合は5,6,7のいずれかとなる為、そのまま返す
        if ($level <= Constant::UNPAID_PREMIUM_AGENCY_ORGANIZER) return $level;

        // 8,9,10いずれかの場合、5,6,7を返す
        if ($level == Constant::PREMIUM_MEMBER_LEVEL) return Constant::UNPAID_PREMIUM_MEMBER;
        if ($level == Constant::PREMIUM_AGENCY_LEVEL) return Constant::UNPAID_PREMIUM_AGENCY;
        if ($level == Constant::PREMIUM_AGENCY_ORGANIZER_LEVEL) return Constant::UNPAID_PREMIUM_AGENCY_ORGANIZER;
        return null;
    }


    /**
     * 紹介報酬Insert
     *
     * @rrturn int
     */
    function _insertIntroducedReward($email) {
        $member = $this->_getMember($email);

        // 紹介者IDが取得できない場合、
        if (!array_key_exists('introducer_id', $member) || is_null($member['introducer_id'])) {
          return;
        }

        // 報酬がない場合も処理なし
        $rewardPrice = $this->_getRewardPrice($member['level']);
        if (is_null($rewardPrice)) {
          return;
        }

        // 必要なテーブルの定義
        $rewardTable = $this->tablePrefix."reward_details";
        // 報酬詳細テーブルはmember_idとintroducer_idが逆となる
        // 例)456さんが123さんを紹介した場合：member_id:456、introducer_id:123
        $data = ['member_id' => $member['introducer_id'],
                 'introducer_id' => $member['member_id'],
                 'date' => current_time('mysql', 1),
                 'level' => $member['level'],
                 'price' => $rewardPrice
                 ];
        $format = ['%d',
                   '%d',
                   '%s',
                   '%d',
                   '%d'];
        $results = $this->wpdb->insert($rewardTable, $data, $format);

        return $results;
    }

    /**
     * 報酬金額取得
     *
     * @return int
     */
    private function _getRewardPrice($level) {
      switch ($level) {
        case Constant::PREMIUM_MEMBER_LEVEL:
          return Constant::PREMIUM_MEMBER_INTRODUCE_FEE; // 2000
        case Constant::PREMIUM_AGENCY_LEVEL:
          return Constant::PREMIUM_AGENCY_INTRODUCE_FEE; // 4000
        case Constant::PREMIUM_AGENCY_ORGANIZER_LEVEL:
          return Constant::PREMIUM_AGENCY_INTRODUCE_FEE; // 4000
        default:
          return null;
        }
    }

    /**
     * 会員取得
     *
     * @return Array
     */
    private function _getMember($email) {
      $memberTable = $this->tablePrefix."swpm_members_tbl";

      return  $this->wpdb->get_row("
        SELECT
          {$memberTable}.member_id AS member_id,
          introducerTable.member_id AS introducer_id,
          {$memberTable}.membership_level AS level,
          {$memberTable}.user_name AS login_id,
          {$memberTable}.phone AS tel,
          {$memberTable}.first_name AS kanji,
          {$memberTable}.last_name AS kana
        FROM {$memberTable}
        LEFT JOIN (SELECT * FROM {$memberTable}) as introducerTable
        ON {$memberTable}.company_name = introducerTable.member_id
        WHERE {$memberTable}.email = '{$email}'
        ", 'ARRAY_A');
    }

    /**
     * 紹介者IDの存在チェック
     *
     * @return boolean
     */
    private function _isExistIntroducer($company_name) {
        $memberTable = $this->tablePrefix."swpm_members_tbl";

        $member_id_ary = $this->wpdb->get_row("
            SELECT
            {$memberTable}.member_id
            FROM {$memberTable}
            WHERE {$memberTable}.member_id = '{$company_name}'", 'ARRAY_A');

        if(!array_key_exists('member_id', $member_id_ary) || is_null($member_id_ary['member_id'])) {
            return false;
        }

        return true;
    }

    private function _deleteIncorrectUser($email) {
        $table = $this->tablePrefix."swpm_members_tbl";
        $this->wpdb->delete( $table, array('email' => $email), array('%s'));
    }

} // end of class

?>
