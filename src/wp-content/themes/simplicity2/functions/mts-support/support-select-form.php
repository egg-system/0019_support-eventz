<div id="schedule-select-article">
  <h3><?php _e('Change date of list', $this->domain) ?></h3>
  <form method="get" action="">
    <input type="hidden" name="page" value="<?php echo self::PAGE_NAME ?>" />

    <?php _e('Year: ', $this->domain); ?>
    <select class="select-year" name="year">
      <?php for ($y = $this_year - 1; $y <= $this_year; $y++) {
        echo "<option value=\"$y\"";
        if ($y == $this_year) {
          echo ' selected="selected"';
        }
        echo ">$y</option>\n";
      } ?>
    </select>

    <?php _e('Month:',$this->domain); ?>
    <select class="select-month" name="month">
      <?php for ($m = 1; $m <= 12; $m++) {
        echo "<option value=\"$m\"";
        if ($m == $this_month) {
          echo ' selected="selected"';
        }
        echo ">$m</option>\n";
      } ?>
    </select>

    <input class="button-secondary" type="submit" value="<?php _e('Change monthly', $this->domain) ?>" />
    <input type="hidden" name="action" value="monthly" />
  </form>

  <?php if ($this->action == 'monthly') : ?><h3><?php echo sprintf('%4d-%02d', $theyear, $themonth) ?></h3>
  <ul class="subsubsub">
    <li><?php echo '<a href="?page=' . self::PAGE_NAME . "&year=" . date('Y', $prev_month)
          . "&month=" . date('n', $prev_month) . "&action=monthly\">$prev_str</a>"; ?> | </li>
    <li><?php echo '<a href="?page=' . self::PAGE_NAME . "&year=" . date('Y', $next_month)
            . "&month=" . date('n', $next_month) . "&action=monthly\">$next_str</a>"; ?></li>
  </ul>
  <div class="clear"> </div><?php endif; ?>
</div>