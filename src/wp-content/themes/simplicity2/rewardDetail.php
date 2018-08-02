<?php
 
// メンバーIDの取得
$id = SwpmMemberUtils::get_logged_in_members_id();

// 必要なテーブルの定義
$rewardDetailsTable = $table_prefix . "reward_details";
$membersTable = $table_prefix . "swpm_members_tbl";
$memberShipTable = $table_prefix . "swpm_membership_tbl";

// 取得する期間
$maxMonth = date("Ym");
// 最大6ヶ月分取得
$term = 6;
$minMonth = date("Ym",strtotime("-${term} month"));
// ループして期間の全ての月を出す
$allMonth = [];
for ($i = 0; $i < $term; $i++) {
    $allMonth[] = date("Ym",strtotime("-${i} month"));
}

$bindSql = <<<SQL
SELECT rd.id,
       DATE_FORMAT(rd.date, '%Y%m') as date,
       rd.price,
       me.member_id,
       me.first_name,
       ms.alias
FROM ${rewardDetailsTable} rd
LEFT JOIN ${membersTable} me
    ON rd.introducer_id = me.member_id 
LEFT JOIN ${memberShipTable} ms
    ON rd.level = ms.id 
WHERE rd.member_id = %d
AND DATE_FORMAT(rd.date, '%Y%m') >= ${minMonth}
AND DATE_FORMAT(rd.date, '%Y%m') <= ${maxMonth}
SQL;
$sql = $wpdb->prepare($bindSql, $id);
$results = $wpdb->get_results($sql, ARRAY_A);
error_log(print_r($results,true)."\n", 3, "/tmp/error.log");

// 取得したデータを成形する
$introducerData = [];
if (!empty($results)) {
    foreach ($results as $record) {
        // 紹介者IDと月ごとでまとめる
        $introducerData[$record['member_id']][$record['date']] = $record;
    }
}
error_log(print_r($introducerData,true)."\n", 3, "/tmp/error.log");
?>

<!--TODO:bootstrapの読み込み方とタイミングを変える-->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
<script type="text/javascript">
// 画面いっぱいにする
document.getElementById('main').style.width = '100%';
</script>
<?php if (!empty($results)) { ?>
    <table class="table">
        <thead>
            <tr>
                <th>id</th>
                <th>紹介者名</th>
                <th>日付</th>
                <th>会員レベル</th>
                <th>金額</th>
                <?php foreach ($allMonth as $month) { ?>
                    <th><?php echo $month; ?></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($results as $result) { ?>
                <tr>
                    <th><?php echo $result['id']; ?></th>
                    <td><?php echo $result['first_name']; ?></td>
                    <td><?php echo $result['date']; ?></td>
                    <td><?php echo $result['alias']; ?></td>
                    <td><?php echo $result['price']; ?></td>
                </tr>
        <?php } ?>
        <?php foreach ($introducerData as $id => $data) { ?>
                <tr>
                    <th><?php echo $result['id']; ?></th>
                    <td><?php echo $result['first_name']; ?></td>
                    <td><?php echo $result['date']; ?></td>
                    <td><?php echo $result['alias']; ?></td>
                    <td><?php echo $result['price']; ?></td>
                </tr>
        <?php } ?>
        </tbody>
    </table>
<?php } else { ?>
    <div>報酬はありません</div>
<?php } ?>

