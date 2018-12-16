<?php 
  get_template_part('parts/list-header');
?>

<div id="premium">
  <h2><img src="http://support.eventz.jp/wp-content/uploads/2017/05/icon_hoshi02-1.svg">
    プレミアムイベント
  </img></h2>
</div>

<br>

<div id="event-list">
<?php
  if (have_events()):
    $count = 0;
    while (have_posts()) {
      the_post();
      if (is_premium()) {
        include('parts/event_list.php');
      } else {
        break;
      }
    }
?>
  <div class="clear"></div>
<?php 
  else : // ここから記事が見つからなかった場合の処理  
    get_template_part('parts/event_not_found');
  endif;
?>
</div>

<br>
<br>
<div id="event">
<h2><img src="http://support.eventz.jp/wp-content/uploads/2017/05/icon_hoshi02-1.svg">カフェ会</img></h2>
</div>
<br>

<div id="event-list">

<!-- 通常のイベント一覧 -->
<?php
if (have_events()) : // WordPress ループ
  $count = 0;
  if (is_cafe()) {
    include('parts/event_list.php');
  }

  while (have_posts()) {
    the_post();
    if (is_cafe()) {
      include('parts/event_list.php');
    }
  }
?>
  <div class="clear"></div>
<?php 
  else : // ここから記事が見つからなかった場合の処理  
    get_template_part('parts/event_not_found');
  endif;
?>
</div><!-- /#list -->

<br>
<br>
<div id="premium-k">
  <h2>
    <img src="http://support.eventz.jp/wp-content/uploads/2017/05/icon_hoshi02-1.svg">
      関西プレミアムイベント
    </img>
  </h2>
</div>
<br>

<div id="event-list">

<!-- 関西イベント一覧 -->
<?php
if (have_events()) : // WordPress ループ
  $count = 0;
  if (is_kansai_premium()) {
    include('parts/event_list.php');
  }

  while (have_posts()) {
    the_post();
    if (is_kansai_premium()) {
      include('parts/event_list.php');
    }
  }
?>
  <div class="clear"></div>
<?php 
  else : // ここから記事が見つからなかった場合の処理  
    get_template_part('parts/event_not_found');
  endif;
?>
</div><!-- /#list -->

<?php
////////////////////////////
//ボトムの広告
////////////////////////////
if (!is_home() || is_ads_top_page_visible()) ://メインページ以外は広告を出す
  get_template_part('ad-article-footer' );
endif; ?>

<?php
////////////////////////////
//インデックスリストボトムウィジェット
////////////////////////////
if ( is_active_sidebar( 'widget-index-bottom' ) ):
  echo '<div id="widget-index-bottom" class="widgets">';
  dynamic_sidebar( 'widget-index-bottom' );
  echo '</div>';
endif; ?>

<div class="terms0408" style="text-align:center; padding: 30px 0 30px 0;">
<a style="font-size: 20px !important; background: #517fa4; padding: 0.1em 3.0em;" href="https://support.eventz.jp/events/tag/cafekai?order=desc&meta_key=_eventorganiser_schedule_start_start&orderby=meta_value">
もっと見る
</a>
</div><!-- /#list -->