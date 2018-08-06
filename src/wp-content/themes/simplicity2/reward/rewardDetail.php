<?php

class RewardDetail
{
    // 最大期間
    const MAX_TERM = 6;

    // テンプレートで使う変数
    public $start;
    public $end;
    public $results;
    public $allMonth;
    public $inputData;

    /**
     * メイン処理(テンプレートに必要なデータのセット)
     *
     * @return void
     */
    public function setTemplateData()
    {
        $this->setParam();
    }

    /**
     * 引数の取得
     *
     * @return void
     */
    private function setParam()
    {
        // パラメータの取得
        $this->start = $_GET['start'];
        $this->end = $_GET['end'];
    }

    /**
     * 引数のチェック
     *
     * @param int $start
     * @param int $end
     * @return boolean
     */
    private function checkParam($start, $end)
    {
        if (strlen($start) !== 6 && strlen($end) !== 6) {
            return false;
        }

        return true;
    }

    /**
     * 期間の取得
     *
     * @return void
     */
    private function getMonth()
    {
        // 取得する期間
        $maxMonth = date("Ym");
        $term = self::MAX_TERM;
        $minMonth = date("Ym",strtotime("-${term} month"));
        // ループして期間の全ての月を出す
        $allMonth = [];
        for ($i = 0; $i < $term; $i++) {
            $allMonth[] = date("Ym",strtotime("-${i} month"));
        }
        // 月を昇順でソート
        asort($allMonth);

        return $allMonth;
    }

    /**
     * DBから報酬データの取得
     *
     * @param int $start
     * @param int $end
     * @return array $results
     */
    private function getRewardData($start, $end)
    {
        // 必要なテーブルの定義
        $rewardDetailsTable = $table_prefix . "reward_details";
        $membersTable = $table_prefix . "swpm_members_tbl";
        $memberShipTable = $table_prefix . "swpm_membership_tbl";

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
AND DATE_FORMAT(rd.date, '%Y%m') >= ${start}
AND DATE_FORMAT(rd.date, '%Y%m') <= ${end}
ORDER BY rd.date
SQL;
        $sql = $wpdb->prepare($bindSql, $id);
        $results = $wpdb->get_results($sql, ARRAY_A);

        return $results;
    }

    /**
     * 入金出金データをセット
     *
     * @param array $results
     * @return void
     */
    private function setInputOutput($results)
    {
        $inputData = [];
        $outputData = [];

        // 早期リターン
        if (empty($results)) {
            $this->inputData = $inputData;
            $this->outputData = $outputData;
            return true;
        }

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
        $this->inputData = $inputData;
        $this->outputData = $outputData;
    }
}
$rewardDetail = new RewardDetail();
$rewardDetail->setTemplateData();
error_log($rewardDetail->start."\n", 3, "/tmp/hikaru_error.log");
error_log($rewardDetail->end."\n", 3, "/tmp/hikaru_error.log");

// パラメータの取得
$start = $_GET['start'];
$end = $_GET['end'];
//$check = checkParam($start, $end);
//error_log($check."\n", 3, "/tmp/error.log");
//error_log(gettype($check)."\n", 3, "/tmp/error.log");
 
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
?>

<!--TODO:bootstrapの読み込み方とタイミングを変える-->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
<script type="text/javascript">
// 画面いっぱいにする
document.getElementById('main').style.width = '100%';
</script>
<div>表示期間：</div>

<form class="form-inline" action="reward_detail_test" method="get">
  <div class="form-group">
    <label>開始</label>
    <input type="number" class="form-control" placeholder="201801" name="start" value="11">
  </div>
  <div class="form-group">
    <label>終了</label>
    <input type="number" class="form-control" placeholder="201806" name="end" value="22">
  </div>
  <button type="submit" class="btn btn-primary">変更</button>
</form>

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

