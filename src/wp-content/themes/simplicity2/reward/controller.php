<?php
namespace Reward;

include_once(__DIR__ . "/constant.php");

class Controller
{
    // ワードプレスのグローバル変数
    private $postId;
    private $wpdb;
    private $tablePrefix;

    /**
     * コンストラクタ
     *
     * @param int paostId
     * @param object $wpdb
     * @param string $tablePrefix
     * @return void
     */
    public function __construct($postId, $wpdb, $tablePrefix)
    {
        $this->postId = $postId;
        $this->wpdb = $wpdb;
        $this->tablePrefix = $tablePrefix;
    }
    
    /**
     * ルーティング処理
     *
     * @return void
     */
    public function routing()
    {
        // 早期リターン
        if (\SwpmMemberUtils::is_member_logged_in() === false) {
            return;
        }

        if ($this->postId === Constant::DETAIL_PAGE_ID && 
            file_exists(Constant::DETAIL_MODEL_FILE) && 
            file_exists(Constant::DETAIL_VIEW_FILE)) {

            // 詳細画面の処理
            include_once(Constant::DETAIL_MODEL_FILE);
            $detail = new Model\Detail($this->wpdb, $this->tablePrefix);
            $detail->exec();
            include_once(Constant::DETAIL_VIEW_FILE);
        } elseif ($this->postId === Constant::CONFIRM_PAGE_ID &&
            file_exists(Constant::CONFIRM_MODEL_FILE) &&
            file_exists(Constant::CONFIRM_VIEW_FILE)) {

            // 確認画面
            include_once(Constant::CONFIRM_MODEL_FILE);
            $confirm = new Model\Confirm($this->wpdb, $this->tablePrefix);
            $confirm->exec();
            include_once(Constant::CONFIRM_VIEW_FILE);
        } elseif ($this->postId === Constant::DONE_PAGE_ID &&
            file_exists(Constant::DONE_MODEL_FILE) &&
            file_exists(Constant::DONE_VIEW_FILE)) {

            // 完了画面
            include_once(Constant::DONE_MODEL_FILE);
            $done = new Model\Done($this->wpdb, $this->tablePrefix);
            $done->exec();
            include_once(Constant::DONE_VIEW_FILE);
        }

    }
}
