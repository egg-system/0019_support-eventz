<?php
require_once(__DIR__ . '/controller.php');

/**
 * bootstrapの読み込み
 */
function enqueue_bootstrap_scripts() {
    wp_enqueue_script( 
        'bootstrap-js-script',
        get_template_directory_uri() . '/functions/mypage-reward/scripts/bootstrap/4.1.3/bootstrap.min.js'
    );
    wp_enqueue_style( 
        'bootstrap-css-script',
        get_template_directory_uri() . '/functions/mypage-reward/scripts/bootstrap/4.1.3/bootstrap.min.css'
    );
}

add_action('wp_enqueue_scripts', 'enqueue_bootstrap_scripts');

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
