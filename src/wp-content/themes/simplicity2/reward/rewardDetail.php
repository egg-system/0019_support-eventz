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

// 取得したデータを成形する
$inputData = [];
$outputData = [];
if (!empty($results)) {
    foreach ($results as $record) {
        // 入金データ
        if ($record['price'] > 0) {
            // 紹介者IDと月ごとでまとめる
            $inputData[$record['member_id']][$record['date']] = $record;

            // 1番最初のデータが1番はじめに登録されたデータなのでそれを代表のデータとしてまとめる
            if (!isset($inputData[$record['member_id']][0])) {
                $inputData[$record['member_id']][0] = $record;
            }
        // 出金データ
        } else {
            // 月ごとでまとめる
            if (!isset($outputData[$record['date']])) {
                $outputData[$record['date']] = $record['price'];
            } else {
                $outputData[$record['date']] += $record['price'];
            }
        }
    }
}
error_log(print_r($outputData,true)."\n", 3, "/tmp/error.log");
?>

<!--TODO:bootstrapの読み込み方とタイミングを変える-->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
<script type="text/javascript">
// 画面いっぱいにする
document.getElementById('main').style.width = '100%';
</script>
<?php if (!empty($results)) { ?>
<div class="table-responsive">
    <table class="table table-condensed">
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
        <?php $number = 1; ?>
            <?php foreach ($inputData as $id => $data) { ?>
                <tr>
                    <td><?php echo $number; ?></td>
                    <td><?php echo $data[0]['first_name']; ?></td>
                    <td><?php echo $data[0]['date']; ?></td>
                    <td></td>
                    <td><?php echo $data[0]['alias']; ?></td>
                    <?php foreach ($allMonth as $month) { ?>
                        <td><?php echo isset($data[$month]['price']) ? '¥' . number_format($data[$month]['price']) : '¥0'; ?></td>
                    <?php } ?>
                </tr>
                <?php $number++ ; ?>
            <?php } ?>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <?php foreach ($allMonth as $month) { ?>
                    <th></th>
                <?php } ?>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>月間報酬額</td>
                <?php foreach ($allMonth as $month) { ?>
                    <?php $sum = 0 ; ?>
                    <?php foreach ($inputData as $id => $data) {
                        $price = isset($data[$month]['price']) ? $data[$month]['price'] : 0;
                        $sum += $price;
                    } ?>
                    <td><?php echo '¥' . number_format($sum); ?></td>
                <?php } ?>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>出金申請額</td>
                <?php foreach ($allMonth as $month) { ?>
                    <?php $price = isset($outputData[$month]) ? abs($outputData[$month]) : 0; ?>
                    <td><?php echo '¥' . number_format($price); ?></td>
                <?php } ?>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>累計報酬額</td>
                <?php $sum = 0 ; ?>
                <?php foreach ($allMonth as $month) { ?>
                <?php 
                    foreach ($inputData as $id => $data) {
                        $price = isset($data[$month]['price']) ? $data[$month]['price'] : 0;
                        $sum += $price;
                    }

                    // 出金分をマイナスする
                    $output = isset($outputData[$month]) ? abs($outputData[$month]) : 0;
                    $sum -= $output;
                ?>
                    <td><?php echo '¥' . number_format($sum); ?></td>
                <?php } ?>
            </tr>
        </tbody>
    </table>
</div>
<?php } else { ?>
    <div>報酬はありません</div>
<?php } ?>

