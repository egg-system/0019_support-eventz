<?php //子テーマ用関数

// --------↓デフォルト記載箇所残してます↓--------
//親skins の取得有無の設定
function include_parent_skins(){
  return true; //親skinsを含める場合はtrue、含めない場合はfalse
}

//子テーマ用のビジュアルエディタースタイルを適用
add_editor_style();

//以下にSimplicity子テーマ用の関数を書く
// --------↑デフォルト記載箇所残してます↑--------


// 定数定義
const UNPAID_PREMIUM_MEMBER = 5; // プレミアム会員（決済未済）
const UNPAID_PREMIUM_AGENCY = 6; // プレミアム代理店会員（決済未済）
const UNPAID_PREMIUM_AGENCY_ORGANIZER = 7; // プレミアム代理店会員＆主催（決済未済）
const MEMBER_LEVEL_ARR = array(UNPAID_PREMIUM_MEMBER, UNPAID_PREMIUM_AGENCY, UNPAID_PREMIUM_AGENCY_ORGANIZER);

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


// 登録時パラメーターを取得してURLに設定
add_action('swpm_front_end_registration_complete_fb','after_registration');
function after_registration($data){
  $member_level = $data['membership_level'];
  // ログ
  var_dump($member_level);
  if (in_array($member_level, MEMBER_LEVEL_ARR)) {
  	$email = $data['email'];
  	$tel = $data['phone'];
    $money = _checkMemberFee($member_level);
  	$redirectUrl = site_url()."/register_complete";
    // $client_ip = (site_url() == 'http://www.c-lounge.club') ? PRODUCT_CLIENT_IP : TEST_CLIENT_IP;
    $client_ip = (site_url() == 'http://sample004.eggsystem.co.jp/') ? TEST_CLIENT_IP : PRODUCT_CLIENT_IP; // テスト用
  	// $paymentUrl = "https://secure.telecomcredit.co.jp/inetcredit/adult/order.pl?clientip=".$client_ip."&money={$money}&rebill_param_id=30day{$money}yen&usrmail={$email}&usrtel={$tel}&redirect_back_url={$redirectUrl}";
    $testPaymentUrl = "https://secure.telecomcredit.co.jp/inetcredit/adult/order.pl?clientip=".$client_ip."&money={$money}&rebill_param_id=1day{$money}yen&usrmail={$email}&usrtel={$tel}&redirect_back_url={$redirectUrl}";
  	header("Location: {$testPaymentUrl}");
    exit;
  }
}

// 初回決済
add_shortcode('receive_telecom_result','receive_telecom_result');
function receive_telecom_result() {
  global $wpdb;
  //IPアドレスでテレコムからのアクセスであることを確認
  $is_telecom_access = _isTelecomIpAccessed();
  // ログ
  var_dump($is_telecom_access);
  if ($is_telecom_access && isset($_GET['email']) && isset($_GET['tel']) && $_GET['rel'] == 'yes') {
    $email = $_GET['email'];
    $member_table = $wpdb->prefix . 'swpm_members_tbl';
    $wpdb->update($member_table, array('membership_level' => PAID_MEMBER_LEVEL), array('email' => $_GET['email']));
    // 件名
    $subject = "【サポートカフェ】会員登録完了";
    // 本文
    $message = "サポートカフェ事務局です。<br><br>会員登録が全て完了いたしました。<br>
    <br>今後ともサポートカフェをよろしくお願いいたします。<br>
    <br>株式会社 トレイス<br>
    <br>MAIL:cafesuppo@gmail.com<br>；"
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

function _checkMemberFee($member_level) {
  if ($member_level == UNPAID_PREMIUM_MEMBER) return PREMIUM_MEMBER_FEE;
  if ($member_level == UNPAID_PREMIUM_AGENCY) return PREMIUM_AGENCY_FEE;
  if ($member_level == UNPAID_PREMIUM_AGENCY_ORGANIZER) return PREMIUM_AGENCY_ORGANIZER_FEE;
  return PREMIUM_MEMBER_FEE; // デフォルトを5000とする(仮)
}

?>
