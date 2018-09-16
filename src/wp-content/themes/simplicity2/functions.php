<?php
// 予約システム改修用php
require_once('functions/mts-support/mts-function.php');
// 会員自動登録
require_once('functions/auto-registration/function.php');
// 報酬確認ページ
require_once('functions/support-rewards/support-reward-function.php');
// マイページの報酬確認
require_once('functions/mypage-reward/function.php');

require_once(ABSPATH . 'wp-admin/includes/file.php');//WP_Filesystemの使用
include 'lib/php-html-css-js-minifier.php'; //縮小化ライブラリ
include 'lib/customizer.php';//テーマカスタマイザー関係の関数
include 'lib/amp.php';       //AMP関係の関数
include 'lib/ad.php';        //広告関係の関数
include 'lib/sns.php';       //SNS関係の関数
include 'lib/admin.php';     //管理画面用の関数
include 'lib/utility.php';   //自作のユーティリティー関数
include 'lib/punycode.php';  //Punycode関係の関数
include 'lib/widget.php';    //ウイジェット関係の関数
include 'lib/widget-areas.php';//ウイジェットエリア関係の関数
include 'lib/custom-field.php';//カスタムフィールド関係の関数
include 'lib/auto-post-thumbnail.php'; //アイキャッチ自動設定関数
//include 'lib/external-link.php'; //外部リンク関係の関数
include 'lib/blog-card.php'; //ブログカード関係の関数
include 'lib/seo.php';       //SEO関係の関数
include 'lib/mobile.php';    //モバイル関係の関数
include 'lib/image.php';     //画像関係の関数
include 'lib/comment.php';   //コメント関係の関数
include 'lib/scripts.php';   //スクリプト関係の関数
include 'lib/qtags.php';     //クイックタグ関係の関数
//CFilteringプラグインとの連携
if ( version_compare( phpversion(), '5.3', '>=' ) ) {
  require_once 'lib/cfiltering.php';
}

//URLの正規表現
define('URL_REG', '/(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)/');

// アイキャッチ画像を有効化
add_theme_support('post-thumbnails');
//サムネイルサイズ
add_image_size('thumb100', 100, 100, true);
add_image_size('thumb150', 150, 150, true);
add_image_size('thumb320', 320, 180, true);
add_image_size('thumb320_raw', 320, 0, false);

//コンテンツの幅の指定
if ( ! isset( $content_width ) ) $content_width = 680;

//カテゴリー説明文でHTMLタグを使う
remove_filter( 'pre_term_description', 'wp_filter_kses' );

//ビジュアルエディターとテーマ表示のスタイルを合わせる
add_editor_style();

// RSS2 の feed リンクを出力
add_theme_support( 'automatic-feed-links' );

// カスタムメニューを有効化
add_theme_support( 'menus' );

// カスタムメニューの「場所」を設定
//register_nav_menu( 'header-navi', 'ヘッダーナビゲーション' );
register_nav_menus(
  array(
    'header-navi' => 'ヘッダーナビ',
    'footer-navi' => 'フッターナビ（サブメニュー不可）',
  )
);

//固定ページに抜粋を追加
add_post_type_support( 'page', 'excerpt' );

//カスタムヘッダー
add_theme_support( 'custom-header', $custom_header_defaults );

//テキストウィジェットでショートコードを使用する
add_filter('widget_text', 'do_shortcode');
add_filter('widget_text_pc_text', 'do_shortcode');
add_filter('widget_text_mobile_text', 'do_shortcode');
add_filter('widget_mobile_ad_text', 'do_shortcode');
add_filter('widget_pc_ad_text', 'do_shortcode');
add_filter('widget_pc_double_ad1_text', 'do_shortcode');
add_filter('widget_pc_double_ad2_text', 'do_shortcode');



//カスタム背景
$custom_background_defaults = array(
        'default-color' => 'ffffff',
);
add_theme_support( 'custom-background', $custom_background_defaults );

//ヘッダーに以下のようなタグが挿入されるWP4.4からの機能を解除
//<link rel='https://api.w.org/' href='http:/xxxx/wordpress/wp-json/' />
remove_action( 'wp_head', 'rest_output_link_wp_head' );

// // Webサイト全体の画像をResponsive images機能の対象から外す
// add_filter( 'wp_calculate_image_srcset', '__return_false' );

// //カスタマイズした値をCSSに反映させる
// function simplicity_customize_css(){
//   if ( is_external_custom_css_enable() && //カスタムCSSを外部ファイルに書き込む時
//        css_custum_to_css_file() ) {//外部ファイルに書き出しがうまくいったとき
//     echo '<link rel="stylesheet" href="'.get_template_directory_uri().'/css/css-custom.css">';
//     //wp_enqueue_style( 'css-custom', get_template_directory_uri().'/css/css-custom.css' );
//   } else {//ヘッダーに埋め込む時
//     get_template_part('css-custom');
//   }
// }
// add_action( 'wp_head', 'simplicity_customize_css');

/*
  get_the_modified_time()の結果がget_the_time()より古い場合はget_the_time()を返す。
  同じ場合はnullをかえす。
  それ以外はget_the_modified_time()をかえす。
*/
function get_mtime($format) {
  $mtime = get_the_modified_time('Ymd');
  $ptime = get_the_time('Ymd');
  if ($ptime > $mtime) {
    return get_the_time($format);
  } elseif ($ptime === $mtime) {
    return null;
  } else {
    return get_the_modified_time($format);
  }
}

// 抜粋の長さを変更する
function custom_excerpt_length() {
  return intval(get_excerpt_length());
}
add_filter('excerpt_length', 'custom_excerpt_length');

// 文末文字を変更する
function custom_excerpt_more($more) {
  return get_excerpt_more();
}
add_filter('excerpt_more', 'custom_excerpt_more');

//本文抜粋を取得する関数
//使用方法：http://nelog.jp/get_the_custom_excerpt
if ( !function_exists( 'get_the_custom_excerpt' ) ):
function get_the_custom_excerpt($content, $length = 70, $is_card = false) {
  global $post;
  //SEO設定のディスクリプション取得
  $description = get_meta_description_blogcard_snippet($post->ID);
  //SEO設定のディスクリプションがない場合は「抜粋」を取得
  if (!$description) {
    $description = $post->post_excerpt;
  }
  if (is_wordpress_excerpt() && $description ) {//Wordpress固有の抜粋文を使用するとき
    return  $description;
  } else {//Simplicity固有の抜粋文を使用するとき
    return get_content_excerpt($content, $length);
  }
}
endif;

//本文部分の冒頭を綺麗に抜粋する
if ( !function_exists( 'get_content_excerpt' ) ):
function get_content_excerpt($content, $length = 70){
  $content =  preg_replace('/<!--more-->.+/is', '', $content); //moreタグ以降削除
  $content =  strip_shortcodes($content);//ショートコード削除
  $content =  strip_tags($content);//タグの除去
  $content =  str_replace('&nbsp;', '', $content);//特殊文字の削除（今回はスペースのみ）
  $content =  preg_replace('/\[.+?\]/i', '', $content); //ショートコードを取り除く
  $content =  preg_replace(URL_REG, '', $content); //URLを取り除く
  // $content =  preg_replace('/\s/iu',"",$content); //余分な空白を削除
  $over    =  intval(mb_strlen($content)) > intval($length);
  $content =  mb_substr($content, 0, $length);//文字列を指定した長さで切り取る
  if ( get_excerpt_more() && $over ) {
    $content = $content.get_excerpt_more();
  }
  return $content;
}
endif;

//外部ファイルのURLに付加されるver=を取り除く
function vc_remove_wp_ver_css_js( $src ) {
    if ( strpos( $src, 'ver=' ) )
        $src = remove_query_arg( 'ver', $src );
    return $src;
}
add_filter( 'style_loader_src', 'vc_remove_wp_ver_css_js', 9999 );
add_filter( 'script_loader_src', 'vc_remove_wp_ver_css_js', 9999 );

//セルフピンバック禁止
function sp_no_self_ping( &$links ) {
    $home = home_url();
    foreach ( $links as $l => $link )
        if ( 0 === strpos( $link, $home ) )
            unset($links[$l]);
}
add_action( 'pre_ping', 'sp_no_self_ping' );

//ファビコンタグを表示
function the_favicon_tag(){
  if (is_favicon_enable()) {
    echo '<link rel="shortcut icon" type="image/x-icon" href="'.get_the_favicon_url().'" />'."\n";
  }
}

//アップルタッチアイコンを表示
function the_apple_touch_icon_tag(){
  if ( is_apple_touch_icon_enable() && is_mobile() ) {
    if ( get_apple_touch_icon_url() ) {
      echo '<link rel="apple-touch-icon-precomposed" href="'.get_apple_touch_icon_url().'" />'."\n";
    } else {
      echo '<link rel="apple-touch-icon-precomposed" href="'.get_stylesheet_directory_uri().'/images/apple-touch-icon.png" />'."\n";
    }
  }
}

//ファビコン表示(フロント)
function blog_favicon() {
  the_favicon_tag();
}
add_action('wp_head', 'blog_favicon');

//ファビコン表示(管理画面)
function admin_favicon() {
  the_favicon_tag();
}
add_action('admin_head', 'admin_favicon');

//iframeのレスポンシブ対応
if ( !function_exists( 'wrap_iframe_in_div' ) ):
function wrap_iframe_in_div($the_content) {
  if ( is_singular() ) {
    //YouTube動画にラッパーを装着
    $the_content = preg_replace('/<iframe[^>]+?youtube\.com[^<]+?<\/iframe>/is', '<div class="video-container"><div class="video">${0}</div></div>', $the_content);
    //Instagram動画にラッパーを装着
    $the_content = preg_replace('/<iframe[^>]+?instagram\.com[^<]+?<\/iframe>/is', '<div class="instagram-container"><div class="instagram">${0}</div></div>', $the_content);
    //Facebook埋め込みにラッパーを装着
    //$the_content = preg_replace('/<iframe[^>]+?www\.facebook\.com[^<]+?<\/iframe>/is', '<div class="facebook-container"><div class="facebook">${0}</div></div>', $the_content);
  }
  return $the_content;
}
endif;
add_filter('the_content','wrap_iframe_in_div');

//pixivの埋め込みの大きさ変換
if ( !function_exists( 'Simplicity_pixiv_embed_changer' ) ):
function Simplicity_pixiv_embed_changer($the_content){
  if ( is_mobile() && strstr($the_content, 'http://source.pixiv.net/source/embed.js') )  {
    $patterns = array();
    $patterns[0] = '/data-size="large"/';
    $patterns[1] = '/data-size="medium"/';
    //$patterns[2] = '/data-border="off"/';
    $replacements = array();
    $replacements[0] = 'data-size="small"';
    $replacements[1] = 'data-size="small"';
    //$replacements[2] = 'data-border="on"';
    $the_content = preg_replace($patterns, $replacements, $the_content);
  }
  elseif ( strstr($the_content, 'http://source.pixiv.net/source/embed.js') )  {
    // $patterns = array();
    // $patterns[0] = '/data-size="small"/';
    // $patterns[1] = '/data-size="medium"/';
    // //$patterns[2] = '/data-border="off"/';
    // $replacements = array();
    // $replacements[0] = 'data-size="large"';
    // $replacements[1] = 'data-size="large"';
    // //$replacements[2] = 'data-border="on"';
    // $the_content = preg_replace($patterns, $replacements, $the_content);
  }
  return $the_content;
}
endif;
add_filter('the_content','Simplicity_pixiv_embed_changer');

//サイト概要の取得
if ( !function_exists( 'get_the_description' ) ):
function get_the_description(){
  global $post;

  //抜粋を取得
  $desc = trim(strip_tags( $post->post_excerpt ));
  //投稿・固定ページにメタディスクリプションが設定してあれば取得
  if (get_meta_description_singular_page()) {
    $desc = get_meta_description_singular_page();
  }
  if ( !$desc ) {//投稿で抜粋が設定されていない場合は、110文字の冒頭の抽出分
    $desc = strip_shortcodes(get_the_custom_excerpt( $post->post_content, 150 ));
    $desc = mb_substr(str_replace(array("\r\n", "\r", "\n"), '', strip_tags($desc)), 0, 120);

  }
  $desc = htmlspecialchars($desc);
  return $desc;
}
endif;

//投稿・固定ページのメタキーワードの取得
if ( !function_exists( 'get_the_keywores' ) ):
function get_the_keywores(){
  global $post;
  $keywords = get_meta_keywords_singular_page();
  if (!$keywords) {
    $categories = get_the_category($post->ID);
    $category_names = array();
    foreach($categories as $category):
      array_push( $category_names, $category -> cat_name);
    endforeach ;
    $keywords = implode($category_names, ',');
  }
  return $keywords;
}
endif;

//最新記事の投稿IDを取得する
if ( !function_exists( 'get_the_latest_ID' ) ):
function get_the_latest_ID() {
  global $wpdb;
  $row = $wpdb->get_row("SELECT ID FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish' ORDER BY post_date DESC");
  return !empty( $row ) ? $row->ID : 0;
}
endif;

//WordPress の投稿スラッグを自動的に生成する
if ( !function_exists( 'auto_post_slug' ) ):
function auto_post_slug( $slug, $post_ID, $post_status, $post_type ) {
  $type = utf8_uri_encode( $post_type );
  // if ( empty( $post_ID ) ){//IDがまだ指定されていないとき
  //   $slug = $type . '-' . strval(get_the_latest_ID() + 1); //最新記事のIDに＋1
  // } else
  if ( preg_match( '/(%[0-9a-f]{2})+/', $slug ) &&
     ( $post_type == 'post' || $post_type == 'page') ) {//投稿もしくは固定ページのときのみ実行する
    $slug = $type . '-' . $post_ID;
  }
  return $slug;
}
endif;
if ( !is_japanese_slug_enable()) {
  add_filter( 'wp_unique_post_slug', 'auto_post_slug', 10, 4  );
}
// header('Content-Type: text/plain; charset=utf-8');
// for ( $i = 0; $i < 3; $i++ ) { $my_post = array( 'post_title' => 'あいう', 'post_content' => "かきく + " . date( 'r' ), 'post_status' => 'publish', 'post_author' => 1, 'post_category' => array( 1 ) ); $my_post2 = $my_post; $my_post2['post_title'] = 'ABCDEFG'; $my_id = wp_insert_post( $my_post ); $my_id2 = wp_insert_post( $my_post2 ); $my_slug = get_post( $my_id )->post_name; $my_slug2 = get_post( $my_id2 )->post_name; echo "<div>id: $my_id = slug: $my_slug</div>"; echo "<div>id2: $my_id2 = slug2: $my_slug2</div>"; }

//投稿ページ以外ではhentryクラスを削除する関数
function remove_hentry( $classes ) {
  if ( !is_single() ) {
    $classes = array_diff($classes, array('hentry'));
  }
  //これらのクラスが含まれたページではhentryを削除する
  $ng_classes = array('type-forum', 'type-topic');//ここに追加していく
  $is_include = false;
  foreach ($ng_classes as $ng_class) {
    //NGのクラス名が含まれていないか調べる
    if ( in_array($ng_class, $classes) ) {
      $is_include = true;
    }
  }
  //含まれていたらhentryを削除する
  if ($is_include) {
    $classes = array_diff($classes, array('hentry'));
  }
  return $classes;
}
add_filter('post_class', 'remove_hentry');

//functions.phpが有るローカルパスを取得
function get_simplicity_local_dir(){
  return str_replace('\\','/', dirname(__FILE__));//置換しているのはWindows環境対策
}

//子テーマ内に指定のファイルがあるかどうか調べる
//ファイルがあった場合は子テーマ内ファイルのローカルパスを（true）
//ファイルが存在しなかった場合はfalseを返す
function file_exists_in_child_theme($filename){
  $dir = get_simplicity_local_dir();
  $theme_dir_uri = get_template_directory_uri();//親テーマのディレクトリURIを取得
  $child_theme_dir_uri = get_stylesheet_directory_uri();//子テーマのディレクトリURIの取得
  if ($theme_dir_uri == $child_theme_dir_uri) return;//同一の場合は子テーマが存在しないのでfalseを返す
  preg_match('/[^\/]+$/i', $theme_dir_uri, $m);//親テーマのディレクトリ名のみ取得
  $theme_dir_name = $m[0];
  preg_match('/[^\/]+$/i', $child_theme_dir_uri, $m);//子テーマのディレクトリ名のみ取得
  $child_theme_dir_name = $m[0];
  $path = preg_replace('/'.$theme_dir_name.'$/i', $child_theme_dir_name, $dir, 1);//文末のディレクトリ名だけ置換
  $path = $path.'/'.$filename;//ローカルパスの作成
  if ( file_exists($path) ) {
    return $path;//ファイルが存在していたらファイルのローカルパスを返す
  }
}

//スキンファイルリストの並べ替え用の関数
function skin_files_comp($a, $b) {
  $f1 = (float)$a['priority'];
  $f2 = (float)$b['priority'];
  //優先度（priority）で比較する
  if ($f1 == $f2) {
      return 0;
  }
  return ($f1 < $f2) ? -1 : 1;
}

//フォルダ以下のファイルをすべて取得
function get_file_list($dir) {
  $list = array();
  $files = scandir($dir);
  foreach($files as $file){
    if($file == '.' || $file == '..'){
      continue;
    } else if (is_file($dir . $file)){
      $list[] = $dir . $file;
    } else if( is_dir($dir . $file) ) {
        //$list[] = $dir;
      $list = array_merge($list, get_file_list($dir . $file . DIRECTORY_SEPARATOR));
    }
  }
  return $list;
}

//スキンとなるファイルの取得
function get_skin_files(){
  define( 'FS_METHOD', 'direct' );

  $parent = true;
  // 子テーマで 親skins の取得有無の設定
  if(function_exists('include_parent_skins')){
    $parent = include_parent_skins();
  }

  $files  = array();
  $child_files  = array();
  $parent_files  = array();

  //子skinsフォルダ内を検索
  $dir = get_stylesheet_directory().'/skins/';
  if(is_child_theme() && file_exists($dir)){
    $child_files = get_file_list($dir);
  }

  //親skinsフォルダ内を検索
  if ( $parent || !is_child_theme() ){//排除フラグが立っていないときと親テーマのときは取得
    $dir = get_template_directory().'/skins/';
    $parent_files = get_file_list($dir);
  }

  //親テーマと子テーマのファイル配列をマージ
  $files = array_merge( $child_files, $parent_files );

  //置換DIR
  $this_dir = str_replace('\\', '/', dirname(__FILE__));
  $this_ary = explode('/', $this_dir);
  array_pop($this_ary);
  $search = implode ('/',$this_ary);

  //置換URI
  $uri_dir = get_template_directory_uri();
  $uri_ary = explode('/', $uri_dir);
  array_pop($uri_ary);
  $replace = implode ('/',$uri_ary);

  $results = array();
  foreach($files as $pathname){
    $pathname = str_replace('\\', '/', $pathname);

    if (preg_match('/([a-zA-Z0-9\-_]+).style\.css$/i', $pathname, $matches)){//フォルダ名の正規表現が[a-zA-Z\-_]+のとき
      $dir_name = strip_tags($matches[1]);
      if ( WP_Filesystem() ) {//WP_Filesystemの初期化
        global $wp_filesystem;//$wp_filesystemオブジェクトの呼び出し
        $css = $wp_filesystem->get_contents($pathname);//$wp_filesystemオブジェクトのメソッドとして呼び出す
        if (preg_match('/Name: *(.+)/i', $css, $matches)) {//CSSファイルの中にName:の記述があるとき
          if (preg_match('/Priority: *(.+)/i', $css, $m)) {//優先度（順番）が設定されている場合は順番取得
            $priority = floatval($m[1]);
          } else {
            $priority = 9999;
          }
          $name = trim(strip_tags($matches[1]));
          if ( is_parts_skin_file($pathname) )//パーツスキンの場合
            $name = '[P] '.$name;

          $file_path = str_replace($search, $replace , $pathname);
          $file_path = remove_protocol($file_path);
          //返り値の設定
          $results[] = array(
            'name' => $name,
            'dir' => $dir_name,
            'priority' => $priority,
            'path' => $file_path,
          );
        }
      }
    }
  }
  uasort($results, 'skin_files_comp');//スキンを優先度順に並び替え

  return $results;
}

//WP_Queryの引数を取得
if ( !function_exists( 'get_related_wp_query_args' ) ):
function get_related_wp_query_args(){
  global $post;
  if ( is_related_entry_association_category() ) {
    //カテゴリ情報から関連記事をランダムに呼び出す
    $categories = get_the_category($post->ID);
    $category_IDs = array();
    foreach($categories as $category):
      array_push( $category_IDs, $category -> cat_ID);
    endforeach ;
    if ( empty($category_IDs) ) return;
    return $args = array(
      'post__not_in' => array($post -> ID),
      'posts_per_page'=> intval(get_related_entry_count()),
      'category__in' => $category_IDs,
      'orderby' => 'rand',
    );
  } else {
    //タグ情報から関連記事をランダムに呼び出す
    $tags = wp_get_post_tags($post->ID);
    $tag_IDs = array();
    foreach($tags as $tag):
      array_push( $tag_IDs, $tag -> term_id);
    endforeach ;
    if ( empty($tag_IDs) ) return;
    return $args = array(
      'post__not_in' => array($post -> ID),
      'posts_per_page'=> intval(get_related_entry_count()),
      'tag__in' => $tag_IDs,
      'orderby' => 'rand',
    );
  }
}
endif;

//アップロード可能なファイルの設定
function my_upload_mimes($mimes = array()) {
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}
add_filter('upload_mimes', 'my_upload_mimes');

//投稿ページと固定ページを一覧リストに表示する
function post_page_all( $query ) {
  if ( is_admin() || ! $query->is_main_query() )
    return;

  if ( $query->is_home() ) {
    $query->set( 'post_type', array( 'post', 'page' ) );
    return;
  }
}
if ( is_page_include_in_list() ) {//固定ページをリスト表示する設定のとき
  add_action( 'pre_get_posts', 'post_page_all' );
}

//アップデートチェックの初期化
if ( is_auto_update_enable() ) {//テーマのオートアップデート機能が有効のとき
  require 'theme-update-checker.php'; //ライブラリのパス
  $example_update_checker = new ThemeUpdateChecker(
    'simplicity2', //テーマフォルダ名
    'http://wp-simplicity.com/wp-content/themes/simplicity/update-info2.json' //JSONファイルのURL
  );
}

// functions.phpに追加(子テーマのでも可)
function my_comment_form_defaults($defaults){
    $defaults['comment_field'] = '<p class="comment-form-comment"><textarea id="comment" class="expanding" name="comment" cols="45" rows="8" aria-required="true" placeholder=""></textarea></p>';
    return $defaults;
}
add_filter( "comment_form_defaults", "my_comment_form_defaults");

//本文から必要のないものを取り除くフック
if ( !function_exists( 'remove_unnecessary_sentences' ) ):
function remove_unnecessary_sentences($the_content) {
  if ( is_singular() ) {
    //border属性は不要
    $the_content = str_replace(' border="0"', '', $the_content);
    $the_content = str_replace(" border='0'", '', $the_content);
  }
  return $the_content;
}
endif;
add_filter('the_content','remove_unnecessary_sentences');

//本文から必要のないものを取り除くフック
if ( !function_exists( 'scrollable_responsive_table' ) ):
function scrollable_responsive_table($the_content) {
  $the_content = preg_replace('/<table/i', '<div class="scrollable-table"><table', $the_content);
  $the_content = preg_replace('/<\/table>/i', '</table></div>', $the_content);
  return $the_content;
}
endif;
if (is_scrollable_table_enable()) {
  add_filter('the_content','scrollable_responsive_table');
}


//カスタムフィールドのショートコードをロケーションURIに置換
function replace_directory_uri($code){
  $code = str_replace('[template_directory_uri]', get_template_directory_uri(), $code);
  $code = str_replace('[stylesheet_directory_uri]', get_stylesheet_directory_uri(), $code);
  $code = str_replace('<?php echo template_directory_uri(); ?>', get_template_directory_uri(), $code);
  $code = str_replace('<?php echo get_stylesheet_directory_uri(); ?>', get_stylesheet_directory_uri(), $code);
  return $code;
}

/*
//現在採用してない
//画像が出てきたらキャプション表示用のラッパーを装着
function wrap_images_for_hover($the_content) {
  if ( is_singular() ) {
    //Alt属性値のある画像タグをラッパー付きのタグで置換する
    $the_content = preg_replace(
      '/(<img.+?alt=[\'"]([^\'"]+?)[\'"].+?>)/i',
      '<a class="hover-image">${1}<div class="details"><span class="info">${2}</span></div></a>',
      $the_content);
    //$the_content = preg_replace('/<\/?p>/i', '', $the_content);
  }
  return $the_content;
}
if ( is_alt_hover_effect_enable() ) {
  add_filter('the_content','wrap_images_for_hover',100);
}
*/

//Simplicityのビジュアルエディタースタイル
function simplicity_theme_add_editor_styles() {
  add_editor_style( 'css/admin-editor.css' );
}
if ( is_admin_editor_enable() ) {
  add_action( 'admin_init', 'simplicity_theme_add_editor_styles' );
}

//テーマカスタマイザーのファイルを外部ファイルに書き出す
function css_custum_to_css_file(){
  if ( WP_Filesystem() ) {//WP_Filesystemの初期化
    global $wp_filesystem;//$wp_filesystemオブジェクトの呼び出し

    //カスタマイザーのカスタムCSSを取得
    ob_start();//バッファリング
    get_template_part('css-custom');//カスタムテンプレートの呼び出し
    $css_settings = ob_get_clean();
    $css_settings = str_replace('<style type="text/css">', '', $css_settings);
    $css_settings = str_replace('</style>', '', $css_settings);
    //var_dump($css_settings);

    //CSSの縮小化
    $css_settings = minify_css($css_settings);

    $wp_filesystem->put_contents(
      get_simplicity_local_dir().'/css/css-custom.css',
      $css_settings,
      0644
    );
    return true;
  }
}

//パーツスキンファイルが存在しているか
function is_parts_skin_file($skin_file){
  if ( get_pearts_base_skin($skin_file) ) {
    return true;
  }
}

//パーツスキンファイルを取得（ないときは空を返す）
function get_pearts_base_skin($skin_file = null){
  if ( !$skin_file )
    $skin_file = get_skin_file();
  //var_dump($skin_file);
  if ( $skin_file ) {
    $path_arr = explode('/', $skin_file);
    //配列を逆順に並び替え
    $reversed_path_arr = array_reverse($path_arr);
    //スキンのフォルダ名を取得
    $skin_dir_name = $reversed_path_arr[1];
    if ( preg_match('/^_/', $skin_dir_name, $m) ) {
      return $skin_file;
    }
  }
}

//スキンフォルダ内のJavaScriptファイルのURLを取得
function get_skins_js_uri(){
  $path_parts = pathinfo( get_skin_file() );
  if ( isset( $path_parts["dirname"] ) ) {
    return $path_parts["dirname"] . '/javascript.js';
  }
}

//スキンフォルダ内のJavaScriptファイルのローカルパスを取得
function get_skins_js_local_dir(){
  if ( get_skins_js_uri() ) {
    $dir = get_skins_js_uri();
    $stylesheet_directory_uri = get_stylesheet_directory_uri();
    $template_directory_uri = get_template_directory_uri();
    // $stylesheet_directory_uri = remove_protocol(get_stylesheet_directory_uri());
    // $template_directory_uri = remove_protocol(get_template_directory_uri());
    if( strpos( $dir , $stylesheet_directory_uri ) !== false ){
      $dir = str_replace( $stylesheet_directory_uri, get_stylesheet_directory(), $dir );
    } else {
      $dir = str_replace( $template_directory_uri, get_template_directory(), $dir );
    }
    // if( strpos( $dir , get_stylesheet_directory_uri() ) !== false ){
    //   $dir = str_replace( get_stylesheet_directory_uri(), get_stylesheet_directory(), $dir );
    // } else {
    //   $dir = str_replace( get_template_directory_uri(), get_template_directory(), $dir );
    // }
    // header('Content-Type: text/plain; charset=utf-8');
    // var_dump($dir);
    return str_replace( '\\', '/', $dir);
  }
}

//Wordpressテーマフォルダのローカルパスを取得
function get_theme_local_dir(){
  $dir = get_simplicity_local_dir();
  $dir_arr = explode('/', $dir);
  array_pop($dir_arr);//Simplicityディレクトリを取り除く
  $theme_dir = implode('/', $dir_arr);
  return $theme_dir;
}

//Wordpressテーマフォルダのパスを取得
function get_theme_dir(){
  $dir = get_stylesheet_directory_uri();
  $dir_arr = explode('/', $dir);
  array_pop($dir_arr);//Simplicityディレクトリを取り除く
  $theme_dir = implode('/', $dir_arr);
  return $theme_dir;
}

//統一パーツスキンとなるファイルの取得
function get_parts_skin_file_uri(){
  //define( 'FS_METHOD', 'direct' );
  define( 'MERGED_CSS', '_merged_.css' );

  $skin_file = get_pearts_base_skin();
  if ( !$skin_file ) return;//パーツスキンじゃないときは
  $skin_arr = explode('/', $skin_file);
  array_pop($skin_arr);//CSSファイル名の除去
  $skin_dir = implode('/', $skin_arr);
  //var_dump(get_theme_local_dir());
  $theme_dir = get_theme_dir();
  //$theme_dir = remove_protocol(get_theme_dir());
  //スキンファイルをローカルパスに変換
  $skin_local_file = str_replace(
    //get_theme_dir(),
    $theme_dir,
    get_theme_local_dir(),
    $skin_file
  );
  //URLをローカルパスに変換
  $skin_local_dir = str_replace(
    //get_theme_dir(),
    $theme_dir,
    get_theme_local_dir(),
    $skin_dir
  );
  //ディレクトリ内の全てのCSSファイルを取得
  //var_dump($skin_local_dir);
  $all_files = get_file_list($skin_local_dir.'/');
  //var_dump($all_files);

  //利用するパーツスキンファイルを取得
  $skin_pearts_local_files = array();
  foreach($all_files as $pathname){
    $pathname = str_replace('\\', '/', $pathname);

    if (preg_match('/\.css$/i', $pathname, $matches)){//フォルダ名の正規表現が[a-zA-Z\-_]+のとき
      //結合ファイルの時は読み込まない
      if (preg_match('/\/_merged_\.css$/i', $pathname, $m)) continue;
      //スキンのstyle.cssは先頭にするため読み込まない
      if ( !preg_match('/\/style\.css$/i', $pathname, $m) ) {
        $skin_pearts_local_files[] = $pathname;
      }

    }
  }
  //文字列順に並び替え
  sort($skin_pearts_local_files, SORT_STRING);
  //先頭にstyle.cssを追加
  $skin_pearts_local_files = array_merge(
    array($skin_local_file),
    $skin_pearts_local_files
  );
  //var_dump($skin_pearts_local_files);

  //パーツスキンファイルを開いて全てまとめる
  $merged_css_text = '';
  foreach($skin_pearts_local_files as $pathname){
    if ( WP_Filesystem() ) {//WP_Filesystemの初期化
      global $wp_filesystem;//$wp_filesystemオブジェクトの呼び出し
      //コメントで位置を表示するためのファイル名取得
      $comment_file_name = str_replace($skin_local_dir.'/', '', $pathname);
      $css = $wp_filesystem->get_contents($pathname);//ファイルの読み込み
      $merged_css_text .=
        "/****************************\r\n".
        "** File：".$comment_file_name."\r\n".
        "****************************/\r\n".
        $css."\r\n";//CSSの結合
    }
  }
  //var_dump($merged_css_text);

  $merged_css_file = $skin_local_dir.'/'.MERGED_CSS;
  if ( WP_Filesystem() ) {//WP_Filesystemの初期化
    global $wp_filesystem;//$wp_filesystemオブジェクトの呼び出し
    $wp_filesystem->put_contents(
      $merged_css_file,
      $merged_css_text,
      0644
    );
  }
  if ( !file_exists($merged_css_file) ) return;//ファイルが存在しないときnull
  return str_replace(
           get_theme_local_dir(),
           get_theme_dir(),
           $merged_css_file);//成功した時はファイルパスを渡す
}

//レスポンシブなページネーションを作成する
if ( !function_exists( 'responsive_pagination' ) ):
function responsive_pagination($pages = '', $range = 4){
  $showitems = ($range * 2)+1;

  global $paged;
  if(empty($paged)) $paged = 1;

  //ページ情報の取得
  if($pages == '') {
    global $wp_query;
    $pages = $wp_query->max_num_pages;
    if(!$pages){
      $pages = 1;
    }
  }

  if(1 != $pages) {
    echo '<ul class="pagination" role="menubar" aria-label="Pagination">';
    //先頭へ
    echo '<li class="first"><a href="'.get_pagenum_link(1).'"><span>First</span></a></li>';
    //1つ戻る
    echo '<li class="previous"><a href="'.get_pagenum_link($paged - 1).'"><span>Previous</span></a></li>';
    //番号つきページ送りボタン
    for ($i=1; $i <= $pages; $i++)     {
      if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) ))       {
        echo ($paged == $i)? '<li class="current"><a>'.$i.'</a></li>':'<li><a href="'.get_pagenum_link($i).'" class="inactive" >'.$i.'</a></li>';
      }
    }
    //1つ進む
    if ( $pages == $paged ) {
      $next_page_num = $paged;
    } else {
      $next_page_num = $paged + 1;
    }


    echo '<li class="next"><a href="'.get_pagenum_link($next_page_num).'"><span>Next</span></a></li>';
    //最後尾へ
    echo '<li class="last"><a href="'.get_pagenum_link($pages).'"><span>Last</span></a></li>';
    echo '</ul>';
  }
}
endif;

//インデックスページで最初のエントリーかどうか
//グローバル変数を使うので注意
//グローバル変数（$g_list_index）は、list.phpのみで指定されています
function is_list_index_first(){
  global $g_list_index;
  return ($g_list_index == 0) && is_home() && !is_paged();
}

//エントリーカードスタイルを利用する設定の場合
function is_entry_card_style(){
  return is_list_style_entry_cards() || is_list_style_large_card_just_for_first() || is_list_style_body_just_for_first();
}

//bodyタグに追加するクラス名
if ( !function_exists( 'body_class_names' ) ):
function body_class_names($classes) {
  if ( is_page_type_default() ) {
    //デフォルトは何もしない
  } elseif ( is_page_type_column1_narrow() ) {
    $classes[] = 'page-type-column1 page-type-narrow';
  } elseif ( is_page_type_column1_wide() ) {
    $classes[] = 'page-type-column1 page-type-wide';
  } elseif ( is_page_type_content_only_narrow() ) {
    $classes[] = 'page-type-content-only page-type-narrow';
  } elseif ( is_page_type_content_only_wide() ) {
    $classes[] = 'page-type-content-only page-type-wide';
  }
  return $classes;
}
endif;
add_filter('body_class', 'body_class_names');

//子テーマを利用しているか
function is_child_theme_enable(){
  return get_template_directory_uri() != get_stylesheet_directory_uri();
}

//ヘッダーでユニバーサルアナリティクスコードの呼び出し
function add_universal_analytics_code(){
  get_template_part('analytics-universal');
}
if ( is_analytics_universal() ) {
  add_action('wp_head', 'add_universal_analytics_code', 11);
}

//HTML5で警告が出てしまう部分をできるだけ修正する
if ( !function_exists( 'simplicity_html5_fix' ) ):
function simplicity_html5_fix($the_content){
  //</div>に</p></div>が追加されてしまう
  //http://tenman.info/labo/snip/archives/5197
  $the_content = str_replace( '</p></div>','</div>', $the_content );
  //Alt属性がないIMGタグにalt=""を追加する
  $the_content = preg_replace('/<img((?![^>]*alt=)[^>]*)>/i', '<img alt=""${1}>', $the_content);
  return $the_content;
}
endif;
add_filter('the_content', 'simplicity_html5_fix');
add_filter('widget_text', 'simplicity_html5_fix');
add_filter('widget_text_pc_text', 'simplicity_html5_fix');
add_filter('widget_text_mobile_text', 'simplicity_html5_fix');

//現在のカテゴリをカンマ区切りテキストで取得する
if ( !function_exists( 'get_category_ids' ) ):
function get_category_ids(){
  if ( is_single() ) {//投稿ページでは全カテゴリー取得
    $categories = get_the_category();
    $category_IDs = array();
    foreach($categories as $category):
      array_push( $category_IDs, $category -> cat_ID);
    endforeach ;
    return $category_IDs;
  } elseif ( is_category() ) {//カテゴリページではトップカテゴリーのみ取得
    $categories = get_the_category();
    $cat_now = $categories[0];
    return array( $cat_now->cat_ID );
  }
  return null;
}
endif;

//モバイルで1ページに表示する最大投稿数を設定する
if ( !function_exists( 'set_posts_per_page_mobile' ) ):
function set_posts_per_page_mobile( $query ) {
  if ( is_mobile() && !is_admin() && $query->is_main_query() ) {
      $query->set( 'posts_per_page', get_posts_per_page_mobile() );
  }
}
endif;
add_action( 'pre_get_posts', 'set_posts_per_page_mobile' );

//Facebookの埋め込みの不要なスクリプトを除去する
if ( !function_exists( 'remove_facebook_embed_scripts' ) ):
function remove_facebook_embed_scripts($the_content){
  //埋め込みタグのスクリプトを空文字に置換する
  $the_content = preg_replace('/<div id="fb-root"><\/div><script>.+?connect\.facebook\.net.+?<\/script>/i', '', $the_content);
  return $the_content;
}
endif;
add_filter('the_content', 'remove_facebook_embed_scripts');

// //テーブルのレスポンシブ
// if ( !function_exists( 'wrap_table_elements' ) ):
// function wrap_table_elements($the_content){
//   //埋め込みタグのスクリプトを空文字に置換する
//   $the_content = str_replace('<table', '<div class="table-wrap"><table', $the_content);
//   $the_content = str_replace('</table>', '</table></div>', $the_content);
//   return $the_content;
// }
// endif;
// add_filter('the_content', 'wrap_table_elements');

//ページが分割ページか
function is_page_multi(){
  global $numpages;
  return $numpages != 1;
}

//分割ページの何ページ目か
function get_multi_page_number() {
  $paged = (get_query_var('page')) ? get_query_var('page') : 1;
  return $paged;
}

// //レンダリングをブロックするスクリプトリソースを遅れて読み込む
// if ( !function_exists( 'add_defer_to_enqueue_script' ) ):
// function add_defer_to_enqueue_script( $url ) {
//     if (  FALSE === strpos( $url, '.js' ) ) return $url;
//     //if ( strpos( $url, 'jquery.js' ) ) return $url;
//     return "$url' defer='defer";
// }
// endif;
// add_filter( 'clean_url', 'add_defer_to_enqueue_script', 11, 1 );

//エントリーカード全体をリンク化する
if ( !function_exists( 'get_template_part_card' ) ):
function get_template_part_card($template_name){
  ob_start();//バッファリング
  get_template_part($template_name);//テンプレートの呼び出し
  $template = ob_get_clean();//テンプレート内容を変数に代入
  /*
  $template = preg_replace('/<a [^>]+?>/i', '', $template);
  $template = str_replace('</a>', '', $template);

  $template = '<a class="hover-card" href="'.get_the_permalink().'">'.$template.'</a>';
  */
  //エントリーカードをカード化する場合はaタグを削除して全体をa.hover-cardで囲む
  $template = wrap_entry_card($template);
  echo $template;
}
endif;

//文字列内のaタグを削除して全体をa.hover-cardで囲む
if ( !function_exists( 'wrap_entry_card' ) ):
function wrap_entry_card($template, $url = null, $is_target_blank = false, $is_nofollow = false, $additional_classes = null){
  if ( is_wraped_entry_card() ) {
    $template = preg_replace('/<a [^>]+?>/i', '', $template);
    $template = str_replace('</a>', '', $template);

    $class = null;
    if ( !$url ) {
      //$class = ' hover-blog-card';
      $url = get_the_permalink();
    }

    $target = null;
    if ( $is_target_blank ) {
      $target = ' target="_blank"';
    }

    //コメント内でブログカード呼び出しが行われた際はnofollowをつける
    $nofollow = $is_nofollow ? ' rel="nofollow"' : null;

    //$blog_card_hover_class = $is_blog_card ? ' hover-blog-card' : null;

    //var_dump($template);
    //$template = '<a class="hover-card" href="'.$url.'"'.$target.'><object>'.$template.'</object></a>';
    $template = '<a class="hover-card'.$additional_classes.'" href="'.$url.'"'.$target.$nofollow.'>'.$template.'</a>';
    //$template = '<span>'.$template.'</span>';
  }
  return $template;
}
endif;

// function category_classize($cat_id) {
//   return 'category-'.$cat_id;
// };

// function get_category_id_classes(){
//   if ( is_single() ) {
//     $cat_ids = get_category_ids();
//     $cat_ids = array_map('category_classize', $cat_ids);
//     if ( $cat_ids ) {
//       return implode(' ', $cat_ids);
//     }
//   }
// }

//カテゴリIDクラスをbodyクラスに含める
if ( !function_exists( 'add_category_id_classes_to_body_classes' ) ):
function add_category_id_classes_to_body_classes($classes) {
  global $post;
  if ( is_single() ) {
    foreach((get_the_category($post->ID)) as $category)
      $classes[] = 'categoryid-'.$category->cat_ID;
  }
  return $classes;
}
endif;
add_filter('body_class', 'add_category_id_classes_to_body_classes');

// //カテゴリスラッグクラスをbodyクラスに含める
// function add_category_slug_classes_to_body_classes($classes) {
//   global $post;
//   foreach((get_the_category($post->ID)) as $category)
//      $classes[] = $category->category_nicename;
//   return $classes;
// }
// add_filter('body_class', 'add_category_slug_classes_to_body_classes');

if ( !function_exists( 'defer_async_scripts' ) ):
function defer_async_scripts( $tag, $handle, $src ) {

  //var_dump($handle);
  // The handles of the enqueued scripts we want to defer
  $async_defer = array(
    //とりあえず影響が計り知れないのでコメントアウト
    // 'jquery-core',
    // 'jquery-migrate',
  );
  $async_scripts = array(
    'comment-reply',
    'lity-js',
    'lightbox-js',
  );
  $defer_scripts = array(
    'admin-bar',
    'simplicity-js',
    'simplicity-child-js',
    'jquery-lazyload-js',
    //'crayon_js',

  );
    if ( in_array( $handle, $async_defer ) ) {
        return '<script src="' . $src . '" async defer></script>' . "\n";
    }
    if ( in_array( $handle, $defer_scripts ) ) {
        return '<script src="' . $src . '" defer></script>' . "\n";
    }
    if ( in_array( $handle, $async_scripts ) ) {
        return '<script src="' . $src . '" async></script>' . "\n";
    }

    return $tag;
}
endif;
add_filter( 'script_loader_tag', 'defer_async_scripts', 10, 3 );

//Wordpress管理画面でJavaScriptファイルも編集できるようにする wp4.4以降
if ( !function_exists( 'add_js_to_wp_theme_editor_filetypes' ) ):
function add_js_to_wp_theme_editor_filetypes($default_types){
  $default_types[] = 'js';
  return $default_types;
}
endif;
add_filter('wp_theme_editor_filetypes', 'add_js_to_wp_theme_editor_filetypes');

// -------------------------以下、カスタマイズ-----------------------------------

// トップページにeventのみを表示させる
function my_search_filter($query) {
  if (is_home() && $query->is_main_query() ) {
    // イベントのみ
    $query->set( 'post_type', 'event' );
//    $query->set( 'slug', 'premium' );　・・・ダメ

/*
$query->set('post_type',
                     array(
                       'taxonomy' => 'event',
                 			'slug'    => 'premium',
    )
);
*/

    // 本日日付を取得
    $currnet_date = date_i18n( 'y/m/d' );
    //echo $currnet_date;
   // 1週間後の日付を取得
   $aweeklater = date( 'y/m/d', strtotime( '+7 days', current_time('timestamp') ) );
   //echo $aweeklater;

//    $query->set( 'posts_per_page', -1 );
     $query->set( 'orderby', 'meta_value' );
     $query->set( 'meta_key', '_eventorganiser_schedule_start_start' );
     $query->set( 'order', 'ASC' );

     $query->set('meta_query',
                      array(
                          array(
                            'key' => '_eventorganiser_schedule_start_start', //カスタムフィールドを指定
                            'value' => array($currnet_date, $aweeklater), //本日日付と1週間後を設定
                            'compare' => 'BETWEEN', //本日日付と1週間後の間
                            'type' => 'DATE' //フォーマットは日付
         )
       )
    );

/*
     $query->set('meta_query',
                      array(
                          array(
                            'key' => '_eventorganiser_schedule_start_start', //カスタムフィールドを指定
                            'value' => $currnet_date, //「ローカルの日付」と比較
                            'compare' => '>=', //より多いか等しい
                            'type' => 'DATE' //フォーマットは日付
         )
       )
    );
    $query->set('meta_query',
                     array(
                         array(
                           'key' => '_eventorganiser_schedule_start_start', //カスタムフィールドを指定
                           'value' => $aweeklater, //「ローカルの日付」と比較
                           'compare' => '<', //より多いか等しい
                           'type' => 'DATE' //フォーマットは日付
        )
      )
   );
*/
    // 並び替えに使うカスタムフィールド(イベント開始時間)を指定する
//    $query->set('meta_key', '_eventorganiser_schedule_start_start');
    // 指定
//    $query->set('orderby', 'meta_value_num');
    // 降順
//    $query->set('order', 'DESC');

  }
}
add_action( 'pre_get_posts', 'my_search_filter' );

// ショートコードでphpファイルを呼び出す
function my_php_Include($params = array()) {
  extract(shortcode_atts(array('file' => 'default'), $params));
  ob_start();
  include(STYLESHEETPATH . "/$file.php");
  return ob_get_clean();
}
add_shortcode('myphp', 'my_php_Include');

// カスタムフィールドの検索を追加（wp_postmetaテーブルをJOINする）
function custom_search_join($join){
    if(!empty($_REQUEST['pulldown_Y'])) {
        $join .= "INNER JOIN wp9_postmeta ON (wp9_posts.ID = wp9_postmeta.post_id)";
    }
    return $join;
}
add_filter( 'posts_join', 'custom_search_join' );

// 日付検索
function custom_search($search, $wp_query) {
        //検索テキストが空でも検索ページを表示
    if ( isset($wp_query->query['s']) ) $wp_query->is_search = true;
    //サーチページ以外だったら終了
    if (!$wp_query->is_search) return;
    //カスタム投稿＝eventのみを検索対象とする
    $search .= " AND post_type = 'event'";

    //カスタムフィールドで検索
    if (!empty($_REQUEST['pulldown_Y'])) {
//        $meta_text = "'%" .$_REQUEST['meta_text']. "%'";
      if (empty($_REQUEST['pulldown_M'])) { //月が選択されていない場合は年だけで検索する
        $selectdate = $_REQUEST['pulldown_Y'];
      }else{
        $selectdate = $_REQUEST['pulldown_Y']."-".$_REQUEST['pulldown_M']."-".$_REQUEST['pulldown_D'];
      }
        $meta_text = "'%" .$selectdate. "%'";

        $search .= "
            AND wp9_postmeta.meta_key = '_eventorganiser_schedule_start_start'
            AND wp9_postmeta.meta_value LIKE {$meta_text}";
     }

    return $search;
}
add_filter('posts_search','custom_search', 10, 2);

// カレンダーの日付表示を訂正
function my_eventorganiser_format_datetime( $formatted_datetime , $format, $datetime ) {
    if ( $format === 'F Y' ) {
        return eo_format_datetime( $datetime, 'Y年 F' );
    } else {
        return $formatted_datetime;
    }
}
add_filter( 'eventorganiser_format_datetime', 'my_eventorganiser_format_datetime', 10, 3 );

/* スマホ：トップページの最大投稿数設定 */
function change_limit_mobile($query){

    $new_limit = 100;

    $iphone = strpos($_SERVER['HTTP_USER_AGENT'],"iPhone");
    $android = strpos($_SERVER['HTTP_USER_AGENT'],"Android");
    $ipad = strpos($_SERVER['HTTP_USER_AGENT'],"iPad");
    $berry = strpos($_SERVER['HTTP_USER_AGENT'],"BlackBerry");
    $ipod = strpos($_SERVER['HTTP_USER_AGENT'],"iPod");

    if (( $iphone || $android || $ipad || $ipod || $berry ) && $query->is_main_query()){
        set_query_var('posts_per_page',$new_limit);
    }
}
add_action('pre_get_posts','change_limit_mobile');

/* 日本語URLをpage-IDへ変換 */
function auto_post_slug( $slug, $post_ID, $post_status, $post_type ) {
    if ( preg_match( '/(%[0-9a-f]{2})+/', $slug ) ) {
        $slug = $post_ID;
    }
    return $slug;
}
add_filter( 'wp_unique_post_slug', 'auto_post_slug', 10, 4  );

// 管理バーのヘルプメニューを非表示にする
function my_admin_head(){
 echo '<style type="text/css">#contextual-help-link-wrap{display:none;}</style>';
 }
add_action('admin_head', 'my_admin_head');

// メニューを非表示にする
function remove_menus () {
 if (!current_user_can('level_1')) { //level10以下のユーザーの場合メニューをunsetする
 remove_menu_page('wpcf7'); //Contact Form 7
 global $menu;
 //unset($menu[2]); // ダッシュボード
 unset($menu[4]); // メニューの線1
 unset($menu[5]); // 投稿
 unset($menu[10]); // メディア
 unset($menu[15]); // リンク
 //unset($menu[20]); // ページ
 unset($menu[25]); // コメント
 unset($menu[59]); // メニューの線2
 unset($menu[60]); // テーマ
 unset($menu[65]); // プラグイン
 unset($menu[70]); // プロフィール
 unset($menu[75]); // ツール
 unset($menu[80]); // 設定
 unset($menu[90]); // メニューの線3
 }
 }
add_action('admin_menu', 'remove_menus');

// 管理バーの項目を非表示
function remove_admin_bar_menu( $wp_admin_bar ) {
 $wp_admin_bar->remove_menu( 'wp-logo' ); // WordPressシンボルマーク
 $wp_admin_bar->remove_menu('my-account'); // マイアカウント
   
 }
add_action( 'admin_bar_menu', 'remove_admin_bar_menu', 70 );

function add_meta_query_vars( $public_query_vars ) {
    $public_query_vars[] = 'meta_key'; //カスタムフィールドのキー
    $public_query_vars[] = 'meta_value'; //カスタムフィールドの値（文字列）
    return $public_query_vars;
}
add_filter( 'query_vars', 'add_meta_query_vars' );

add_filter('swpm_email_registration_complete_body', 'convert_nl');
function convert_nl($message) {
	return nl2br($message);
}

