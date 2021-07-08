<?php

namespace AutoReg;

include_once(__DIR__ . "/constant.php");
include_once(__DIR__ . "/mailConstant.php");

class AutoRegistration {

    // ワードプレスのグローバル変数
    private $wpdb;
    private $tablePrefix;

    function __construct($wpdb, $tablePrefix) {
        $this->wpdb = $wpdb;
        $this->tablePrefix = $tablePrefix;

        // タイムゾーンのセット
        date_default_timezone_set('Asia/Tokyo');
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
            $client_ip = (site_url() == Constant::SITE_URL) ? Constant::PRODUCT_CLIENT_IP : Constant::TEST_CLIENT_IP;

            // プレミアム代理店主催の場合、moneyパラメーターへ指定する金額が変化する
            // TODO 関東と関西でURL降り分け
            // $paymentUrl = '';
            // if (in_array(intval($member_level), Constant::MEMBER_LEVEL_ARY_WEST, true)) {
            //   // 関西
            //   $paymentUrl = Constant::TELECOM_CREDIT_FORM_URL.$client_ip."&money={$fee}&rebill_param_id=1month{$money}yen_end&usrmail={$email}&usrtel={$tel}&redirect_back_url={$redirectUrl}";
            // } else {
            //   // 関東
            //   $fee = ($member_level == Constant::UNPAID_PREMIUM_AGENCY_ORGANIZER) ? Constant::PREMIUM_AGENCY_ORGANIZER_URL_FEE : $money;
            //   //$paymentUrl = Constant::TEST_URL.$client_ip."&money={$fee}&rebill_param_id=1day{$money}yen&usrmail={$email}&usrtel={$tel}&redirect_back_url={$redirectUrl}";
            //   $paymentUrl = Constant::TELECOM_CREDIT_FORM_URL.$client_ip."&money={$fee}&rebill_param_id=1month{$money}yen_end&usrmail={$email}&usrtel={$tel}&redirect_back_url={$redirectUrl}";
            // }

            // プレミアム代理店&主催の場合、moneyパラメーターへ指定する金額が変化する
            $fee = ($member_level == Constant::UNPAID_PREMIUM_AGENCY_ORGANIZER) ? Constant::PREMIUM_AGENCY_ORGANIZER_URL_FEE : $money;
            //$paymentUrl = Constant::TEST_URL.$client_ip."&money={$fee}&rebill_param_id=1day{$money}yen&usrmail={$email}&usrtel={$tel}&redirect_back_url={$redirectUrl}";
            $paymentUrl = Constant::TELECOM_CREDIT_FORM_URL.$client_ip."&money={$fee}&rebill_param_id=1month{$money}yen_end&usrmail={$email}&usrtel={$tel}&redirect_back_url={$redirectUrl}";

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
         if ($this->_isPaymentSucceed($is_telecom_access)) {
           $email = $_GET['email'];
           $fee = $_GET['money'];

           // 会員取得
           $member_info = $this->_getMember($email);
           if (!array_key_exists('introducer_id', $member_info) || is_null($member_info['introducer_id'])) {
              $this->_sendErrEmailRegistration($email, $member_info);
              $this->_initPaymentErr($email, "会員取得処理失敗");
              return;
           }

           // 会員レベルの更新
           $upd_result = $this->_updateMembershipLevel($email, $member_info);
           if (false === $upd_result) {
              $this->_initPaymentErr($email, "会員レベル更新処理失敗");
              return;
           }

           // 紹介者報酬登録
           $ins_result = $this->_insertIntroducedReward($email, $member_info);
           if (false === $ins_result) {
              $this->_initPaymentErr($email, "紹介者報酬登録失敗");
              return;
           }

           // 決済成功日更新
           $upd_result = $this->_updatePaymentDate($email, $member_info);
           if (false === $upd_result) {
              $this->_initPaymentErr($email, "決済日更新処理失敗");
              return;
           }

           // 初回決済完了メール
           $this->_sendInitPaymentMail($email, $member_info);
           echo('決済認証成功');
         } else {
           // Slackへの通知
           $member_info = $this->_getMember($_GET['email']);
           if (isset($_GET['email'])) $this->_msgPaymentErr(Constant::FIRST_PAY, $member_info['level']);

           // 初回決済エラーのお知らせメール
           $this->_sendPaymentErrMail($_GET['email'], $member_info);

           echo('決済認証失敗');
         }
    }


    /**
     * 会員登録完了メール送信
     *
     * @return void
     */
     public function _sendInitPaymentMail($email, $member_info) {

         // XX様
         $head_name = $member_info['kanji'] . " 様<br>";

         // 会員レベル毎に異なる表示内容を取得
         $each_member_contents = $this->_getEachMemberLevelMsg($member_info);

         // 会員レベル毎の文面
         $subject = ""; // 件名
         $message = "";
         if ($member_info['level'] == Constant::UNPAID_PREMIUM_MEMBER) {
           // プレミアム会員
           $subject = "【重要】【サポートイベント】プレミアム会員決済登録確認完了のお知らせ";
           $premiumMsg01 = MailConstant::PREMIUM_MEMBER_MAIL01;
           $premiumMsg02 = MailConstant::PREMIUM_MEMBER_MAIL02;
           $message = $premiumMsg01 . $each_member_contents . $premiumMsg02;
         } else {
           // プレミアム代理店会員、代理店&主催会員
           $subject= "【重要】【サポートイベント】プレミアム代理店決済登録確認完了のお知らせ";
           $agenciesMsg01 = MailConstant::PREMIUM_AGENCIES_MEMBER_MAIL01;
           $agenciesMsg02 = MailConstant::PREMIUM_AGENCIES_MEMBER_MAIL02;
           $message = $agenciesMsg01 . $each_member_contents . $agenciesMsg02;
         }

         // 全文
         $all_message = $head_name . $message;

         // ヘッダー
         $headers = ['From: サポートイベント <cafesuppo@gmail.com>', 'Content-Type: text/html; charset=UTF-8',];
         wp_mail($email, $subject, $all_message, $headers);
    }


    /**
     * 初回決済エラーのお知らせメール
     *
     * @return void
     */
     public function _sendPaymentErrMail($email, $member_info) {
         // XX様
         $head_name = $member_info['kanji'] . " 様<br>";

         // タイトル
         $subject = "【重要】【サポートイベント】クレジットカードご決済エラーのお知らせ";

         // 本文
         $message = mailConstant::INIT_PAYMENT_ERR_MSG;

         // 全文
         $all_message = $head_name . $message;

         // ヘッダー
         $headers = ['From: サポートイベント <cafesuppo@gmail.com>', 'Content-Type: text/html; charset=UTF-8',];
         wp_mail($email, $subject, $all_message, $headers);
     }


     /**
      * 初回メールアドレスエラーのお知らせメール
      *
      * @return void
      */
      public function _sendErrEmailRegistration($email, $member_info) {
          // XX様
          $head_name = "会員様<br>";

          // タイトル
          $subject = "【重要】【サポートイベント】メールアドレスご登録エラーのお知らせ";

          // 本文
          $message = mailConstant::ERR_EMAIL_REGISTRATION_MSG;

          // 全文
          $all_message = $head_name . $message;

          // ヘッダー
          $headers = ['From: サポートイベント <cafesuppo@gmail.com>', 'Content-Type: text/html; charset=UTF-8',];
          wp_mail($email, $subject, $all_message, $headers);
      }


    /**
     * 会員レベル毎に、会員登録完了メールに表示する内容を変更
     *
     * @return String
     */
    private function _getEachMemberLevelMsg($member_info) {
        // 会員情報
        $common_msg = "<br>=========================
                <br>　　会員情報
                <br>=========================
                <br>会員ID: {$member_info['member_id']}
                <br>ログインID: {$member_info['login_id']}
                <br>メールアドレス: {$email}
                <br>電話番号: {$member_info['tel']}
                <br>氏名(漢字): {$member_info['kanji']}
                <br>氏名(かな）: {$member_info['kana']}
                <br>紹介者コード（紹介者の会員ID）: {$member_info['introducer_id']}";

        $msg = "";
        switch($member_info['level']) {
        case Constant::UNPAID_PREMIUM_MEMBER:
            $msg = "<br>利用規約 (https://support.eventz.jp/kiyaku/) に同意しました
                    <br>会員レベル: プレミアム会員
                    <br>=========================
                    <br>
                    <br>";
            break;
        case Constant::UNPAID_PREMIUM_AGENCY:
            $msg = "<br>利用規約 (https://support.eventz.jp/kiyaku/) に同意しました
                    <br>会員レベル: プレミアム代理店会員
                    <br>=========================
                    <br>
                    <br>";
            break;
        case Constant::UNPAID_PREMIUM_AGENCY_ORGANIZER:
            $msg = "<br>主催者利用規約 ( https://support.eventz.jp/syusai-kiyaku ) に同意しました
                    <br>会員レベル: プレミアム代理店会員&主催
                    <br>=========================
                    <br>
                    <br>";
            break;
        default:
              return $msg;
        }

        return $common_msg . $msg;
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
          // Slackへ通知
          $this->_msgIpErr($_GET['email'], $_SERVER["REMOTE_ADDR"]);
          return;
        }

        // 継続決済失敗時の処理
        if ($this->_isPaymentError($is_telecom_access)) {
            $email = $_GET['email'];
            // 会員取得
            $member_info = $this->_getMember($email);
            // Slack API 通知
            $this->_msgPaymentErr(Constant::CONTINUE_PAY, $member_info['level']);
            if (!array_key_exists('level', $member_info) || is_null($member_info['level'])) {
               return;
            }

            // 未決済会員レベル取得
            $unpaid_member_level = $this->_getUnpaidMemberLevel($member_info['level']);

            // 会員レベルを未決済を戻し、statusをinactiveにする
            $member_table = $this->tablePrefix."swpm_members_tbl";
            $upd_result = $this->wpdb->update($member_table,
                                              array('membership_level' => $unpaid_member_level,
                                                    'account_state' => 'inactive',
                                                    'payment_err_date' => current_time('mysql', 1)
                                                   ),
                                              array('email' => $email),
                                              array('%s', '%s')
                                             );
            if (false === $upd_result) {
              return;
            }

            return;
        }

        //  継続決済成功後、紹介者報酬登録へ
        if ($is_telecom_access && isset($_GET['email'])) {
            $email = $_GET['email'];
            $member = $this->_getMember($email);

            // 報酬テーブルに登録
            $ins_result = $this->_insertIntroducedReward($email, $member);
            if (false === $ins_result) {
              return;
            }

            // 継続決済実行日を登録
            $upd_result = $this->_updatePaymentDate($email, $member);
            if (false === $upd_result) {
              return;
            }

            // 会員レベルが未決済の状態なら、決済会員レベルに復活
            $upd_result = $this->_updateMembershipLevel($email, $member);
            if (false === $upd_result) {
              return;
            }
        }
    }


    /**
     * 決済成功判定
     *
     * @return boolean
     */
    private function _isPaymentSucceed($is_telecom_access) {
        return $is_telecom_access && isset($_GET['email']) && isset($_GET['rel']) && isset($_GET['money']) && $_GET['rel'] == 'yes';
    }


    /**
     * 継続決済成功判定
     *
     * @return boolean
     */
    private function _isPaymentError($is_telecom_access) {
      if ($rel == 'no') return true;
      if (!isset($email) || !isset($money)) return true;
      return false;
    }


    /**
     * IPアドレスNG通知
     *
     * @return void
     */
    private function _msgIpErr($email, $ipAddr) {
        $text = "IPアドレスエラー:" . $ipAddr . " user_mail:" . $email;
        $this->_sendSlackMsg($text);
    }


    /**
     * 決済NG通知
     *
     * @return void
     */
    private function _msgPaymentErr($paymentType, $level) {
        $msgParams = $_GET['email'] . " 会員レベル:" . $level . " 結果可否(rel):" . $_GET['rel'] . " IPアドレス" . $_SERVER["REMOTE_ADDR"];
        $text = "";
        if ($paymentType == Constant::CONTINUE_PAY) {
          $text = 'TEST 継続決済でNG : user_mail ' . $msgParams;
        } else {
          $text = 'TEST 初回決済でNG : user_mail ' . $msgParams;
        }
        $this->_sendSlackMsg($text);
    }


    /**
     * 初回決済NG通知
     *
     * @return void
     */
    private function _initPaymentErr($email, $err_reason) {
        $text = 'TEST 初回決済処理エラー:' .  $err_reason . ' user_mail:' . $email;
        $this->_sendSlackMsg($text);
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
        // 5,6,7のいずれかの場合
        if ($memberLevel == Constant::UNPAID_PREMIUM_MEMBER) return Constant::PREMIUM_MEMBER_FEE; // 5000
        if ($memberLevel == Constant::UNPAID_PREMIUM_AGENCY) return Constant::PREMIUM_AGENCY_FEE; // 8000
        if ($memberLevel == Constant::UNPAID_PREMIUM_AGENCY_ORGANIZER) return Constant::PREMIUM_AGENCY_ORGANIZER_FEE; // 8000
        if ($memberLevel == Constant::UNPAID_PREMIUM_MEMBER_WEST) return Constant::PREMIUM_MEMBER_FEE_WEST; // 2000
        if ($memberLevel == Constant::UNPAID_PREMIUM_AGENCY_WEST) return Constant::PREMIUM_AGENCY_FEE_WEST; // 4000
        if ($memberLevel == Constant::UNPAID_PREMIUM_AGENCY_ORGANIZER_WEST) return Constant::PREMIUM_AGENCY_ORGANIZER_FEE_WEST; // 4000
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

        // (継続決済用)決済会員の場合はそのレベルをそのまま返す
        if ($memberLevel == Constant::PREMIUM_MEMBER_LEVEL) return $memberLevel;
        if ($memberLevel == Constant::PREMIUM_AGENCY_LEVEL) return $memberLevel;
        if ($memberLevel == Constant::PREMIUM_AGENCY_ORGANIZER_LEVEL) return $memberLevel;
        if ($memberLevel == Constant::PREMIUM_MEMBER_LEVEL_WEST) return $memberLevel;
        if ($memberLevel == Constant::PREMIUM_AGENCY_LEVEL_WEST) return $memberLevel;
        if ($memberLevel == Constant::PREMIUM_AGENCY_LEVEL_ORGANIZER_WEST) return $memberLevel;
  
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
     * 会員レベルをUpdate
     *
     * @return int
     */
    private function _updateMembershipLevel($email, $member_info) {

        // 会員レベルの取得
        $membership_level = $this->_getMemberLevel($member_info['level']);
        // 会員レベルの取得が出来ていない場合処理なし
        if (is_null($membership_level)) {
          return;
        }

        // フォームにて入力された分のレコードの会員レベル更新
        $member_table = $this->tablePrefix."swpm_members_tbl";
        $upd_result = $this->wpdb->update($member_table, array('membership_level' => $membership_level), array('email' => $email));
        if (false === $upd_result) {
          return;
        }

        return $upd_result;
    }


    /**
     * 決済実行日をUpdate
     *
     * @return int
     */
    private function _updatePaymentDate($email, $member_info) {

        if (!array_key_exists('payment_date', $member_info) || !array_key_exists('account_state', $member_info)) {
          return;
        }
        // 決済日とアカウントステータスを更新
        $member_table = $this->tablePrefix."swpm_members_tbl";
        $upd_result = $this->wpdb->update($member_table,
                                          array('payment_date' => current_time('mysql', 1),
                                                'account_state' => 'active'),
                                          array('email' => $email),
                                          array('%s', '%s')
                                         );

        return $upd_result;
    }

    // TODO リファクタリング予定
    // /**
    //  * 決済失敗日をUpdate
    //  *
    //  * @return int
    //  */
    // private function _updatePaymentErrDate($email, $member_info) {
    //
    //     if (!array_key_exists('payment_date', $member_info) || !array_key_exists('account_state', $member_info)) {
    //       return;
    //     }
    //     // 決済日とアカウントステータスを更新
    //     $member_table = $this->tablePrefix."swpm_members_tbl";
    //     $upd_result = $this->wpdb->update($member_table,
    //                                       array('payment_date' => current_time('mysql', 1),
    //                                             'account_state' => 'active'),
    //                                       array('email' => $email),
    //                                       array('%s', '%s')
    //                                      );
    //
    //     return $upd_result;
    // }


    /**
     * 紹介報酬Insert
     *
     * @return int
     */
    function _insertIntroducedReward($email, $member) {

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
        $data = ['member_id' => $member['introducer_id'], // 123
                 'introducer_id' => $member['member_id'], // 456
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
        // 関東
        case Constant::PREMIUM_MEMBER_LEVEL:
          return Constant::PREMIUM_MEMBER_INTRODUCE_FEE; // 2000
        case Constant::PREMIUM_AGENCY_LEVEL:
          return Constant::PREMIUM_AGENCY_INTRODUCE_FEE; // 4000
        case Constant::PREMIUM_AGENCY_ORGANIZER_LEVEL:
          return Constant::PREMIUM_AGENCY_ORGANIZER_INTRODUCE_FEE; // 4000
        // 関西
        case Constant::PREMIUM_AGENCY_LEVEL_WEST:
          return Constant::PREMIUM_AGENCY_INTRODUCE_FEE_WEST; // 1000
        case Constant::PREMIUM_AGENCY_ORGANIZER_LEVEL_WEST:
          return Constant::PREMIUM_AGENCY_ORGANIZER_INTRODUCE_FEE_WEST; // 2000
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
          introducerTable.membership_level AS introducer_level,
          {$memberTable}.membership_level AS level,
          {$memberTable}.user_name AS login_id,
          {$memberTable}.phone AS tel,
          {$memberTable}.first_name AS kanji,
          {$memberTable}.last_name AS kana,
          {$memberTable}.account_state AS account_state,
          {$memberTable}.payment_date AS payment_date
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

    /**
     * 紹介者ID間違い時のレコード削除
     *
     * @retuen void
     */
    private function _deleteIncorrectUser($email) {
        $table = $this->tablePrefix."swpm_members_tbl";
        $this->wpdb->delete( $table, array('email' => $email), array('%s'));
    }

} // end of class

?>
