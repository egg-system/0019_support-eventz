<?php
namespace Reward;

include_once(__DIR__ . "/constant.php");
include_once(__DIR__ . "/lib/dao.php");

class Controller
{
    // ワードプレスのグローバル変数
    private $wpdb;
    private $tablePrefix;

    /**
     * コンストラクタ
     *
     * @param object $wpdb
     * @param string $tablePrefix
     * @return void
     */
    public function __construct($wpdb, $tablePrefix)
    {
        $this->wpdb = $wpdb;
        $this->tablePrefix = $tablePrefix;

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
            $detail = new Model\Detail($this->wpdb, $this->tablePrefix);
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
            $confirm = new Model\Confirm($this->wpdb, $this->tablePrefix);
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
            $done = new Model\Done($this->wpdb, $this->tablePrefix);
            $done->exec();
            include_once(Constant::DONE_VIEW_FILE);
        }
    }
}
