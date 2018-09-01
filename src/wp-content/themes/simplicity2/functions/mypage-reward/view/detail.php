<form class="form-inline" action="<?php echo Reward\Constant::DETAIL_PAGE_URL; ?>" method="get">
  <select class="form-control col-3" name="start" value="<?php echo $detail->start; ?>">
    <?php foreach ($detail->selectTerm as $key => $month) { ?>
      <?php $selected = ($month === $detail->start) ? "selected" : ""; ?>
      <option <?php echo $selected; ?>><?php echo $month; ?></option>
    <?php } ?>
  </select>&nbsp;〜&nbsp;
  <select class="form-control col-3" name="end">
    <?php foreach ($detail->selectTerm as $key => $month) { ?>
      <?php $selected = ($month === $detail->end) ? "selected" : ""; ?>
      <option <?php echo $selected; ?>><?php echo $month; ?></option>
    <?php } ?>
  </select>
  <button type="submit" class="btn btn-primary">表示期間変更</button>
</form>
<?php if (!empty($detail->error)) { ?>
  <div class="alert alert-danger" role="alert"><?php echo $detail->error; ?></div>
<?php } ?>

<?php if (!empty($detail->results)) { ?>
<div class="table-responsive">
    <table class="table table-condensed">
        <thead>
            <tr>
                <th>No</th>
                <th>紹介者名</th>
                <th>登録日</th>
                <th>区分変更</th>
                <th>会員レベル</th>
                <?php foreach ($detail->allMonth as $month) { ?>
                    <th><?php echo $month; ?></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
        <?php $number = 1; ?>
            <?php foreach ($detail->inputData as $id => $data) { ?>
                <tr>
                    <td><?php echo $number; ?></td>
                    <td><?php echo $data[0]['first_name']; ?></td>
                    <td><?php echo $data[0]['date']; ?></td>
                    <td></td>
                    <td><?php echo $data[0]['alias']; ?></td>
                    <?php foreach ($detail->allMonth as $month) { ?>
                        <td class="text-right"><?php echo isset($data[$month]['price']) ? '¥' . number_format($data[$month]['price']) : '¥0'; ?></td>
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
                <?php foreach ($detail->allMonth as $month) { ?>
                    <th></th>
                <?php } ?>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>月間報酬額</td>
                <?php foreach ($detail->allMonth as $month) { ?>
                    <?php $sum = 0 ; ?>
                    <?php foreach ($detail->inputData as $id => $data) {
                        $price = isset($data[$month]['price']) ? $data[$month]['price'] : 0;
                        $sum += $price;
                    } ?>
                    <td class="text-right"><?php echo '¥' . number_format($sum); ?></td>
                <?php } ?>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>出金申請額</td>
                <?php foreach ($detail->allMonth as $month) { ?>
                    <?php $price = isset($detail->outputData[$month]) ? abs($detail->outputData[$month]) : 0; ?>
                    <td class="text-right"><?php echo '¥' . number_format($price); ?></td>
                <?php } ?>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>累計報酬額</td>
                <?php $sum = $detail->pastTotalPrice ; ?>
                <?php foreach ($detail->allMonth as $month) { ?>
                <?php 
                    foreach ($detail->inputData as $id => $data) {
                        $price = isset($data[$month]['price']) ? $data[$month]['price'] : 0;
                        $sum += $price;
                    }

                    // 出金分をマイナスする
                    $output = isset($detail->outputData[$month]) ? abs($detail->outputData[$month]) : 0;
                    $sum -= $output;
                ?>
                    <td class="text-right"><?php echo '¥' . number_format($sum); ?></td>
                <?php } ?>
            </tr>
        </tbody>
    </table>
</div>
<div class="alert alert-light text-right" role="alert" width="200px">
※表示できる期間は最大6ヶ月です
</div>
<?php } else { ?>
    <div>報酬はありません</div>
<?php } ?>

<div class="card bg-light mb-3">
  <div class="card-header">出金申請</div>
  <div class="card-body">
    <form class="form-inline" action="<?php echo Reward\Constant::CONFIRM_PAGE_URL; ?>" method="post">
      <input type="number" class="form-control col-4" placeholder="¥30,000" name="price" value="">
      &nbsp;/&nbsp;<?php echo number_format($detail->totalPrice); ?>
      <button type="submit" class="btn btn-success">申請</button>
    </form>
    <div>※出金は<?php echo number_format(Reward\Constant::OUTPUT_UNIT); ?>円単位で申請できます</div>
    <div>※出金は<?php echo number_format(Reward\Constant::MINIMUM_OUTPUT_PRICE); ?>円以上から申請できます</div>
  </div>
</div>
                    
<script type="text/javascript">
// 画面いっぱいにする
document.getElementById('main').style.width = '100%';
</script>
