<?php

class AutoRegistration {

    // 定数定義
    const UNPAID_PREMIUM_MEMBER = 5; // プレミアム会員（決済未済）
    const UNPAID_PREMIUM_AGENCY = 6; // プレミアム代理店会員（決済未済）
    const UNPAID_PREMIUM_AGENCY_ORGANIZER = 7; // プレミアム代理店会員＆主催（決済未済）
    const PREMIUM_MEMBER_LEVEL = 8; // プレミアム会員
    const PREMIUM_AGENCY_LEVEL = 9; // プレミアム代理店会員
    const PREMIUM_AGENCY_ORGANIZER_LEVEL = 10; // プレミアム代理店会員＆主催

    const PREMIUM_MEMBER_FEE = 5000; // プレミアム会員
    const PREMIUM_AGENCY_FEE = 8000; // プレミアム代理店会員
    const PREMIUM_AGENCY_ORGANIZER_FEE = 8000; // プレミアム代理店会員＆主催

    const TEST_CLIENT_IP = '00286';
    const PRODUCT_CLIENT_IP = '95518';
    // テレコムIPは下記固定で共通
    const TELECOM_IP_FROM_TO = array('52.196.8.0', '54.65.177.67', '54.95.89.20', '54.238.8.174');
    const SITE_URL = 'http://sample004.eggsystem.co.jp';
    const TELECOM_CREDIT_FORM_URL = 'https://secure.telecomcredit.co.jp/inetcredit/adult/order.pl?clientip=';
    const TEST_URL = 'https://secure.telecomcredit.co.jp/inetcredit/secure/order.pl?clientip=';

    function __construct() {
        add_action('swpm_front_end_registration_complete_fb', array($this, 'after_registration'));
        add_shortcode('receive_telecom_result', array($this, 'receive_telecom_result'));
        add_shortcode('receive_telecom_result_continue', array($this, 'receive_telecom_result_continue'));
    }

    // 登録時パラメーターを取得してURLに設定
    function after_registration($data){
        $member_level = $data['membership_level'];
        $member_level_ary = array(5, 6, 7);
        if (in_array($member_level, $member_level_ary)) {
            $email = $data['email'];
            $tel = $data['phone'];
            $money = $this->_getMemberFee($member_level);
            // パラメーターの取得が出来ていない場合処理なし
            if (empty($email) || empty($tel) || is_null($money)) {
                return;
            }
            $redirectUrl = site_url()."/register_complete";
            // $client_ip = (site_url() == 'http://www.c-lounge.club') ? PRODUCT_CLIENT_IP : TEST_CLIENT_IP;
            $client_ip = (site_url() == self::SITE_URL) ? self::TEST_CLIENT_IP : self::PRODUCT_CLIENT_IP; // テスト用

            // $paymentUrl = TELECOM_CREDIT_FORM_URL.$client_ip."&money={$money}&rebill_param_id=30day{$money}yen&usrmail={$email}&usrtel={$tel}&redirect_back_url={$redirectUrl}";
            $testPaymentUrl = self::TEST_URL.$client_ip."&money={$money}&rebill_param_id=1day{$money}yen&usrmail={$email}&usrtel={$tel}&redirect_back_url={$redirectUrl}";
	  
            header("Location: {$testPaymentUrl}");
            exit;
        }
    }


    // 初回決済
    function receive_telecom_result() {
        //IPアドレスでテレコムからのアクセスであることを確認
        $is_telecom_access = $this->_isTelecomIpAccessed();
        if ($is_telecom_access && isset($_GET['email']) && isset($_GET['rel']) && isset($_GET['money']) && $_GET['rel'] == 'yes') {
            $email = $_GET['email'];
            $fee = $_GET['money'];
            $level = $this->_getMemberLevel($fee);
            // 会員レベルの取得が出来ていない場合処理なし
            if (is_null($level)) {
                return;
            }

            // フォームにて入力された分のレコードの更新
            global $wpdb;
            $member_table = "{$wpdb->prefix}swpm_members_tbl";
            $wpdb->update($member_table, array('membership_level' => $level), array('email' => $_GET['email']));

            $this->_insertIntroducedReward($email);
	        $this->_sendInitPaymentMail($email);
            echo('決済認証成功');
        } else {
            error_log(print_r('payment faild',true)."\n", 3, "/tmp/error.log");
            echo('決済認証失敗');
        }
    }


    // 会員登録完了メール送信
    function _sendInitPaymentMail($email) {
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
        error_log(print_r('Mail send',true)."\n", 3, "/tmp/error.log");
    }   


    // 継続決済
    function receive_telecom_result_continue() {
        //IPアドレスでテレコムからのアクセスであることを確認
        $is_telecom_access = $this->_isTelecomIpAccessed();
		if (!$is_telecom_access) {
			echo('不正なアクセスです。');
			return;
		}
		
        if ($is_telecom_access && isset($_GET['email']) && isset($_GET['rel']) && $_GET['rel'] == 'no') {
            global $wpdb;
            $member_table = "{$wpdb->prefix}swpm_members_tbl";
            $wpdb->update($member_table, array('membership_level' => self::UNPAID_MEMBER_LEVEL), array('email' => $_GET['email']));

	        echo('継続決済失敗データを受信しました。');
			return;
        }
		
		if ($is_telecom_access && isset($_GET['email'])) {
			$email = $_GET['email'];
			$this->_insertIntroducedReward($email);	
		}
		
		echo('決済データを受信しました。');
    }


    // テレコムアクセスチェック
    function _isTelecomIpAccessed() {
        $remoteIp = $_SERVER["REMOTE_ADDR"];
        return in_array($remoteIp, self::TELECOM_IP_FROM_TO);
    }

    // 会員料金取得
    function _getMemberFee($member_level) {
        if ($member_level == self::UNPAID_PREMIUM_MEMBER) return self::PREMIUM_MEMBER_FEE;
        if ($member_level == self::UNPAID_PREMIUM_AGENCY) return self::PREMIUM_AGENCY_FEE;
        if ($member_level == self::UNPAID_PREMIUM_AGENCY_ORGANIZER) return self::PREMIUM_AGENCY_ORGANIZER_FEE;
        return null;
    }

    // 会員レベル取得
    function _getMemberLevel($fee) {
        if ($fee == self::PREMIUM_MEMBER_FEE) return self::PREMIUM_MEMBER_LEVEL;
        if ($fee == self::PREMIUM_AGENCY_FEE) return self::PREMIUM_AGENCY_LEVEL;
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

    const PREMIUM_MEMBER_INTRODUCE_FEE = 2000; // プレミアム会員紹介報酬
    const PREMIUM_AGENCY_INTRODUCE_FEE = 4000; // プレミアム代理店会員紹介報酬

    function _getRewardPrice($level) {  
	    switch ($level) {
		    case self::PREMIUM_MEMBER_LEVEL:
			    return self::PREMIUM_MEMBER_INTRODUCE_FEE;
		    case self::PREMIUM_AGENCY_LEVEL:
		    case self::PREMIUM_AGENCY_ORGANIZER_LEVEL:
			    return self::PREMIUM_AGENCY_ORGANIZER_LEVEL;
		    default:
			    return null;
	    }
    }

    function _getMember($email) {
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

new AutoRegistration();

?>
