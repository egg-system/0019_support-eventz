<?php
/*
 * 報酬画面の定数
 *
 * PHPのバージョンが低いと__DIR__をconstで使うとParse errorになるが
 * includeで使えば問題ないらしいので使う
 */

namespace Reward;

class Constant
{
    // 詳細ページ
    const DETAIL_MODEL_FILE = __DIR__ . "/model/detail.php";
    const DETAIL_VIEW_FILE = __DIR__ . "/view/detail.php";
    const DETAIL_PAGE_URL = "reward_detail";

    // 確認ページ
    const CONFIRM_MODEL_FILE = __DIR__ . "/model/confirm.php";
    const CONFIRM_VIEW_FILE = __DIR__ . "/view/confirm.php";
    const CONFIRM_PAGE_URL = "reward_confirm";

    // 完了ページ
    const DONE_MODEL_FILE = __DIR__ . "/model/done.php";
    const DONE_VIEW_FILE = __DIR__ . "/view/done.php";
    const DONE_PAGE_URL = "reward_done";
    
    // マイページ
    const MYPAGE_MODEL_FILE = __DIR__ . "/model/mypage.php";
    const MYPAGE_VIEW_FILE = __DIR__ . "/view/mypage.php";
    const MYPAGE_URL = "mypage";
    
    // 最大表示期間
    const MAX_TERM = 6;

    // テーブル名
    const REWARD_TABLE = "reward_details";
    const MEMBERS_TABLE = "swpm_members_tbl";
    const MEMBERSHIP_TABLE = "swpm_membership_tbl";

    // 出金申請できる単位
    const OUTPUT_UNIT = 1000;
    // 最小出金金額
    const MINIMUM_OUTPUT_PRICE = 30000;

    // サイトの管理者メール
    //const SITE_MAIL = "cafesuppo@gmail.com";
    const SITE_MAIL = "ht.hikaru.takahashi@gmail.com";

    // nonce値 ※gitにはコミットしない
    const NONCE_DETAIL_PAGE = '';
    const NONCE_CONFIRM_PAGE = '';
}
