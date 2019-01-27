<?php
require_once(__DIR__ . '/controller.php');
require_once(__DIR__ . '/auto-registration.php');

/**
 *  * 会員自動登録
 *
 */
add_action('swpm_front_end_registration_complete_fb', 'after_registration_func');
function after_registration_func($form_data) {
    global $wpdb, $table_prefix;
    $autoRegistration = new AutoReg\AutoRegistration($wpdb, $table_prefix);
    $autoRegistration->after_registration($form_data);

    // global $wpdb, $tablePrefix;
    // $autoRegController = new AutoReg\Controller($wpdb, $tablePrefix);
    // $autoRegController->after_registration($form_data);
}

/**
 *  * テレコム初回決済時処理
 *
 */
add_shortcode('receive_telecom_result', 'receive_telecom_result_func');
function receive_telecom_result_func() {
    global $wpdb, $table_prefix;
    $receiveTelecomResultController = new AutoReg\Controller($wpdb, $table_prefix);
    $receiveTelecomResultController->receive_telecom_result();
}

/**
 *  * テレコム継続決済時処理
 *
 */
add_shortcode('receive_telecom_result_continue', 'receive_telecom_result_continue_func');
function receive_telecom_result_continue_func() {
    global $wpdb, $table_prefix;
    $receiveTelecomResultContinueController = new AutoReg\Controller($wpdb, $table_prefix);
    $receiveTelecomResultContinueController->receive_telecom_result_continue();
}

/**
 * 完了メール送信
 *
 */
//add_shortcode('send_complete_mail', 'send_complete_mail');
//function send_complete_mail() {
//    global $wpdb, $table_prefix;
//    $autoRegistration = new AutoReg\AutoRegistration($wpdb, $table_prefix);
//    $email = $_GET['email'];
//    error_log(print_r($email, true)."\n", 3, "/tmp/auto-registration.log");
//    $autoRegistration = _sendInitPaymentMail($email);
//}

?>
