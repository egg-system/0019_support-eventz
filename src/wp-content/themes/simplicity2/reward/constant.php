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
    const DETAIL_PAGE_ID = 15411;
    const DETAIL_MODEL_FILE = __DIR__ . "/model/detail.php";
    const DETAIL_VIEW_FILE = __DIR__ . "/view/detail.php";
    const DETAIL_PAGE_URL = "reward_detail";

    // 確認ページ
    const CONFIRM_PAGE_ID = 15673;
    const CONFIRM_MODEL_FILE = __DIR__ . "/model/confirm.php";
    const CONFIRM_VIEW_FILE = __DIR__ . "/view/confirm.php";
    const CONFIRM_PAGE_URL = "reward_confirm";

    // 完了ページ
    const DONE_PAGE_ID = 15678;
    const DONE_MODEL_FILE = __DIR__ . "/model/done.php";
    const DONE_VIEW_FILE = __DIR__ . "/view/done.php";
    const DONE_PAGE_URL = "reward_done";
    
    // 最大表示期間
    const MAX_TERM = 6;

    // テーブル名
    const REWARD_TABLE = "reward_details";
}
