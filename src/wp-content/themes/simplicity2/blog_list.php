<?php get_header(); ?>

<?php query_posts('post_type=post&paged='.$paged); ?>

  <?php
  if (have_posts()) : // WordPress ループ
    while (have_posts()) : the_post(); // 繰り返し処理開始
    ?>
      <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <article class="article">
          <header>
            <h2 class="entry-title"><a href="<?php the_permalink() ?>"><?php echo get_the_title(); ?></a></h2>
            <p class="post-meta">
              <?php the_time("Y年m月j日") ?>
            </p>

            <?php //固定ページ本文上ウイジェット
            if ( is_page() && is_active_sidebar( 'widget-over-page-article' ) ): ?>
              <div id="widget-over-page-article" class="widgets">
              <?php dynamic_sidebar( 'widget-over-page-article' ); ?>
              </div>
            <?php endif; ?>
        </header>

        <div id="the-content" class="entry-content">
        <?php if(has_post_thumbnail()) { the_post_thumbnail(); } ?>
        <?php //the_content('続きを読む'); //本文の呼び出し?>
        <?php the_excerpt(); //本文の抜粋の呼び出し?>
        </div>

        <footer>
          <?php get_template_part('pager-page-links');//ページリンクのページャー?>

          <?php //固定ページ本文上ウイジェット
          if ( is_page() && is_active_sidebar( 'widget-under-page-article' ) ): ?>
            <div id="widget-under-page-article" class="widgets">
            <?php dynamic_sidebar( 'widget-under-page-article' ); ?>
            </div>
          <?php endif; ?>



          <p class="footer-post-meta">
          </p>

        </footer>
        </article><!-- .article -->
      </div><!-- .page -->
    <?php
    endwhile; // 繰り返し処理終了
  else : // ここから記事が見つからなかった場合の処理 ?>
      <div class="post">
        <h2>NOT FOUND</h2>
        <p><?php echo get_theme_text_not_found_message();//見つからない時のメッセージ ?></p>
      </div>
  <?php
  endif;
  ?>

<?php get_footer(); ?>
