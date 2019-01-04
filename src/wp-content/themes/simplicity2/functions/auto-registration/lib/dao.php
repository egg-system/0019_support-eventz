<?php
namespace AutoReg;

use AutoReg\Constant as Constant;
use AutoReg\AutoRegUtils as AutoRegUtils;

class Dao{

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
  public function __construct($wpdb, $tablePrefix) {
      $this->wpdb = $wpdb;
      $this->tablePrefix = $tablePrefix;
  }

  /**
   * 紹介者IDの存在チェック
   *
   * @return boolean
   */
  public function isExistIntroducer($companyName) {
    // 必要なテーブルの定義
    $membersTable = $this->tablePrefix . Constant::MEMBERS_TABLE;

    $memberIdAry = $this->wpdb->get_row("
      SELECT
      {$membersTable}.member_id
      FROM {$membersTable}
      WHERE {$membersTable}.member_id = '{$companyName}'", 'ARRAY_A');

    if(!array_key_exists('member_id', $memberIdAry) || is_null($memberIdAry['member_id'])) {
      return false;
    }

    return true;
  }


  /**
   * 紹介者ID間違い時のレコード削除
   *
   * @retuen void
   */
  public function deleteIncorrectUser($email) {
      $membersTable = $this->tablePrefix . Constant::MEMBERS_TABLE;
      $this->wpdb->delete( $table, array('email' => $email), array('%s'));
  }


  /**
   * 会員取得
   *
   * @return Array
   */
  public function getMember($email) {
    $membersTable = $this->tablePrefix . Constant::MEMBERS_TABLE;

    return  $this->wpdb->get_row("
      SELECT
        {$membersTable}.member_id AS member_id,
        introducerTable.member_id AS introducer_id,
        introducerTable.membership_level AS introducer_level,
        {$membersTable}.membership_level AS level,
        {$membersTable}.user_name AS login_id,
        {$membersTable}.phone AS tel,
        {$membersTable}.first_name AS kanji,
        {$membersTable}.last_name AS kana,
        {$membersTable}.account_state AS account_state,
        {$membersTable}.payment_date AS payment_date
      FROM {$membersTable}
      LEFT JOIN (SELECT * FROM {$membersTable}) as introducerTable
      ON {$membersTable}.company_name = introducerTable.member_id
      WHERE {$membersTable}.email = '{$email}'
      ", 'ARRAY_A');
  }


  /**
   * 会員レベルをUpdate
   *
   * @return int
   */
  public function updateMembershipLevel($email, $membersInfo) {

      // 会員レベルの取得
      $membershipLevel = AutoRegUtils::getMemberLevel($membersInfo['level']);
      // 会員レベルの取得が出来ていない場合処理なし
      if (is_null($membershipLevel)) {
        return;
      }

      // フォームにて入力された分のレコードの会員レベル更新
      $membersTable = $this->tablePrefix . Constant::MEMBERS_TABLE;
      $updResult = $this->wpdb->update($membersTable, array('membership_level' => $membershipLevel), array('email' => $email));
      if (false === $updResult) {
        return;
      }

      return $updResult;
  }

  /**
   * 決済実行日をUpdate
   *
   * @return int
   */
  public function updatePaymentDate($email, $membersInfo) {

      if (!array_key_exists('payment_date', $membersInfo) || !array_key_exists('account_state', $membersInfo)) {
        return;
      }
      // 決済日とアカウントステータスを更新
      $membersTable = $this->tablePrefix . Constant::MEMBERS_TABLE;
      $updResult = $this->wpdb->update($membersTable,
                                        array('payment_date' => current_time('mysql', 1),
                                              'account_state' => 'active'),
                                        array('email' => $email),
                                        array('%s', '%s')
                                       );

      return $updResult;
  }


  /**
   * 紹介報酬Insert
   *
   * @return int
   */
  public function insertIntroducedReward($email, $memberInfo) {

      // 紹介者IDが取得できない場合、
      if (!array_key_exists('introducer_id', $memberInfo) || is_null($memberInfo['introducer_id'])) {
        return;
      }

      // 報酬がない場合も処理なし
      $rewardPrice = AutoRegUtils::getRewardPrice($memberInfo['level']);
      if (is_null($rewardPrice)) {
        return;
      }

      // 必要なテーブルの定義
      $rewardTable = $this->tablePrefix . Constant::REWARD_TABLE;
      // 報酬詳細テーブルはmember_idとintroducer_idが逆となる
      // 例)456さんが123さんを紹介した場合：member_id:456、introducer_id:123
      $data = ['member_id' => $memberInfo['introducer_id'], // 123
               'introducer_id' => $memberInfo['member_id'], // 456
               'date' => current_time('mysql', 1),
               'level' => $memberInfo['level'],
               'price' => $rewardPrice
               ];
      $format = ['%d',
                 '%d',
                 '%s',
                 '%d',
                 '%d'];
      $results = $this->wpdb->insert($rewardTable, $data, $format);

      return $results;
  }


  /**
   * 未決済会員レベルに戻す
   *
   * @return int
   */
  public function updateMembershipLevelReturns($email, $memberInfo) {

    // 未決済会員レベル取得
    $unpaidMemberLevel = AutoRegUtils::getUnpaidMemberLevel($memberInfo['level']);

    $membersTable = $this->tablePrefix . Constant::MEMBERS_TABLE;
    $updResult = $this->wpdb->update($membersTable,
                                      array('membership_level' => $unpaidMemberLevel,
                                            'account_state' => 'inactive',
                                            'payment_err_date' => current_time('mysql', 1)
                                           ),
                                      array('email' => $email),
                                      array('%s', '%s')
                                     );

    return $updResult;
  }

} // end of class

?>
