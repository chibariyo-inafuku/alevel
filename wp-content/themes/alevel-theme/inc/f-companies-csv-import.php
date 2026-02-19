<?php
/**
 * f_companies（掲載企業）CSVインポート機能
 *
 * CSVフォーマット:
 *   title, company_pref, company_jobtype, thumbnail
 * - title: 企業名（必須）
 * - company_pref: 都道府県slug（複数はカンマ区切り）
 * - company_jobtype: 職種slug（複数はカンマ区切り）
 * - thumbnail: メディアライブラリの画像ファイル名（事前アップロード済み）
 */

if ( ! defined('ABSPATH') ) {
  exit;
}

/**
 * 管理メニュー追加
 */
add_action('admin_menu', function () {
  add_submenu_page(
    'edit.php?post_type=f_companies',
    'CSVインポート',
    'CSVインポート',
    'manage_options',
    'f-companies-csv-import',
    'f_companies_csv_import_render_page'
  );
});

/**
 * サンプルCSVダウンロード
 */
add_action('admin_init', function () {
  if ( ! isset($_GET['page']) || $_GET['page'] !== 'f-companies-csv-import' ) {
    return;
  }
  if ( ! isset($_GET['action']) || $_GET['action'] !== 'download_sample' ) {
    return;
  }
  if ( ! current_user_can('manage_options') ) {
    wp_die(__('権限がありません。'));
  }
  if ( ! wp_verify_nonce($_GET['_wpnonce'] ?? '', 'f_companies_csv_sample') ) {
    wp_die(__('不正なリクエストです。'));
  }

  $csv = "title,company_pref,company_jobtype,thumbnail\n";
  $csv .= "\"株式会社サンプル\",aichi,\"it,manufacturing\",company-a.png\n";

  header('Content-Type: text/csv; charset=UTF-8');
  header('Content-Disposition: attachment; filename="f_companies_sample.csv"');
  echo "\xEF\xBB\xBF"; // UTF-8 BOM
  echo $csv;
  exit;
});

/**
 * 管理画面ページ表示
 */
function f_companies_csv_import_render_page() {
  if ( ! current_user_can('manage_options') ) {
    wp_die(__('権限がありません。'));
  }

  $sample_url = wp_nonce_url(
    add_query_arg(['page' => 'f-companies-csv-import', 'action' => 'download_sample'], admin_url('edit.php?post_type=f_companies')),
    'f_companies_csv_sample'
  );

  $result = null;
  if ( isset($_POST['f_companies_csv_import']) && wp_verify_nonce($_POST['_wpnonce'], 'f_companies_csv_import') ) {
    $result = f_companies_csv_import_process();
  }
  ?>
  <div class="wrap">
    <h1>掲載企業 CSVインポート</h1>

    <?php if ( $result ) : ?>
      <div class="notice notice-<?php echo $result['errors'] > 0 ? 'warning' : 'success'; ?> is-dismissible">
        <p>
          成功: <?php echo (int) $result['success']; ?> 件
          <?php if ( $result['errors'] > 0 ) : ?>
            、失敗: <?php echo (int) $result['errors']; ?> 件
          <?php endif; ?>
        </p>
        <?php if ( ! empty($result['messages']) ) : ?>
          <ul style="margin: 0.5em 0 0 1em;">
            <?php foreach ( $result['messages'] as $msg ) : ?>
              <li><?php echo esc_html($msg); ?></li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <div class="card" style="max-width: 600px; margin-top: 20px;">
      <h2>CSVフォーマット</h2>
      <p>以下のカラムを含むUTF-8のCSVファイルをアップロードしてください。</p>
      <table class="widefat striped">
        <thead>
          <tr>
            <th>カラム</th>
            <th>説明</th>
          </tr>
        </thead>
        <tbody>
          <tr><td>title</td><td>企業名（必須）</td></tr>
          <tr><td>company_pref</td><td>都道府県（slug。複数はカンマ区切り）</td></tr>
          <tr><td>company_jobtype</td><td>職種（slug。複数はカンマ区切り）</td></tr>
          <tr><td>thumbnail</td><td>アイキャッチ画像のファイル名（メディアライブラリに事前アップロード済み）</td></tr>
        </tbody>
      </table>
      <p>
        <a href="<?php echo esc_url($sample_url); ?>" class="button">サンプルCSVをダウンロード</a>
      </p>
    </div>

    <div class="card" style="max-width: 600px; margin-top: 20px;">
      <h2>CSVをアップロード</h2>
      <form method="post" enctype="multipart/form-data">
        <?php wp_nonce_field('f_companies_csv_import'); ?>
        <p>
          <input type="file" name="csv_file" accept=".csv" required>
        </p>
        <p>
          <button type="submit" name="f_companies_csv_import" class="button button-primary">インポート実行</button>
        </p>
      </form>
    </div>
  </div>
  <?php
}

/**
 * CSVインポート処理
 */
function f_companies_csv_import_process() {
  if ( ! current_user_can('manage_options') ) {
    return ['success' => 0, 'errors' => 1, 'messages' => ['権限がありません。']];
  }

  if ( empty($_FILES['csv_file']['tmp_name']) || ! is_uploaded_file($_FILES['csv_file']['tmp_name']) ) {
    return ['success' => 0, 'errors' => 1, 'messages' => ['CSVファイルを選択してください。']];
  }

  $file = $_FILES['csv_file']['tmp_name'];
  $handle = fopen($file, 'r');
  if ( ! $handle ) {
    return ['success' => 0, 'errors' => 1, 'messages' => ['ファイルを開けませんでした。']];
  }

  // UTF-8 BOM をスキップ
  $bom = fread($handle, 3);
  if ( $bom !== "\xEF\xBB\xBF" ) {
    rewind($handle);
  }

  // ヘッダー行
  $header = fgetcsv($handle);
  if ( ! $header ) {
    fclose($handle);
    return ['success' => 0, 'errors' => 1, 'messages' => ['CSVが空です。']];
  }

  $header = array_map('trim', array_map('strtolower', $header));
  $col_title = array_search('title', $header, true);
  $col_pref = array_search('company_pref', $header, true);
  $col_job = array_search('company_jobtype', $header, true);
  $col_thumb = array_search('thumbnail', $header, true);

  if ( $col_title === false ) {
    fclose($handle);
    return ['success' => 0, 'errors' => 1, 'messages' => ['title カラムが見つかりません。']];
  }

  $success = 0;
  $errors = 0;
  $messages = [];
  $line_no = 1;

  while ( ($row = fgetcsv($handle)) !== false ) {
    $line_no++;
    $title = isset($row[$col_title]) ? trim((string) $row[$col_title]) : '';
    if ( $title === '' ) {
      $errors++;
      $messages[] = sprintf('%d行目: タイトルが空のためスキップ', $line_no);
      continue;
    }

    $pref_slugs = [];
    if ( $col_pref !== false && isset($row[$col_pref]) && trim($row[$col_pref]) !== '' ) {
      $pref_slugs = array_map('trim', array_filter(explode(',', $row[$col_pref])));
    }

    $job_slugs = [];
    if ( $col_job !== false && isset($row[$col_job]) && trim($row[$col_job]) !== '' ) {
      $job_slugs = array_map('trim', array_filter(explode(',', $row[$col_job])));
    }

    $thumbnail_filename = ($col_thumb !== false && isset($row[$col_thumb])) ? trim((string) $row[$col_thumb]) : '';

    $post_id = wp_insert_post([
      'post_title'   => $title,
      'post_type'    => 'f_companies',
      'post_status'  => 'publish',
      'post_content' => '',
    ], true);

    if ( is_wp_error($post_id) ) {
      $errors++;
      $messages[] = sprintf('%d行目 (%s): 投稿作成失敗 - %s', $line_no, $title, $post_id->get_error_message());
      continue;
    }

    // タクソノミー: company_pref
    if ( ! empty($pref_slugs) ) {
      $pref_ids = f_companies_csv_ensure_terms('company_pref', $pref_slugs);
      if ( ! empty($pref_ids) ) {
        wp_set_object_terms($post_id, $pref_ids, 'company_pref');
      }
    }

    // タクソノミー: company_jobtype
    if ( ! empty($job_slugs) ) {
      $job_ids = f_companies_csv_ensure_terms('company_jobtype', $job_slugs);
      if ( ! empty($job_ids) ) {
        wp_set_object_terms($post_id, $job_ids, 'company_jobtype');
      }
    }

    // アイキャッチ画像（ファイル名で検索）
    if ( $thumbnail_filename !== '' ) {
      $attachment_id = f_companies_csv_get_attachment_id_by_filename($thumbnail_filename);
      if ( $attachment_id ) {
        set_post_thumbnail($post_id, $attachment_id);
      } else {
        $messages[] = sprintf('%d行目 (%s): 画像 "%s" がメディアライブラリに見つかりません', $line_no, $title, $thumbnail_filename);
      }
    }

    $success++;
  }

  fclose($handle);

  return [
    'success'  => $success,
    'errors'   => $errors,
    'messages' => array_slice($messages, -20), // 直近20件のみ表示
  ];
}

/**
 * タームが存在しなければ作成し、term_id の配列を返す
 */
function f_companies_csv_ensure_terms($taxonomy, array $slugs) {
  $ids = [];
  foreach ( $slugs as $slug ) {
    if ( $slug === '' ) continue;
    $term = term_exists($slug, $taxonomy);
    if ( ! $term ) {
      $term = wp_insert_term($slug, $taxonomy, ['slug' => $slug]);
      if ( is_wp_error($term) ) continue;
    }
    $ids[] = (int) $term['term_id'];
  }
  return $ids;
}

/**
 * ファイル名でメディアライブラリから attachment ID を取得
 * 複数ヒット時は最も新しいものを返す
 */
function f_companies_csv_get_attachment_id_by_filename($filename) {
  global $wpdb;

  $filename = trim((string) $filename);
  if ( $filename === '' ) return 0;

  $escaped = $wpdb->esc_like($filename);
  $pattern = '%/' . $escaped;

  $sql = $wpdb->prepare(
    "SELECT p.ID FROM {$wpdb->posts} p
     INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_wp_attached_file'
     WHERE p.post_type = 'attachment'
       AND (pm.meta_value = %s OR pm.meta_value LIKE %s)
     ORDER BY p.post_date DESC
     LIMIT 1",
    $filename,
    $pattern
  );

  $id = (int) $wpdb->get_var($sql);
  if ( $id > 0 ) return $id;

  // ファイル名のみで検索（パスを含まない場合: 2025/02/company-a.png のような値にマッチ）
  $sql2 = $wpdb->prepare(
    "SELECT p.ID FROM {$wpdb->posts} p
     INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_wp_attached_file'
     WHERE p.post_type = 'attachment'
       AND pm.meta_value LIKE %s
     ORDER BY p.post_date DESC
     LIMIT 1",
    '%' . $escaped
  );

  return (int) $wpdb->get_var($sql2);
}
