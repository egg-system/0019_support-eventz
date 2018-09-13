<div class="alert alert-primary" role="alert">
出金申請が完了しました。
</div>
<?php if (!empty($done->error)) { ?>
    <div class="alert alert-danger" role="alert"><?php echo $done->error; ?></div>
<?php } ?>
<form style="float: left;" class="form-inline" action="<?php echo Reward\Constant::DETAIL_PAGE_URL; ?>" method="get">
  <button type="submit" class="btn btn-success">マイページに戻る</button>
</form>
