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
        if ($this->postId === Constant::DETAIL_PAGE_ID && 
            file_exists(Constant::DETAIL_MODEL_FILE) && 
            file_exists(Constant::DETAIL_VIEW_FILE) && 
            \SwpmMemberUtils::is_member_logged_in()) {

            // 詳細画面の処理
            include_once(Constant::DETAIL_MODEL_FILE);
            
            // ロジック
            $detail = new Model\Detail($this->wpdb, $this->tablePrefix);
            $detail->exec();
            
            // テンプレートの読み込み
            include_once(Constant::DETAIL_VIEW_FILE);
        }
    }
}
