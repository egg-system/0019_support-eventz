<?php
    $count += 1;
    global $g_list_index;
    $g_list_index = $count-1;//インデックスなので-1

    //一覧リストのスタイル
    if ( is_list_style_bodies() ) {//一覧表示スタイルが本文表示
      get_template_part('entry-body');//一覧表示スタイルが本文表示の場合
    } else if ( is_list_style_large_cards() ){//大きなエントリーカードの場合
      get_template_part_card('entry-card-large');
    } else if ( is_list_style_large_card_just_for_first() ){//最初だけ大きなエントリーカードの場合
      //最初だけ大きなものであとは普通のエントリーカード
      if ( is_home() && !is_paged() && $count == 1 ) {
        get_template_part_card('entry-card-large');
      } else {
        get_template_part_card('entry-card');
      }
    } else if ( is_list_style_body_just_for_first() ){//最初だけ本文表示の場合
      //最初だけ本文表示であとは普通のエントリーカード
      if ( is_home() && !is_paged() && $count == 1 ) {
        get_template_part('entry-body');
      } else {
        get_template_part_card('entry-card');
      }
    } else {//エントリーカードか、大きなサムネイルカードの場合　★★★ここを使用★★★
      //一覧表示スタイルがカードor大きなサムネイルカード表示の場合
      get_template_part_card('entry-card');
    }

    //トップページ中間に広告を表示できるかどうか（表示するかどうか）
    if ( is_ads_list_in_middle_on_top_page_enable($count) ) {
      get_template_part('ad');
    }

    //3つ目のアイテムの下にインデックスリストミドルウィジェットを表示するか
    if ( $count == 3 && //3番目
      is_list_style_entry_type() && //表示タイプがエントリーカードタイプの時のみ
      is_active_sidebar( 'widget-index-middle' ) && //インデックスミドルに値が入っているとき
      !is_pagination_last_page() && //インデックスリストの最後のページでないとき
      is_posts_per_page_6_and_over() //1ページに表示する最大投稿数が6以上の時
    ) {
      echo '<div id="widget-index-middle" class="widgets">';
      dynamic_sidebar( 'widget-index-middle' );
      echo '</div>';
    }