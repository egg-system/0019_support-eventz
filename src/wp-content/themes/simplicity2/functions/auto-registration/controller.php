<?php
namespace AutoReg;

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
     * 会員登録後にテレコムクレジット入力画面へリダイレクト
     *
     * @return void
     */
    public function after_registration($form_data) {
        if (file_exists(Constant::AFTER_REGISTRATION_MODEL_FILE)) {
            include_once(Constant::AFTER_REGISTRATION_MODEL_FILE);
            $afterRegistration = new Model\afterRegistration($this->wpdb, $this->tablePrefix, $form_data);
            $afterRegistration->exec();
        }
    }


    /**
     * テレコム初回決済
     *
     * @return void
     */
    public function receive_telecom_result() {
        if (file_exists(Constant::RECEIVE_TELECOM_RESULT_MODEL_FILE)) {
            include_once(Constant::RECEIVE_TELECOM_RESULT_MODEL_FILE);
            $receiveTelecomResult = new Model\receiveTelecomResult($this->wpdb, $this->tablePrefix);
            $receiveTelecomResult->exec();
        }
    }


    /**
     * テレコム継続決済
     *
     * @return void
     */
    public function receive_telecom_result_continue() {
        if (file_exists(Constant::RECEIVE_TELECOM_RESULT_CONTINUE_MODEL_FILE)) {
            include_once(Constant::RECEIVE_TELECOM_RESULT_CONTINUE_MODEL_FILE);
            $receiveTelecomResultContinue = new Model\ReceiveTelecomResultContinue($this->wpdb, $this->tablePrefix);
            $receiveTelecomResultContinue->exec();
        }
    }
}

?>
