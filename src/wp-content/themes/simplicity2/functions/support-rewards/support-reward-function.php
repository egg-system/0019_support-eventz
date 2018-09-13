<?php

require('class/support-rewards-controller.php');

const ADMIN_REWARD_PAGE="support-reward";

add_action('admin_menu', 'init_support_reward_page', 11);

function init_support_reward_page()
{
	$supportReward = new SupportRewardsController();
	if ($_GET['content-type'] === 'csv' && $_GET['page'] === ADMIN_REWARD_PAGE) {
		$supportReward->export();
	}

	add_menu_page('報酬確認', '報酬', 'administrator', ADMIN_REWARD_PAGE, [$supportReward, 'index']); 
}

add_action('admin_enqueue_scripts', 'add_reward_admin_css');

function add_reward_admin_css() 
{
	$rewardAdminCssPath = '/wp-content/themes/simplicity2/functions/support-rewards/reward-admin.css';
	wp_enqueue_style('custom', $rewardAdminCssPath);
}