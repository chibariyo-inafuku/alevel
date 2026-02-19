<?php


/* ================================
 * アイキャッチ設定
 * ================================ */
add_action('after_setup_theme', function () {
  add_theme_support('post-thumbnails');
});
/* ================================
 * カスタム投稿設定
 * ================================ */
function register_custom_post_types()
{
    // p_companies Post Type
    register_post_type('companies', array(
      'labels' => array(
        'name' => '出展企業',
        'singular_name' => '出展企業',
        'menu_name' => '出展企業',
        'name_admin_bar' => '出展企業',
      ),
      'public' => true,
    
      //  一覧URLを /company/ にする
      'has_archive' => 'company',
    
      //  詳細URLを /company/スラッグ/ にする
      'rewrite' => array(
        'slug'       => 'company',
        'with_front' => false,
      ),
    
      'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
      'show_in_rest' => true,
      'capability_type' => 'post',
    ));
    

    // 無料企業
    register_post_type('f_companies', array(
        'labels' => array(
            'name' => '掲載企業',
            'singular_name' => '掲載企業',
            'menu_name' => '掲載企業',
            'name_admin_bar' => '掲載企業',
            //'add_new' => 'Add New',
            //'add_new_item' => 'Add New Blog',
            //'new_item' => 'New Blog',
            //'edit_item' => 'Edit Blog',
            //'view_item' => 'View Blog',
            //'all_items' => 'All Blogs',
            //'search_items' => 'Search Blogs',
            //'not_found' => 'No Blogs found.',
            //'not_found_in_trash' => 'No Blogs found in Trash.',
        ),
        'public' => true,
        //'menu_position' => 6,
        //menu_icon' => 'dashicons-edit-large',
        'has_archive' => true,
        'rewrite' => array('slug' => 'f_companies'),
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        //'taxonomies' => array('post_tag'),
        'show_in_rest' => true,
        'capability_type' => 'post',
    ));
	
		// エラベルTV
    register_post_type('special-stage', array(
        'labels' => array(
            'name' => 'エラベルTV',
            'singular_name' => 'エラベルTV',
            'menu_name' => 'エラベルTV',
            'name_admin_bar' => 'エラベルTV',
            //'add_new' => 'Add New',
            //'add_new_item' => 'Add New Blog',
            //'new_item' => 'New Blog',
            //'edit_item' => 'Edit Blog',
            //'view_item' => 'View Blog',
            //'all_items' => 'All Blogs',
            //'search_items' => 'Search Blogs',
            //'not_found' => 'No Blogs found.',
            //'not_found_in_trash' => 'No Blogs found in Trash.',
        ),
        'public' => true,
        //'menu_position' => 6,
        //menu_icon' => 'dashicons-edit-large',
        'has_archive' => true,
        'rewrite' => array('slug' => 'special-stage'),
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        //'taxonomies' => array('post_tag'),
        'show_in_rest' => true,
        'capability_type' => 'post',
    ));
}
add_action('init', 'register_custom_post_types');
/*-----------------------------------
 カスタムタクソノミー「を登録
-----------------------------------*/
function custom_taxonomy_creation() {
    register_taxonomy(
        'company_pref',  // タクソノミーの名前
        ['companies', 'f_companies'],     // このタクソノミーを紐づける投稿タイプ
        array(
            'labels' => array(
                'name' => '都道府県',
                'singular_name' => '都道府県',
            ),
            'show_ui' => true,
            'show_in_rest' => true,
            'hierarchical' => true, // カテゴリー型
        )
    );
    register_taxonomy(
        'company_jobtype',  // タクソノミーの名前
        ['companies', 'f_companies'],     // このタクソノミーを紐づける投稿タイプ
        array(
            'labels' => array(
                'name' => '職種',
                'singular_name' => '職種',
            ),
            'show_ui' => true,
            'show_in_rest' => true,
            'hierarchical' => true, // カテゴリー型
        )
    );

}
add_action('init', 'custom_taxonomy_creation');

	/* ================================
* オプションページ	* CSVインポート
* ================================ */
require_once get_template_directory() . '/inc/f-companies-csv-import.php';
require_once get_template_directory() . '/inc/company-jobtype-csv-import.php';

/* ================================
 * オプションページ
 * ================================ */
if ( function_exists('acf_add_options_page') ) {

    // メインのオプションページ
    acf_add_options_page(array(
        'page_title'  => 'TOPページ設定',
        'menu_title'  => 'TOPページ通設定',
        'menu_slug'   => 'site-common-settings',
        'capability'  => 'edit_posts',
        'redirect'    => false
    ));

}



/**
 * ACFの text / textarea をキーワード検索(s=)の対象に含める（companies / f_companies）
 * - group / repeater / flexible 内の sub_field も対象（meta_key の LIKE を使う）
 * - WP_Query に 'my_acf_search_on' => 1 が付いているときだけ発動（＝任意のクエリで使える）
 */

if ( ! function_exists('my_acf_collect_text_textarea_meta_rules') ) {
  function my_acf_collect_text_textarea_meta_rules( $post_type ) {
    if ( ! function_exists('acf_get_field_groups') || ! function_exists('acf_get_fields') ) {
      return ['exact' => [], 'like' => []];
    }

    $cache_key = 'my_acf_search_rules_' . $post_type;
    $cached = get_transient($cache_key);
    if ( is_array($cached) && isset($cached['exact'], $cached['like']) ) {
      return $cached;
    }

    $target_types = ['text', 'textarea'];

    $rules = [
      'exact' => [], // meta_key = xxxx
      'like'  => [], // meta_key LIKE xxxx_%_yyyy
    ];

    // post_type に紐づくフィールドグループ
    $groups = acf_get_field_groups([
      'post_type' => $post_type,
    ]);

    $join_key = function($prefix, $name) {
      $prefix = (string) $prefix;
      $name   = (string) $name;
      if ($prefix === '') return $name;
      if ($name === '') return $prefix;
      return $prefix . '_' . $name;
    };

    $walk = function($fields, $prefix = '', $in_repeater = false) use (&$walk, &$rules, $target_types, $join_key) {
      if ( empty($fields) || !is_array($fields) ) return;

      foreach ($fields as $f) {
        $type = $f['type'] ?? '';
        $name = $f['name'] ?? '';
        if ($type === '' || $name === '') {
          // name が空のものは meta_key 化できないのでスキップ
          // (tab 等)
        }

        // コンテナ系
        if ( $type === 'group' ) {
          $next_prefix = $join_key($prefix, $name);
          if ( !empty($f['sub_fields']) && is_array($f['sub_fields']) ) {
            $walk($f['sub_fields'], $next_prefix, $in_repeater);
          }
          continue;
        }

        if ( $type === 'repeater' || $type === 'flexible_content' ) {
          $next_prefix = $join_key($prefix, $name);
          // repeater/flexible の中は row index が入るので LIKE になる
          $next_in_repeater = true;

          if ( !empty($f['sub_fields']) && is_array($f['sub_fields']) ) {
            $walk($f['sub_fields'], $next_prefix, $next_in_repeater);
          }

          // flexible layouts
          if ( !empty($f['layouts']) && is_array($f['layouts']) ) {
            foreach ($f['layouts'] as $layout) {
              if ( !empty($layout['sub_fields']) && is_array($layout['sub_fields']) ) {
                $walk($layout['sub_fields'], $next_prefix, $next_in_repeater);
              }
            }
          }
          continue;
        }

        // 通常フィールド（leaf）
        if ( in_array($type, $target_types, true) && $name !== '' ) {
          if ( $in_repeater ) {
            // prefix_%_name を LIKE で拾う（% は row index 用）
            // 例: company_biz-group_%_company_biz-title
            $rules['like'][] = $prefix . '_%_' . $name;
          } else {
            // group の prefix がある場合は prefix_name
            $rules['exact'][] = $join_key($prefix, $name);
          }
        }

        // 万一、leaf の中に sub_fields がぶら下がるケースも拾う
        if ( !empty($f['sub_fields']) && is_array($f['sub_fields']) ) {
          $next_prefix = $join_key($prefix, $name);
          $walk($f['sub_fields'], $next_prefix, $in_repeater);
        }
      }
    };

    foreach ($groups as $g) {
      $fields = acf_get_fields($g);
      $walk($fields, '', false);
    }

    $rules['exact'] = array_values(array_unique(array_filter($rules['exact'])));
    $rules['like']  = array_values(array_unique(array_filter($rules['like'])));

    set_transient($cache_key, $rules, 12 * HOUR_IN_SECONDS);

    return $rules;
  }
}

/**
 * ACFフィールドグループを保存したらキャッシュ削除
 * （companies / f_companies 両方）
 */
add_action('acf/save_post', function () {
  delete_transient('my_acf_search_rules_companies');
  delete_transient('my_acf_search_rules_f_companies');
}, 20);


/**
 * JOIN: postmeta を結合（検索拡張フラグがある時だけ）

 */
add_filter('posts_join', function($join, $q){
  if ( is_admin() ) return $join;
  if ( ! $q->get('my_acf_search_on') ) return $join;

  global $wpdb;

  if ( strpos($join, 'acfmeta') !== false ) return $join;

  $join .= " LEFT JOIN {$wpdb->postmeta} AS acfmeta ON ({$wpdb->posts}.ID = acfmeta.post_id) ";
  return $join;
}, 20, 2);


/**
 * SEARCH: タイトル/本文の通常検索 OR ACF(text/textarea)検索
 * ※ posts_search を使うと壊れにくいです
 */
add_filter('posts_search', function($search, $q){
  if ( is_admin() ) return $search;
  if ( ! $q->get('my_acf_search_on') ) return $search;

  global $wpdb;

  $s = (string) $q->get('s');
  if ( $s === '' ) return $search; // キーワードなし

  // post_type 判定（クエリ側で 1つに固定して使うのが安全）
  $pt = $q->get('post_type');
  if ( is_array($pt) ) $pt = reset($pt);
  if ( !is_string($pt) || $pt === '' ) $pt = 'companies';

  $rules = my_acf_collect_text_textarea_meta_rules($pt);
  $exact = $rules['exact'];
  $likeK = $rules['like'];

  if ( empty($exact) && empty($likeK) ) return $search;

  $likeVal = '%' . $wpdb->esc_like($s) . '%';

  $orParts = [];

  // exact: meta_key IN (...)
  if ( !empty($exact) ) {
    $ph = implode(',', array_fill(0, count($exact), '%s'));
    $sql = $wpdb->prepare(
      "(acfmeta.meta_key IN ($ph) AND acfmeta.meta_value LIKE %s)",
      array_merge($exact, [$likeVal])
    );
    $orParts[] = $sql;
  }

  // like: meta_key LIKE 'prefix\_%\_name'
  if ( !empty($likeK) ) {
    foreach ($likeK as $pattern) {
      // esc_like は % と _ をエスケープしてくれるので、
      // ここで「row index 用の %」だけ戻して使う（安全に）
      $escaped = $wpdb->esc_like($pattern);

      // pattern の中の "_%_" は、esc_like で "\_\%\_" になる
      // これを「\_%\_」(row index の % はワイルドカード) に戻す
      $escaped = str_replace('\\_\\%\\_', '\\_%\\_', $escaped);

      $orParts[] = $wpdb->prepare(
        "(acfmeta.meta_key LIKE %s ESCAPE '\\\\' AND acfmeta.meta_value LIKE %s)",
        $escaped,
        $likeVal
      );
    }
  }

  if ( empty($orParts) ) return $search;

  // $search は " AND ( ... )" の形なので、先頭の AND を外して括る
  $trim = preg_replace('/^\s*AND\s*/i', '', $search);

  $acfClause = implode(' OR ', $orParts);

  // 通常検索 OR ACF検索
  $search = " AND ( {$trim} OR ( {$acfClause} ) ) ";

  return $search;
}, 20, 2);


/**
 * 重複防止（JOINで増えるため）
 */
add_filter('posts_distinct', function($distinct, $q){
  if ( is_admin() ) return $distinct;
  if ( ! $q->get('my_acf_search_on') ) return $distinct;
  return 'DISTINCT';
}, 20, 2);
