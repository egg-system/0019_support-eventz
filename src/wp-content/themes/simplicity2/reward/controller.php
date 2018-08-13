<?php
namespace Reward;

class Controller
{
    // 詳細ページ
    const DETAIL_PAGE_ID = 15411;
    const DETAIL_MODEL_FILE = __DIR__ . "/model/detail.php";
    const DETAIL_VIEW_FILE = __DIR__ . "/view/detail.php";
    // 確認ページ
    // 完了ページ

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
        if ($this->postId === self::DETAIL_PAGE_ID && 
            file_exists(self::DETAIL_MODEL_FILE) && 
            file_exists(self::DETAIL_VIEW_FILE) && 
            \SwpmMemberUtils::is_member_logged_in()) {

            // 詳細画面の処理
            include_once(self::DETAIL_MODEL_FILE);
            
            // ロジック
            $detail = new Detail($this->wpdb, $this->tablePrefix);
            $detail->exec();
            
            // テンプレートの読み込み
            include_once(self::DETAIL_VIEW_FILE);
        }
    }
}
