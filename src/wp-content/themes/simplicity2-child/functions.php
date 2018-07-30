<?php //子テーマ用関数

// --------デフォルト記載箇所--------
//親skins の取得有無の設定
function include_parent_skins(){
  return true; //親skinsを含める場合はtrue、含めない場合はfalse
}

//子テーマ用のビジュアルエディタースタイルを適用
add_editor_style();

//以下にSimplicity子テーマ用の関数を書く
// ------------------------------


// 定数定義
const UNPAID_MEMBER_LEVEL = 2;
const PAID_MEMBER_LEVEL = 3;
const TEST_CLIENT_IP = '00286';
const PRODUCT_CLIENT_IP = '95518';
const TELECOM_IP_FROM_TO = array('52.196.8.0', '54.65.177.67', '54.95.89.20', '54.238.8.174');

// 初回決済
add_shortcode('receive_telecom_result','receive_telecom_result');
function receive_telecom_result() {
	global $wpdb;
	//IPアドレスでテレコムからのアクセスであることを確認
	$is_telecom_access = _isTelecomIpAccessed();
	if ($is_telecom_access && isset($_GET['email']) && isset($_GET['rel']) && $_GET['rel'] == 'yes') {
		$email = $_GET['email'];
    		$member_table = $wpdb->prefix . 'swpm_members_tbl';
		$wpdb->update($member_table, array('membership_level' => PAID_MEMBER_LEVEL), array('email' => $_GET['email']));
    // 件名
    $subject = "【サポートカフェ】登録完了";
    // 本文
    $message = "サポートカフェ事務局です。<br><br>会員登録が全て完了いたしました。<br><br>
    <br>株式会社 トレイス<br>
    <br>MAIL:cafesuppo@gmail.com<br>；
    // ヘッダー
    $headers = ['From: サポートカフェ <cafesuppo@gmail.com>',
                         'Content-Type: text/html; charset=UTF-8',];
    wp_mail($email, $subject, $message, $headers);
    echo('決済認証成功');
	} else {
    echo('決済認証失敗');
  }
}


// 継続決済
add_shortcode('receive_telecom_result_continue','receive_telecom_result_continue');
function receive_telecom_result_continue() {
	global $wpdb;
	//IPアドレスでテレコムからのアクセスであることを確認
	$is_telecom_access = _isTelecomIpAccessed();
	if ($is_telecom_access && isset($_GET['email']) && isset($_GET['rel']) && $_GET['rel'] == 'no') {
		$email = $_GET['email'];
		$member_table = $wpdb->prefix . 'swpm_members_tbl';
		$wpdb->update($member_table, array('membership_level' => UNPAID_MEMBER_LEVEL), array('email' => $_GET['email']));
    echo('継続決済失敗データを受信しました。');
	} else {
    echo('決済データを受信しました。');
  }
}

function _isTelecomIpAccessed() {
  $remoteIp = $_SERVER["REMOTE_ADDR"];
  return in_array($remoteIp, TELECOM_IP_FROM_TO);
}

?>
