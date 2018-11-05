<?php

/**
 * Template Name: サイドバーなし
 *
 */
?>

<?php get_header(); ?>

  <?php get_template_part('breadcrumbs-page'); //固定ページパンくずリスト?>

  <?php
  if (have_posts()) : // WordPress ループ

    $count = 0;

    while (have_posts()) : the_post(); // 繰り返し処理開始

    $count += 1;
    global $g_list_index;
    $g_list_index = $count-1;//インデックスなので-1

    ?>
      <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <article class="article">
          <header>
            <h1 class="entry-title"><?php echo get_the_title(); ?></h1>
            <p class="post-meta">
<!--              <?php get_template_part('datetime') //投稿日と更新日?> -->

<!--              <?php get_template_part('edit-link') //編集リンク?> -->

              <?php wlw_edit_post_link('WLWで編集', '<span class="wlw-edit"><span class="fa fa-pencil-square-o fa-fw"></span>', '</span>'); ?>
            </p>

            <?php get_template_part('admin-pv');//管理者のみにPV表示?>

            <?php get_template_part('ad-top');//記事トップ広告 ?>

            <?php get_template_part('sns-buttons-top');//タイトル下の小さなシェアボタン?>

            <?php //固定ページ本文上ウイジェット
            if ( is_page() && is_active_sidebar( 'widget-over-page-article' ) ): ?>
              <div id="widget-over-page-article" class="widgets">
              <?php dynamic_sidebar( 'widget-over-page-article' ); ?>
              </div>
            <?php endif; ?>
        </header>

                <?php get_template_part('entry-eye-catch');//アイキャッチ挿入機能?>

        <div id="the-content" class="entry-content">
        <?php the_content(); //本文の呼び出し?>
        </div>

        <footer>
          <?php get_template_part('pager-page-links');//ページリンクのページャー?>

          <?php //固定ページ本文上ウイジェット
          if ( is_page() && is_active_sidebar( 'widget-under-page-article' ) ): ?>
            <div id="widget-under-page-article" class="widgets">
            <?php dynamic_sidebar( 'widget-under-page-article' ); ?>
            </div>
          <?php endif; ?>

          <?php get_template_part('ad-article-footer');//本文下広告?>

          <?php //固定ページSNSボタン上ウイジェット
          if ( is_active_sidebar( 'widget-over-page-sns-buttons' ) ): ?>
            <div id="widget-over-page-sns-buttons" class="widgets">
            <?php dynamic_sidebar( 'widget-over-page-sns-buttons' ); ?>
            </div>
          <?php endif; ?>

<!-- SNSボタンは削除 -->

          <p class="footer-post-meta">

            <?php get_template_part('author-link') //投稿者リンク?>

            <?php get_template_part('edit-link') //編集リンク?>

            <?php wlw_edit_post_link('WLWで編集', '<span class="wlw-edit"><span class="fa fa-pencil-square-o fa-fw"></span>', '</span>'); ?>
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

<!--footer-->
            </div><!-- /#main -->
          </main>

        </div><!-- /#body-in -->
      </div><!-- /#body -->

      <!-- footer -->
      <footer itemscope itemtype="http://schema.org/WPFooter">
        <div id="footer" class="main-footer">
          <div id="footer-in">

            <?php //フッターにウィジェットが一つも入っていない時とモバイルの時は表示しない
            if ( (is_active_sidebar('footer-left') ||
              is_active_sidebar('footer-center') ||
              is_active_sidebar('footer-right') ) &&
              !is_mobile() ): ?>
            <div id="footer-widget">
               <div class="footer-left">
               <?php if ( dynamic_sidebar('footer-left') ) : else : ?>
               <?php endif; ?>
               </div>
               <div class="footer-center">
               <?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar('footer-center') ) : else : ?>
               <?php endif; ?>
               </div>
               <div class="footer-right">
               <?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar('footer-right') ) : else : ?>
               <?php endif; ?>
               </div>
            </div>
          <?php endif; ?>

          <div class="clear"></div>
            <div id="copyright" class="wrapper">
              <?php //フッターメニューの設定
              if ( has_nav_menu('footer-navi') ): ?>
              <div id="footer-navi">
                <div id="footer-navi-in">
                  <?php wp_nav_menu( array( 'theme_location' => 'footer-navi' ) ); ?>
                  </div>
              </div>
              <?php endif ?>
              <div class="credit">
                <?php echo get_site_license(); //サイトのライセンス表記の取得 ?>
              </div>

              <?php if ( is_local_test() && is_responsive_test_visible() ): //ローカルかつ設定で表示になっている場合のみ?>
                <br /><a href="<?php echo get_template_directory_uri().'/responsive-test/?'.get_this_page_url(); ?>" target="_blank" rel="nofollow">レスポンシブテスト</a>
              <?php endif; ?>
            </div>
        </div><!-- /#footer-in -->
        </div><!-- /#footer -->
      </footer>
      <?php get_template_part('button-go-to-top'); //トップへ戻るボタンテンプレート?>
      <?php get_template_part('buttons-footer-mobile'); //フッターモバイルボタンのテンプレート?>
    </div><!-- /#container -->
    <?php wp_footer(); ?>
    <?php get_template_part('footer-custom-field');//カスタムフィールドの挿入（カスタムフィールド名：footer_custom）?>
    <?php get_template_part('footer-slicknav'); //SlickNav用のテンプレート（ツリー式モバイル用メニュー）?>
    <?php get_template_part('footer-javascript'); //フッターで呼び出すJavaScript用のテンプレート?>
    <?php get_template_part('analytics'); //アクセス解析用テンプレート?>
    <?php get_template_part('footer-insert'); //</body>手前のフッターにタグを挿入したいとき用のテンプレート?>
  </body>
</html>
