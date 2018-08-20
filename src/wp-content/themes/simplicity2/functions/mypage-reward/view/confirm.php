<!--TODO:bootstrapの読み込み方とタイミングを変える-->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

<?php if (!empty($confirm->error)) { ?>
    <div style="color: red;"><?php echo $confirm->error; ?></div>
<?php } else { ?>
    <label>出金申請金額：<?php echo number_format($confirm->price); ?>円でよろしいですか？</label>
    <div>
        <form style="float: left;" class="form-inline" action="<?php echo Reward\Constant::DETAIL_PAGE_URL; ?>" method="post">
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
