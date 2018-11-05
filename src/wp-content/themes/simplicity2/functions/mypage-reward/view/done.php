<?php if (!empty($done->error)) { ?>
    <div class="alert alert-danger" role="alert"><?php echo $done->error; ?></div>
<?php } else { ?>
    <div class="alert alert-primary" role="alert">
    出金申請が完了しました。
    </div>
<?php } ?>
<a href="<?php echo Reward\Constant::DETAIL_PAGE_URL; ?>">
<button type="submit" class="btn btn-success">報酬明細画面に戻る<a/button>
</a>

<script type="text/javascript">
// 画面いっぱいにする
document.getElementById('main').style.width = '100%';
</script>
