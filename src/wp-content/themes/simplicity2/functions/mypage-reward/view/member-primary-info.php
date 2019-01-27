<?php
	$memberInfo = SwpmAuth::get_instance();
	if ($memberInfo->is_logged_in()) :
?>

<div class="mypage-memberinfo">
	<p><?php echo $memberInfo->get('first_name'); ?></p>
	<p>会員ID：<?php echo $memberInfo->get('member_id'); ?></p>
</div>

<?php endif; ?>