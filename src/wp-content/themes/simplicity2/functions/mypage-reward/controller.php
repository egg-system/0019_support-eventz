<?php
namespace Reward;

include_once(__DIR__ . "/constant.php");
include_once(__DIR__ . "/lib/dao.php");

use Reward\Dao as Dao;

class Controller
{
    // ワードプレスのグローバル変数
    private $dao;

    /**
     * コンストラクタ
     *
     * @param object $wpdb
     * @param string $tablePrefix
     * @return void
     */
    public function __construct($wpdb, $tablePrefix)
    {
        $this->dao = new Dao($wpdb, $tablePrefix);

        // タイムゾーンのセット
        date_default_timezone_set('Asia/Tokyo');
    }

    /**
     * 詳細画面
     *
     * @return void
     */
    public function detail()
    {
        // 早期リターン
        if (\SwpmMemberUtils::is_member_logged_in() === false) {
            return;
        }

        if (file_exists(Constant::DETAIL_MODEL_FILE) &&
            file_exists(Constant::DETAIL_VIEW_FILE)) {

            include_once(Constant::DETAIL_MODEL_FILE);
            $detail = new Model\Detail($this->dao);
            $detail->exec();
            include_once(Constant::DETAIL_VIEW_FILE);
        }
    }

    /**
     * 確認画面
     *
     * @return void
     */
    public function confirm()
    {
        // 早期リターン
        if (\SwpmMemberUtils::is_member_logged_in() === false) {
            return;
        }

        if (file_exists(Constant::CONFIRM_MODEL_FILE) &&
            file_exists(Constant::CONFIRM_VIEW_FILE)) {

            include_once(Constant::CONFIRM_MODEL_FILE);
            $confirm = new Model\Confirm($this->dao);
            $confirm->exec();
            include_once(Constant::CONFIRM_VIEW_FILE);
        }
    }

    /**
     * 確認画面
     *
     * @return void
     */
    public function done()
    {
        // 早期リターン
        if (\SwpmMemberUtils::is_member_logged_in() === false) {
            return;
        }

        if (file_exists(Constant::DONE_MODEL_FILE) &&
            file_exists(Constant::DONE_VIEW_FILE)) {

            include_once(Constant::DONE_MODEL_FILE);
            $done = new Model\Done($this->dao);
            $done->exec();
            include_once(Constant::DONE_VIEW_FILE);
        }
    }

    /**
     * マイページ
     *
     * @return void
     */
    public function mypage()
    {
        // 早期リターン
        if (\SwpmMemberUtils::is_member_logged_in() === false) {
            return;
        }

        if (file_exists(Constant::MYPAGE_MODEL_FILE) &&
            file_exists(Constant::MYPAGE_VIEW_FILE)) {

            include_once(Constant::MYPAGE_MODEL_FILE);
            $done = new Model\Mypage($this->dao);
            $done->exec();
            include_once(Constant::MYPAGE_VIEW_FILE);
        }
    }

    /**
     * 自分の情報
     *
     * @return void
     */
    public function memberinfo()
    {
        // 早期リターン
        if (\SwpmMemberUtils::is_member_logged_in() === false) {
            return;
        }

        if (file_exists(Constant::MEMBERINFO_MODEL_FILE) &&
            file_exists(Constant::MEMBERINFO_VIEW_FILE)) {

            include_once(Constant::MEMBERINFO_MODEL_FILE);
            $done = new Model\Memberinfo($this->dao);
            $done->exec();
            include_once(Constant::MEMBERINFO_VIEW_FILE);
        }
    }
}
