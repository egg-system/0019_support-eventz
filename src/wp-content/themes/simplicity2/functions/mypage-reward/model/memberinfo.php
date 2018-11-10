<?php
namespace Reward\Model;

use Reward\Constant as Constant;

class Memberinfo
{
    // DBからデータを取得するオブジェクト
    private $dao = null;

    // テンプレートで使う変数
    public $error = "";

    /**
     * コンストラクタ
     *
     * @param object $dao
     * @return void
     */
    public function __construct($dao)
    {
        $this->dao = $dao;
    }

    /**
     * メイン処理(テンプレートに必要なデータのセット)
     *
     * @return void
     */
    public function exec()
    {
      // メンバーIDの取得
      $membersId = \SwpmMemberUtils::get_logged_in_members_id();

      // 必要なユーザーデータの取得
      $memberInfo = $this->dao->getMemberInfo($membersId);
    }

}
