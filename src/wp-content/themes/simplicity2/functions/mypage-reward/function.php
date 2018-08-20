<?php
require_once(__DIR__ . '/controller.php');

/**
 * マイページ報酬詳細画面
 */
function reward_detail() {
    global $post, $wpdb, $table_prefix;
    $rewardController = new Reward\Controller($post->ID, $wpdb, $table_prefix);
    $rewardController->detail();
}
add_shortcode('reward_detail', 'reward_detail');

/**
 * マイページ報酬確認画面
 */
function reward_confirm() {
    global $post, $wpdb, $table_prefix;
    $rewardController = new Reward\Controller($post->ID, $wpdb, $table_prefix);
    $rewardController->confirm();
}
add_shortcode('reward_confirm', 'reward_confirm');

/**
 * マイページ報酬完了画面
 */
function reward_done() {
    global $post, $wpdb, $table_prefix;
    $rewardController = new Reward\Controller($post->ID, $wpdb, $table_prefix);
    $rewardController->done();
}
add_shortcode('reward_done', 'reward_done');
