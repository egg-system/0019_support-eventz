<?php
require_once(__DIR__ . '/controller.php');

/**
 * css,jsの読み込み
 */
function enqueue_mypage_scripts() {
    // bootstrapファイルの読み込み
    wp_enqueue_script(
        'bootstrap',
        get_template_directory_uri() . '/functions/mypage-reward/scripts/bootstrap/4.1.3/bootstrap.min.js'
    );
    wp_enqueue_style(
        'bootstrap',
        get_template_directory_uri() . '/functions/mypage-reward/scripts/bootstrap/4.1.3/bootstrap.min.css'
    );
    // カスタムファイルの読み込み
    wp_enqueue_style(
        'custom',
        get_template_directory_uri() . '/functions/mypage-reward/scripts/custom.css'
    );
}

add_action('wp_enqueue_scripts', 'enqueue_mypage_scripts');

/**
 * マイページ報酬詳細画面
 */
function reward_detail() {
    global $wpdb, $table_prefix;
    $rewardController = new Reward\Controller($wpdb, $table_prefix);
    $rewardController->detail();
}
add_shortcode('reward_detail', 'reward_detail');

/**
 * マイページ報酬確認画面
 */
function reward_confirm() {
    global $wpdb, $table_prefix;
    $rewardController = new Reward\Controller($wpdb, $table_prefix);
    $rewardController->confirm();
}
add_shortcode('reward_confirm', 'reward_confirm');

/**
 * マイページ報酬完了画面
 */
function reward_done() {
    global $wpdb, $table_prefix;
    $rewardController = new Reward\Controller($wpdb, $table_prefix);
    $rewardController->done();
}
add_shortcode('reward_done', 'reward_done');

/**
 * マイページ
 */
function mypage() {
    global $wpdb, $table_prefix;
    $rewardController = new Reward\Controller($wpdb, $table_prefix);
    $rewardController->mypage();
}
add_shortcode('mypage', 'mypage');

/**
 * ID, 名前など
 */
function memberinfo() {
    global $wpdb, $table_prefix;
    $rewardController = new Reward\Controller($wpdb, $table_prefix);
    $rewardController->memberinfo();
}
add_shortcode('memberinfo', 'memberinfo');
