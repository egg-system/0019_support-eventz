<?php if (!empty($confirm->error)) { ?>
    <div style="color: red;"><?php echo $confirm->error; ?></div>
    <form style="float: left;" class="form-inline" action="javascript:history.back();">
      <div class="form-group">
        <input type="hidden" class="form-control" name="price" value="">
      </div>
      <button type="submit" class="btn btn-danger">戻る</button>
    </form>
<?php } else { ?>
    <label>出金申請金額：<?php echo number_format($confirm->price); ?>円でよろしいですか？</label>
    <div>
        <form style="float: left;" class="form-inline" action="javascript:history.back();">
          <div class="form-group">
            <input type="hidden" class="form-control" name="price" value="<?php echo $confirm->price; ?>">
          </div>
          <button type="submit" class="btn btn-danger">キャンセル</button>
        </form>
        <form style="float: left;" class="form-inline" action="<?php echo Reward\Constant::DONE_PAGE_URL; ?>" method="post">
          <div class="form-group">
            <input type="hidden" class="form-control" name="price" value="<?php echo $confirm->price; ?>">
          </div>
          <button type="submit" class="btn btn-success">OK</button>
        </form>
    </div>
<?php } ?>
