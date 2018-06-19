<!-- トップページのみスライドショーを表示 -->
<?php if(is_home()): ?>
  <?php
      echo do_shortcode("[metaslider id=57]");
  ?>
<?php endif; ?>