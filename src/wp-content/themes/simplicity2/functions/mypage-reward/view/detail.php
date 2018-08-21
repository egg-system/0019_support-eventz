<!--TODO:bootstrapの読み込み方とタイミングを変える-->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
<script type="text/javascript">
// 画面いっぱいにする
document.getElementById('main').style.width = '100%';
</script>

<form class="form-inline" action="<?php echo Reward\Constant::DETAIL_PAGE_URL; ?>" method="get">
  <div class="form-group">
    <label>開始</label>
    <input type="number" class="form-control" placeholder="201801" name="start" value="<?php echo $detail->start; ?>">
  </div>
  <div class="form-group">
    <label>終了</label>
    <input type="number" class="form-control" placeholder="201806" name="end" value="<?php echo $detail->end; ?>">
  </div>
  <button type="submit" class="btn btn-primary">変更</button>
</form>
<?php if (!empty($detail->error)) { ?>
    <div style="color: red;"><?php echo $detail->error; ?></div>
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
                    <td><?php echo '¥' . number_format($sum); ?></td>
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
                    <td><?php echo '¥' . number_format($sum); ?></td>
                <?php } ?>
            </tr>
        </tbody>
    </table>
</div>
<?php } else { ?>
    <div>報酬はありません</div>
<?php } ?>

<form class="form-inline" action="<?php echo Reward\Constant::CONFIRM_PAGE_URL; ?>" method="post">
  <div class="form-group">
    <label>出金申請金額：</label>
    <input type="number" class="form-control" placeholder="¥30,000" name="price" value="">
    &nbsp;/&nbsp;<?php echo number_format($detail->totalPrice); ?>
  </div>
  <button type="submit" class="btn btn-success">申請</button>
</form>
