<div class="wrap">

		<div id="icon-edit" class="icon32"><br /></div>
		<h2><?php _e('Booking List', $this->domain) ?></h2>

		<?php if (!empty($this->message)) : ?><div class="<?php echo $this->errflg ? 'error' : 'updated' ?>">
			<p><?php echo $this->message ?></p>
		</div><?php endif; ?>

		<?php $this->_select_form() ?>

		<div id="booking-list">
			<form id="movies-filter" method="get">
				<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
				<?php $this->blist->display() ?>
			</form>
		</div>
	</div>