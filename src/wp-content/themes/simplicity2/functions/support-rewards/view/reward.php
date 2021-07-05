<h1 class="wp-heading-inline">報酬確認</h1>
<div id="reward-forms">
  <form 
    id="reward-form"
    method="GET"
    action="<?php echo admin_url() ?>"
  >
    <div class="form-input">
      <div>
        <label for="search_date">年月</label>
        <input 
          type="month"
          name="search_date"
          value="<?php echo $_GET['search_date'] ?>"
        />
      </div>
      <div> 
        <label for="is_only_withdrawal">出金申請のみ</label>
        <select name="is_only_withdrawal">
          <option value="OFF" <?php 
            if ($_GET['is_only_withdrawal'] !== 'ON') { 
              echo 'selected'; 
            } 
          ?>>
            OFF
          </option>
          <option value="ON" <?php 
            if ($_GET['is_only_withdrawal'] === 'ON') { 
              echo 'selected'; 
            } 
          ?>>ON</option>
        </select>
      </div>
    </div>
    <div id="support-reward-buttons">
      <input type="submit" value="検索"/>
      <a href="<?php echo admin_url(). '?page=support-reward&content-type=csv' ?>">
        csvダウンロード
      </a>
    </div>
    <input type="hidden" name="page" value="support-reward"/>
  </form>
</div>

<?php $rewardTable->display(); ?>