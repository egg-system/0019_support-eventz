<?php
namespace Reward;

use Reward\Constant as Constant;

class Dao
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
     * DBから報酬データの取得
     *
     * @param int $start
     * @param int $end
     * @param int $membersId
     * @return array $results
     */
    public function getRewardData($start, $end, $membersId)
    {
        // 必要なテーブルの定義
        $rewardDetailsTable = $this->tablePrefix . Constant::REWARD_TABLE;
        $membersTable = $this->tablePrefix . "swpm_members_tbl";
        $memberShipTable = $this->tablePrefix . "swpm_membership_tbl";
        
        $bindSql = <<<SQL
SELECT rd.id,
       DATE_FORMAT(rd.date, '%Y%m') as date,
       rd.price,
       me.member_id,
       me.first_name,
       ms.alias
FROM ${rewardDetailsTable} rd
LEFT JOIN ${membersTable} me
    ON rd.introducer_id = me.member_id 
LEFT JOIN ${memberShipTable} ms
    ON rd.level = ms.id 
WHERE rd.member_id = %d
AND DATE_FORMAT(rd.date, '%Y%m') >= ${start}
AND DATE_FORMAT(rd.date, '%Y%m') <= ${end}
ORDER BY rd.date
SQL;
        $sql = $this->wpdb->prepare($bindSql, $membersId);
        $results = $this->wpdb->get_results($sql, ARRAY_A);

        return $results;
    }

    /**
     * DBから自分の報酬全額を取得する
     *
     * @param int $membersId
     * @return array $results
     */
    public function getTotalRewardPrice($membersId)
    {
        // 必要なテーブルの定義
        $rewardDetailsTable = $this->tablePrefix . Constant::REWARD_TABLE;
        
        $bindSql = <<<SQL
SELECT sum(price) as price
FROM ${rewardDetailsTable}
WHERE member_id = %d
SQL;
        $sql = $this->wpdb->prepare($bindSql, $membersId);
        $results = $this->wpdb->get_results($sql, ARRAY_A);

        return $results[0]['price'];
    }
}
