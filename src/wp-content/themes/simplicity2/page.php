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

        <?php
            // 報酬詳細画面に使う情報
            $rewardDetailPage = 15411;
            $rewardDetailFile = __DIR__ . '/reward/rewardDetail.php';
            $rewardDetailTemplateFile = __DIR__ . '/reward/rewardDetailTemplate.php';
            if ($post->ID === $rewardDetailPage && file_exists($rewardDetailFile) && SwpmMemberUtils::is_member_logged_in()) {
                // 報酬詳細画面だけの処理を読み込み
                include_once($rewardDetailFile);
                // ロジック
                $rewardDetail = new RewardDetail($wpdb, $table_prefix);
                $rewardDetail->setTemplateData();
                // テンプレートの読み込み
                include_once($rewardDetailTemplateFile);
            }
        ?>

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

<?php get_footer(); ?>
