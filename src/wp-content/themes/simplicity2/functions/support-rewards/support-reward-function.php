<?php

require('class/support-rewards-controller.php');

add_action('admin_menu', 'init_support_reward_page', 11);

function init_support_reward_page()
{
    $supportReward = new SupportRewardsController();
    add_menu_page('報酬確認', '報酬', 'administrator', 'support-reward', [$supportReward, 'index']); 
}
