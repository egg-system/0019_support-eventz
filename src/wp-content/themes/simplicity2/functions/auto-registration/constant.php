<?php
/*
 * 自動登録の定数
 *
 * PHPのバージョンが低いと__DIR__をconstで使うとParse errorになるが
 * includeで使えば問題ないらしいので使う
 */

namespace AutoReg;

class Constant
{
  // ---会員レベル---
  // 関東会員レベル
  // プレミアム会員（決済未済）
  const UNPAID_PREMIUM_MEMBER = 5;
  // プレミアム代理店会員（決済未済）
  const UNPAID_PREMIUM_AGENCY = 6;
  // プレミアム代理店会員＆主催（決済未済）
  const UNPAID_PREMIUM_AGENCY_ORGANIZER = 7;
  // プレミアム会員
  const PREMIUM_MEMBER_LEVEL = 8;
  // プレミアム代理店会員
  const PREMIUM_AGENCY_LEVEL = 9;
  // プレミアム代理店会員＆主催
  const PREMIUM_AGENCY_ORGANIZER_LEVEL = 10;

  // 関西会員レベル
  // プレミアム会員（決済未済）
  const UNPAID_PREMIUM_MEMBER_WEST = 11;
  // プレミアム代理店会員（決済未済）
  const UNPAID_PREMIUM_AGENCY_WEST = 12;
  // プレミアム代理店会員＆主催（決済未済）
  const UNPAID_PREMIUM_AGENCY_ORGANIZER_WEST = 13;
  // プレミアム会員
  const PREMIUM_MEMBER_LEVEL_WEST = 14;
  // プレミアム代理店会員
  const PREMIUM_AGENCY_LEVEL_WEST = 15;
  // プレミアム代理店会員＆主催
  const PREMIUM_AGENCY_ORGANIZER_LEVEL_WEST = 16;
  // 会員レベルの配列
  const MEMBER_LEVEL_ARY = array(self::UNPAID_PREMIUM_MEMBER, self::UNPAID_PREMIUM_AGENCY, self::UNPAID_PREMIUM_AGENCY_ORGANIZER, self::UNPAID_PREMIUM_MEMBER_WEST, self::UNPAID_PREMIUM_AGENCY_WEST, self::UNPAID_PREMIUM_AGENCY_ORGANIZER_WEST);
  const MEMBER_LEVEL_ARY_WEST = array(self::UNPAID_PREMIUM_MEMBER_WEST, self::UNPAID_PREMIUM_AGENCY_WEST, self::UNPAID_PREMIUM_AGENCY_ORGANIZER_WEST);

  // ---会員料金---
  // 関東
  // プレミアム会員
  const PREMIUM_MEMBER_FEE = 5000;
  // プレミアム代理店会員
  const PREMIUM_AGENCY_FEE = 8000;
  // プレミアム代理店会員＆主催
  const PREMIUM_AGENCY_ORGANIZER_FEE = 8000;

  // 関西
  // プレミアム会員
  const PREMIUM_MEMBER_FEE_WEST = 2000;
  // プレミアム代理店会員
  const PREMIUM_AGENCY_FEE_WEST = 4000;
  // プレミアム代理店会員＆主催
  const PREMIUM_AGENCY_ORGANIZER_FEE_WEST = 4000;

  // プレミアム代理店会員＆主催 初回決済時URLパラメーター指定金額
  const PREMIUM_AGENCY_ORGANIZER_URL_FEE = 40400;
  // プレミアム代理店関西会員＆主催 初回決済時URLパラメーター指定金額
  const PREMIUM_AGENCY_ORGANIZER_URL_FEE_WEST = 4000;

  // ---紹介報酬---
  // 関東
  // プレミアム会員紹介報酬
  const PREMIUM_MEMBER_INTRODUCE_FEE = 2000;
  // プレミアム代理店会員紹介報酬
  const PREMIUM_AGENCY_INTRODUCE_FEE = 4000;
  // プレミアム代理店&主催会員紹介報酬
  const PREMIUM_AGENCY_ORGANIZER_INTRODUCE_FEE = 4000;

  // 関西
  // プレミアム会員紹介報酬
  const PREMIUM_MEMBER_INTRODUCE_FEE_WEST = 1000;
  // プレミアム代理店会員紹介報酬
  const PREMIUM_AGENCY_INTRODUCE_FEE_WEST = 2000;
  // プレミアム代理店&主催会員紹介報酬
  const PREMIUM_AGENCY_ORGANIZER_INTRODUCE_FEE_WEST = 2000;

  // ---IP,URL---
  // 本番クライアントIP
  const PRODUCT_CLIENT_IP = '95543';
  // テレコムIPは下記固定で共通
  const TELECOM_IP_FROM_TO = array('52.196.8.0', '54.65.177.67', '54.95.89.20', '54.238.8.174');
  // リダイレクト先
  const REDIRECT_URL = '/register_complete';
  // サポートカフェ本番サイト
  const SITE_URL = 'https://support.eventz.jp';
  // サポートカフェ テレコムクレジット決済URL
  const TELECOM_CREDIT_FORM_URL = 'https://secure.telecomcredit.co.jp/inetcredit/secure/order.pl?clientip=';

  // テストクライアントIP
  const TEST_CLIENT_IP = '00286';
  // サポートカフェサイトURL
  const TEST_SITE_URL = 'http://sample004.eggsystem.co.jp';
  // テスト用(要コメントアウト)
  const TEST_URL = 'https://secure.telecomcredit.co.jp/inetcredit/secure/order.pl?clientip=';

  // ---ログ関係---
  // Slack API
  // 初回決済
  const FIRST_PAY = 1;
  // 継続決済
  const CONTINUE_PAY = 2;
  // bot token
  //const BOT_TOKEN = 'xoxb-253968019206-465508768417-WPPOmWSd8lrZnTbEVrujmV1l';
  const BOT_TOKEN = 'xoxb-253968019206-465508768417-zJTzLJUTbkrBbaU0FiY8rGws';

  // テーブル名
  const REWARD_TABLE = "reward_details";
  const MEMBERS_TABLE = "swpm_members_tbl";

  // 業務ロジック
  const AFTER_REGISTRATION_MODEL_FILE = __DIR__ . "/model/afterRegistration.php";
  const RECEIVE_TELECOM_RESULT_MODEL_FILE = __DIR__ . "/model/receiveTelecomResult.php";
  const RECEIVE_TELECOM_RESULT_CONTINUE_MODEL_FILE = __DIR__ . "/model/receiveTelecomResultContinue.php";

}

?>
