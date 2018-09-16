<?php if (!empty($done->error)) { ?>
    <div class="alert alert-danger" role="alert"><?php echo $done->error; ?></div>
<?php } else { ?>
    <div class="alert alert-primary" role="alert">
    出金申請が完了しました。
    </div>
<?php } ?>
<a href="<?php echo Reward\Constant::DETAIL_PAGE_URL; ?>">
<button type="submit" class="btn btn-success">マイページに戻る</button>
</a>
