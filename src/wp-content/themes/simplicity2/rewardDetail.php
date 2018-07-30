<?php
 
// メンバーIDの取得
$id = SwpmMemberUtils::get_logged_in_members_id();

// 報酬詳細テーブルからデータを取得
$rewardDetailsTable = $table_prefix . "reward_details";
$membersTable = $table_prefix . "swpm_members_tbl";
$bindSql = <<<SQL
SELECT re.id,
       re.member_id,
       re.date,
       re.level,
       re.price,
       me.user_name
FROM ${rewardDetailsTable} re
LEFT JOIN ${membersTable} me
    ON re.introducer_id = me.member_id 
WHERE re.member_id = %d
SQL;
$sql = $wpdb->prepare($bindSql, $id);
$results = $wpdb->get_results($sql, ARRAY_A);
error_log(print_r($results,true)."\n", 3, "/tmp/error.log");

?>

<!--TODO:bootstrapの読み込み方とタイミングを変える-->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
<?php if (!empty($results)) { ?>
    <table class="table">
        <thead>
            <tr>
                <th>id</th>
                <th>紹介者名</th>
                <th>日付</th>
                <th>会員レベル</th>
                <th>金額</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($results as $result) { ?>
                <tr>
                    <th><?php echo $result['id']; ?></th>
                    <td><?php echo $result['user_name']; ?></td>
                    <td><?php echo $result['date']; ?></td>
                    <td><?php echo $result['level']; ?></td>
                    <td><?php echo $result['price']; ?></td>
                </tr>
        <?php } ?>
        </tbody>
    </table>
<?php } else { ?>
    <div>報酬はありません</div>
<?php } ?>

