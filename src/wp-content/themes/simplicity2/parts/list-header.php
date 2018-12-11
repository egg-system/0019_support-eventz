<?php
  if (is_category()) {
    get_template_part('breadcrumbs');
  }
?>

<?php if (!is_home() && !is_search()) { ?>
  <h1 id="archive-title">「
    <?php if( is_category() ) { ?>
    <?php single_cat_title(); ?>
    <?php } elseif( is_tag() ) { ?>
    <?php single_tag_title(); ?>
    <?php } elseif( is_tax() ) { ?>
    <?php single_term_title(); ?>
    <?php } elseif (is_day()) { ?>
    <?php echo get_the_time( get_theme_text_ymd_format() );//年月日のフォーマットを取得 ?>
    <?php } elseif (is_month()) { ?>
    <?php echo get_the_time( get_theme_text_ym_format() );//年と月のフォーマットを取得 ?>
    <?php } elseif (is_year()) { ?>
    <?php echo get_the_time( get_theme_text_y_format() );//年のフォーマットを取得 ?>
    <?php } elseif (is_author()) { ?>
    <?php echo esc_html(get_queried_object()->display_name); ?>
    <?php } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
    Archives
    <?php } else { ?>
    Archives
    <?php } ?>
  」<?php echo get_theme_text_list();//「一覧」部分の取得 ?>
  </h1>
<?php }

  if (!is_home() || is_ads_top_page_visible())//メインページ以外は広告を出す
    get_template_part('ad-top');//記事トップ広告 
    
  if ( is_active_sidebar( 'widget-index-top' ) ) {
    echo '<div id="widget-index-top" class="widgets">';
    dynamic_sidebar( 'widget-index-top' );
    echo '</div>';
  }

  if (is_category() && //カテゴリページの時
          !is_paged() &&   //カテゴリページのトップの時
          category_description()) : //カテゴリの説明文が空でない時 ?>
<!-- カテゴリの説明文 -->
<div class="category-description"><?php echo category_description(); ?></div>
<?php endif; 
  if (is_tag() && //タグページの時
            !is_paged() &&   //タグページのトップの時
            tag_description()) : //タグの説明文が空でない時 ?>
  <!-- カテゴリの説明文 -->
  <div class="category-description tag-description"><?php echo tag_description(); ?></div>
<?php endif; ?>
<div class="button-0415-01">
  <a href="#premium">
    <div class="button-premium">
      交流会・飲み会・勉強会へ参加したい
    </div>
  </a>
  <a href="#event">
    <div class="button-kigaru">
      気軽にカフェ会へ参加したい
    </div>
  </a>
</div>

<br>

<div class="button-0415-02"><br></div>