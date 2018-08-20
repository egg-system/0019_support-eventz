<?php
namespace Reward\Model;

use Reward\Constant as Constant;

class Done
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
    }

    /**
     * メイン処理(テンプレートに必要なデータのセット)
     *
     * @return void
     */
    public function exec()
    {
        error_log("done exec\n", 3, "/tmp/hikaru_error.log");
    }
    
    /**
     * 個人のIDを取得
     *
     * @return int $membersId
     */
    private function getMembersId()
    {
        // メンバーIDの取得
        if ($this->membersId === null) {
            $this->membersId = \SwpmMemberUtils::get_logged_in_members_id();
        }

        return $this->membersId;
    }
}
