<!-- 検索機能 -->
<div id="searchbox" class="searchbox">
     <form role="search" method="get" id="searchform" action="<?php echo home_url( '/' ); ?>">

		<div><h3>◎日付で探す</h3></div>
<div class="custom_pulldown_date">
  <!-- 年選択プルダウン  -->
      <select name="pulldown_Y" size="1" />
        　<option value="">－－</option>
      <?php
        // 年を取得
        $nowY = date("Y");
        for($i = 2017; $i<= $nowY; $i++):
      ?>
      <?php if($i=$nowY){ //今年にデフォルトチェック?>
        <option value="<?php echo $i;?>" selected="true"><?php echo $i;?></option>
      <?php } else{ ?>
        <option value="<?php echo $i;?>"><?php echo $i;?></option>
      <?php } ?>
          <?php endfor;?>
      </select>
  年
  <!-- 月選択プルダウン  -->
        <select name="pulldown_M" size="1" />
          　<option value="" selected="true">－－</option>
          <?php
            for($i = 1; $i<= 12; $i++):
          ?>
               <option value="<?php echo sprintf('%02d', $i);?>"><?php echo sprintf('%02d', $i);?></option>
          <?php endfor;?>
        </select>
  月
  <!-- 日選択プルダウン  -->
        <select name="pulldown_D" size="1" />
          　<option value="" selected="true">－－</option>
        <?php
          for($i = 1; $i<= 31; $i++):
        ?>
            <option value="<?php echo sprintf('%02d', $i);?>"><?php echo sprintf('%02d', $i);?></option>
            <?php endfor;?>
        </select>
  日

			 <div><h3>◎地域で探す</h3></div>
          <input type="hidden" name="s">
          <input type="hidden" name="post_type" value="event" />

        <div class="custom_pulldown_cat">
        <select name="event-category">
               <option value="" selected="true">地域を選択してください</option>

               <?php
                    $terms = get_terms('event-category' , 'get=all');

                    foreach($terms as $term){
                         echo '<option value="' . $term->slug . '">' .
                         esc_html($term -> name) .
                         '</option>';
                    }
               ?>
          </select>
        </div>

  <div><h3>◎フリーワードで探す</h3></div>
  <div class="free_search">
    	<input name="s" id="s" type="text" placeholder="フリーワード検索です" />
	<br>
</div>
  <div class="search_button">
    <input id="submit" type="submit" value="検索" />
		<img src="<?php echo get_template_directory_uri(); ?>/images/search.png">
  </div>
</form>
</div>
