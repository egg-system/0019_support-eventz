// 定数定義
const UNPAID_PREMIUM_MEMBER = 5; // プレミアム会員（決済未済）
const UNPAID_PREMIUM_AGENCY = 6; // プレミアム代理店会員（決済未済）
const UNPAID_PREMIUM_AGENCY_ORGANIZER = 7; // プレミアム代理店会員＆主催（決済未済）
const PREMIUM_MEMBER_LEVEL = 8; // プレミアム会員
const PREMIUM_AGENCY_LEVEL = 9; // プレミアム代理店会員
const PREMIUM_AGENCY_ORGANIZER_LEVEL = 10; // プレミアム代理店会員＆主催
const MEMBER_LEVEL_ARR = array(UNPAID_PREMIUM_MEMBER, UNPAID_PREMIUM_AGENCY, UNPAID_PREMIUM_AGENCY_ORGANIZER);

const PREMIUM_MEMBER_FEE = 5000; // プレミアム会員
const PREMIUM_AGENCY_FEE = 8000; // プレミアム代理店会員
const PREMIUM_AGENCY_ORGANIZER_FEE = 8000; // プレミアム代理店会員＆主催

const TEST_CLIENT_IP = '00286';
const PRODUCT_CLIENT_IP = '95518';
// テレコムIPは下記固定で共通
const TELECOM_IP_FROM_TO = array('52.196.8.0', '54.65.177.67', '54.95.89.20', '54.238.8.174');
