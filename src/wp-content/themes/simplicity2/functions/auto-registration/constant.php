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
  // 定数定義
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
  // 会員レベルの配列
  const MEMBER_LEVEL_ARY = array(self::UNPAID_PREMIUM_MEMBER, self::UNPAID_PREMIUM_AGENCY, self::UNPAID_PREMIUM_AGENCY_ORGANIZER);

  // プレミアム会員
  const PREMIUM_MEMBER_FEE = 5000;
  // プレミアム代理店会員
  const PREMIUM_AGENCY_FEE = 8000;
  // プレミアム代理店会員＆主催
  const PREMIUM_AGENCY_ORGANIZER_FEE = 8000;

  // プレミアム会員紹介報酬
  const PREMIUM_MEMBER_INTRODUCE_FEE = 2000;
  // プレミアム代理店会員紹介報酬
  const PREMIUM_AGENCY_INTRODUCE_FEE = 4000;

  const TEST_CLIENT_IP = '00286';
  const PRODUCT_CLIENT_IP = '95518';
  // テレコムIPは下記固定で共通
  const TELECOM_IP_FROM_TO = array('52.196.8.0', '54.65.177.67', '54.95.89.20', '54.238.8.174');
  // リダイレクト先
  const REDIRECT_URL = '/register_complete';
  // サポートカフェサイトURL
  const SITE_URL = 'http://sample004.eggsystem.co.jp';
  // サポートカフェ テレコムクレジット決済URL
  const TELECOM_CREDIT_FORM_URL = 'https://secure.telecomcredit.co.jp/inetcredit/adult/order.pl?clientip=';
  // テスト用(要コメントアウト)
  const TEST_URL = 'https://secure.telecomcredit.co.jp/inetcredit/secure/order.pl?clientip=';
}

?>
