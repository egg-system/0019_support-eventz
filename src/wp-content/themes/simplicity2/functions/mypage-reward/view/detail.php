<h3>現時点報酬金額合計</h3>
<?php echo number_format($detail->totalPrice) . "円"; ?><br>
<?php echo "(" . date("Y年m月d日") . "時点)"; ?>

<h3>過去の履歴参照</h3>
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
  <button type="submit" class="btn btn-primary btn-sm">表示期間変更</button>
</form>
<?php if (!empty($detail->error)) { ?>
  <div class="alert alert-danger" role="alert"><?php echo $detail->error; ?></div>
<?php } ?>

<?php if (!empty($detail->results)) { ?>
  <div id="post-17425">
    <!-- 外枠 -->
    <!--div class="table-responsive　x_data_area"-->
    <div class="x_data_area">
        <!-- ロック部分 -->
        <div class="lock_box">
          <!--table class="table table-condensed data"-->
          <table class="data">
            <tr>
              <th><span>No</span></th>
              <th class="r_none"><span>会員名</span></th>
            </tr>
            <?php $number = 1; ?>
            <?php foreach ($detail->inputData as $id => $data) { ?>
              <tr>
                <!--No-->
                <td class="r_none"><p><?php echo $number; ?></p></td>
                <!--会員名-->
                <td class="r_none">
                  <p>
                    <?php
                      if (($data[0]['first_name'] === '' || $data[0]['first_name'] === null) &&
                          ($data[0]['member_id'] === '' || $data[0]['member_id'] === null)) {
                          // 紹介者の名前とIDがない場合は過去分の累計報酬として扱う
                          echo '過去の累計報酬額';
                      } else {
                          echo $data[0]['first_name'];
                      }
                  　?>
                   </p>
                 </td>
              </tr>
            <?php $number++ ; ?>
            <?php } ?>
            <tr>
                <th class="gray"><p>&nbsp;</p></th>
                <th class="gray"><p>&nbsp;</p></th>
            </tr>
            <tr>
                <td><p>&nbsp;</p></td>
                <td class="r_none"><p>&nbsp;</p></td>
            </tr>
            <tr>
                <td><p>&nbsp;</p></td>
                <td class="r_none"><p>&nbsp;</p></td>
            </tr>
            <tr>
                <td><p>&nbsp;</p></td>
                <td class="r_none"><p>&nbsp;</p></td>
            </tr>
          </table>
        </div>
        <!-- /ロック部分 -->

        <!-- 横スクロール部分 -->
        <div class="x_scroll_box">
          <table class="data">
            <tr>
              <th><span>登録日</span></th>
              <th><span>会員レベル</span></th>
              <!--yyyymm-->
              <?php foreach ($detail->allMonth as $month) { ?>
                <th><?php echo $month; ?></th>
              <?php } ?>
            </tr>
            <?php $number = 1; ?>
            <?php foreach ($detail->inputData as $id => $data) { ?>
              <tr>
                <!--登録日-->
                <td><p><?php echo $data[0]['date']; ?></p></td>
                <!--会員レベル-->
                <td><p><?php echo $data[0]['alias']; ?></p></td>
                <!--yyyymm-->
                <p>
                  <?php foreach ($detail->allMonth as $month) { ?>
                    <td class="text-right"><?php echo isset($data[$month]['price']) ? '¥' . number_format($data[$month]['price']) : '¥0'; ?></td>
                  <?php } ?>
                </p>
              </tr>
            <?php $number++ ; ?>
            <?php } ?>

            <!--glay line-->
            <tr>
                <th class="gray"><p>&nbsp;</p></th>
                <th class="gray"><p>&nbsp;</p></th>
                <?php foreach ($detail->allMonth as $month) { ?>
                    <th></th>
                <?php } ?>
            </tr>
            <tr>
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
                <td>出金申請額</td>
                <?php foreach ($detail->allMonth as $month) { ?>
                    <?php $price = isset($detail->outputData[$month]) ? abs($detail->outputData[$month]) : 0; ?>
                    <td class="text-right"><?php echo '¥' . number_format($price); ?></td>
                <?php } ?>
            </tr>
            <tr>
                 <td></td>
                 <td>表示期間内の累計報酬額</td>
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
        </table>
      </div><!--/x_scroll_box-->
    </div><!--/x_data_area-->
  </div><!--/post-17425-->

  <h3>出金申請</h3>
  <div class="card bg-light mb-3">
    <div class="card-body">
      <form class="form-inline" action="<?php echo Reward\Constant::CONFIRM_PAGE_URL; ?>" method="post">
        <input type="number" class="form-control col-4" placeholder="¥30,000" name="price" value="">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(Reward\Constant::NONCE_DETAIL_PAGE);?>">
        &nbsp;/&nbsp;<?php echo number_format($detail->totalPrice); ?>
        <?php if ($detail->totalPrice < Reward\Constant::MINIMUM_OUTPUT_PRICE) { ?>
          <button type="submit" class="btn btn-secondary" disabled>申請</button>
        <?php } else { ?>
          <button type="submit" class="btn btn-success">申請</button>
        <?php } ?>
      </form>
      <div>※出金は<?php echo number_format(Reward\Constant::OUTPUT_UNIT); ?>円単位で申請できます</div>
      <div>※出金は<?php echo number_format(Reward\Constant::MINIMUM_OUTPUT_PRICE); ?>円以上から申請できます</div>
    </div>
  </div>

  <!--戻るボタン-->
  <div class="wrap-top-page-back">
    <button type="button" class="btn-back" onclick="location.href='https://support.eventz.jp/mypage'">マイページトップへ</button>
  </div>
  <script type="text/javascript">
  // 画面いっぱいにする
  document.getElementById('main').style.width = '100%';
  </script>
<?php } else { ?>
  <div class="alert alert-warning" role="alert">指定された期間の報酬はありません。</div>
<?php } ?>
