<?php
 
// メンバーIDの取得
$id = SwpmMemberUtils::get_logged_in_members_id();

// 報酬詳細テーブルからデータを取得
$tableName = $table_prefix . "reward_details";
$sql = $wpdb->prepare("SELECT * FROM ${tableName} WHERE member_id = %d", $id);
error_log($sql."\n", 3, "/tmp/error.log");
$results = $wpdb->get_results($sql, ARRAY_A);
error_log(print_r($results,true)."\n", 3, "/tmp/error.log");

?>

<div>
id:<?php echo $results[0]['id']; ?><br>
紹介者ID:<?php echo $results[0]['introducer_id']; ?><br>
日付:<?php echo $results[0]['date']; ?><br>
会員レベル:<?php echo $results[0]['level']; ?><br>
金額:<?php echo $results[0]['price']; ?><br>
</div>
