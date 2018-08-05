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
// 月を昇順でソート
asort($allMonth);

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
ORDER BY rd.date
SQL;
$sql = $wpdb->prepare($bindSql, $id);
$results = $wpdb->get_results($sql, ARRAY_A);
//error_log(print_r($results,true)."\n", 3, "/tmp/error.log");

// 取得したデータを成形する
$introducerData = [];
if (!empty($results)) {
    $i = 1;
    $cnt = count($results);
    foreach ($results as $record) {
        // 紹介者IDと月ごとでまとめる
        $introducerData[$record['member_id']][$record['date']] = $record;
        // 月ごとの紹介者データを1つにまとめる
        $introducerData[$record['member_id']][0] = $record;
        /*
        // 登録日は一番最初のものにする
        if ($i === 1) {
            $introducerData[$record['member_id']][0]['date'] = $record['date'];
        } 
        // それ以外は一番最後のデータを使う
error_log($i."\n", 3, "/tmp/error.log");
error_log($i."\n", 3, "/tmp/error.log");
        if ($i === $cnt) {
            $introducerData[$record['member_id']][0]['id'] = $record['id'];
            $introducerData[$record['member_id']][0]['first_name'] = $record['first_name'];
            $introducerData[$record['member_id']][0]['alias'] = $record['alias'];
        } 
        $i++;
         */
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
                <th>No</th>
                <th>紹介者名</th>
                <th>登録日</th>
                <th>区分変更</th>
                <th>会員レベル</th>
                <?php foreach ($allMonth as $month) { ?>
                    <th><?php echo $month; ?></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($introducerData as $id => $data) { ?>
                <tr>
                    <th><?php echo $data[0]['id']; ?></th>
                    <td><?php echo $data[0]['first_name']; ?></td>
                    <td><?php echo $data[0]['date']; ?></td>
                    <td></td>
                    <td><?php echo $data[0]['alias']; ?></td>
                    <?php foreach ($allMonth as $month) { ?>
                        <td><?php echo isset($data[$month]['price']) ? $data[$month]['price'] : '-'; ?></td>
                    <?php } ?>
                </tr>
        <?php } ?>
        </tbody>
    </table>
<?php } else { ?>
    <div>報酬はありません</div>
<?php } ?>

