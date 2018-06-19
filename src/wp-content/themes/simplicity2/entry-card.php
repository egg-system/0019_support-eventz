<?php //投稿一覧リストのループ内で呼び出されるエントリーカード ?>
<article id="post-<?php the_ID(); ?>" <?php post_class('entry cf'.(is_list_style_large_thumb_cards() ? ' entry-large-thumbnail' : '').(is_list_style_tile_thumb_cards() ? ' entry-tile' : '').( is_entry_card_style() ? ' entry-card' : '')) ?>>

<!-- カスタマイズ -->
<h2><p class="entry-snippet">
    <?php //カスタムフィールドの表示（開始時刻）
      $startdate = post_custom('_eventorganiser_schedule_start_start');
      echo date('m-d(D) H:i', strtotime($startdate)); ?>
＠
    <?php
      //カスタムフィールドの表示（場所）
      //  eo_venue_name();

      //カテゴリの表示（地域）
      $cat = '';
      $separator = ', ';
      $before = '';
      $cat = get_the_term_list( $post->ID, 'event-category', $before, $separator );
      // 表示
      echo $cat;
    ?>
</p></h2>
  <figure class="entry-thumb">
    <?php if ( is_entry_card_style() ): //デフォルトのエントリーカード表示の場合?>
      <?php if ( has_post_thumbnail() ): // サムネイルを持っているとき ?>
        <a href="<?php the_permalink(); ?>" class="entry-image entry-image-link" title="<?php the_title(); ?>"><?php the_post_thumbnail( 'thumb150', array('class' => 'entry-thumnail', 'alt' => get_the_title()) ); ?></a>
      <?php else: // サムネイルを持っていない ?>
        <a href="<?php the_permalink(); ?>" class="entry-image entry-image-link" title="<?php the_title(); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/no-image.png" alt="NO IMAGE" class="entry-thumnail no-image list-no-image" /></a>
      <?php endif; ?>
    <?php else: //大きなサムネイルカードの場合?>
      <?php if ( has_post_thumbnail() ): // サムネイルを持っているとき
        //サムネイル画像の縦横比を保存するかどうかで取得するサムネイルを変化
        $thumb = ( is_list_style_tile_thumb_cards_raw() ? 'thumb320_raw' : 'thumb320') ?>
        <a href="<?php the_permalink(); ?>" class="entry-image entry-image-link" title="<?php the_title(); ?>"><?php the_post_thumbnail($thumb , array('class' => 'entry-thumnail', 'alt' => get_the_title()) ); ?></a>
      <?php else: // サムネイルを持っていないとき ?>
        <a href="<?php the_permalink(); ?>" class="entry-image entry-image-link" title="<?php the_title(); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/no-image-320.png" alt="NO IMAGE" class="entry-thumnail no-image list-no-image" /></a>
      <?php endif; ?>
    <?php endif; ?>
  </figure><!-- /.entry-thumb -->
  <div class = pv0404>
	<?php
	 //フロントページにPV数を表示_20180325_Hayashi
	?>
	<?php if(function_exists('the_views')) {
    the_views();
	} ?>
	</div>
	<div class = pv0408>
	<?php
	echo "<br />";
	?>
	</div>
  <?php //エントリーカードのコンテンツ部分を呼び出す
  get_template_part('entry-card-content')?>
 <div class = terms0325>
	<div class = terms0408>
	<?php
	the_terms( $post->ID, 'event-tag', '', '' );
	?>
	</div>
</div>
</article>