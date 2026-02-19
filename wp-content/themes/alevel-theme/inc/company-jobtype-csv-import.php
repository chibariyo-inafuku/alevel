<?php
/**
 * 職種タクソノミー（company_jobtype）CSVインポート機能
 *
 * カスタム投稿とは別に、職種タームのみを一括登録する。
 *
 * CSVフォーマット:
 *   name, slug, description, parent
 * - name: 職種名（必須）
 * - slug: スラッグ（任意。空なら name から自動生成）
 * - description: 説明（任意）
 * - parent: 親タームの slug または name（階層用。任意）
 */

if ( ! defined('ABSPATH') ) {
  exit;
}

define('COMPANY_JOBTYPE_TAXONOMY', 'company_jobtype');

/**
 * 管理メニュー追加
 */
add_action('admin_menu', function () {
  add_submenu_page(
    'edit.php?post_type=f_companies',
    '職種CSVインポート',
    '職種CSVインポート',
    'manage_options',
    'company-jobtype-csv-import',
    'company_jobtype_csv_import_render_page'
  );
});

/**
 * サンプルCSVダウンロード
 */
add_action('admin_init', function () {
  if ( ! isset($_GET['page']) || $_GET['page'] !== 'company-jobtype-csv-import' ) {
    return;
  }
  if ( ! isset($_GET['action']) || $_GET['action'] !== 'download_sample' ) {
    return;
  }
  if ( ! current_user_can('manage_options') ) {
    wp_die(__('権限がありません。'));
  }
  if ( ! wp_verify_nonce($_GET['_wpnonce'] ?? '', 'company_jobtype_csv_sample') ) {
    wp_die(__('不正なリクエストです。'));
  }

  $csv = "name,slug,description,parent\n";
  $csv .= "\"IT・情報通信\",it,\"情報技術関連\",\n";
  $csv .= "\"製造業\",manufacturing,\"ものづくり\",\n";
  $csv .= "\"小売・卸売\",retail,\"販売業\",\n";

  header('Content-Type: text/csv; charset=UTF-8');
  header('Content-Disposition: attachment; filename="company_jobtype_sample.csv"');
  echo "\xEF\xBB\xBF"; // UTF-8 BOM
  echo $csv;
  exit;
});

/**
 * 管理画面ページ表示
 */
function company_jobtype_csv_import_render_page() {
  if ( ! current_user_can('manage_options') ) {
    wp_die(__('権限がありません。'));
  }

  $sample_url = wp_nonce_url(
    add_query_arg(['page' => 'company-jobtype-csv-import', 'action' => 'download_sample'], admin_url('edit.php?post_type=f_companies')),
    'company_jobtype_csv_sample'
  );

  $result = null;
  if ( isset($_POST['company_jobtype_csv_import']) && wp_verify_nonce($_POST['_wpnonce'], 'company_jobtype_csv_import') ) {
    $result = company_jobtype_csv_import_process();
  }
  ?>
  <div class="wrap">
    <h1>職種 タクソノミー CSVインポート</h1>

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
      <p>以下のカラムを含むUTF-8のCSVファイルをアップロードしてください。掲載企業のCSVインポートより<strong>先に</strong>職種を登録しておくと便利です。</p>
      <table class="widefat striped">
        <thead>
          <tr>
            <th>カラム</th>
            <th>説明</th>
          </tr>
        </thead>
        <tbody>
          <tr><td>name</td><td>職種名（必須）</td></tr>
          <tr><td>slug</td><td>スラッグ（任意。空ならnameから自動生成）</td></tr>
          <tr><td>description</td><td>説明（任意）</td></tr>
          <tr><td>parent</td><td>親タームのslugまたはname（階層用。任意）</td></tr>
        </tbody>
      </table>
      <p>
        <a href="<?php echo esc_url($sample_url); ?>" class="button">サンプルCSVをダウンロード</a>
      </p>
    </div>

    <div class="card" style="max-width: 600px; margin-top: 20px;">
      <h2>CSVをアップロード</h2>
      <form method="post" enctype="multipart/form-data">
        <?php wp_nonce_field('company_jobtype_csv_import'); ?>
        <p>
          <input type="file" name="csv_file" accept=".csv" required>
        </p>
        <p>
          <button type="submit" name="company_jobtype_csv_import" class="button button-primary">インポート実行</button>
        </p>
      </form>
    </div>
  </div>
  <?php
}

/**
 * CSVインポート処理
 */
function company_jobtype_csv_import_process() {
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
  $col_name = array_search('name', $header, true);
  $col_slug = array_search('slug', $header, true);
  $col_desc = array_search('description', $header, true);
  $col_parent = array_search('parent', $header, true);

  if ( $col_name === false ) {
    fclose($handle);
    return ['success' => 0, 'errors' => 1, 'messages' => ['name カラムが見つかりません。']];
  }

  $success = 0;
  $errors = 0;
  $messages = [];
  $line_no = 1;

  while ( ($row = fgetcsv($handle)) !== false ) {
    $line_no++;
    $name = isset($row[$col_name]) ? trim((string) $row[$col_name]) : '';
    if ( $name === '' ) {
      $errors++;
      $messages[] = sprintf('%d行目: name が空のためスキップ', $line_no);
      continue;
    }

    $slug = ($col_slug !== false && isset($row[$col_slug]) && trim($row[$col_slug]) !== '')
      ? sanitize_title(trim($row[$col_slug]))
      : sanitize_title($name);

    $description = ($col_desc !== false && isset($row[$col_desc])) ? trim((string) $row[$col_desc]) : '';

    $parent_id = 0;
    if ( $col_parent !== false && isset($row[$col_parent]) && trim($row[$col_parent]) !== '' ) {
      $parent_val = trim($row[$col_parent]);
      $parent_term = company_jobtype_resolve_term(COMPANY_JOBTYPE_TAXONOMY, $parent_val);
      if ( $parent_term ) {
        $parent_id = (int) $parent_term['term_id'];
      } else {
        $messages[] = sprintf('%d行目 (%s): 親ターム "%s" が見つかりません（先に親を登録してください）', $line_no, $name, $parent_val);
      }
    }

    $existing = term_exists($slug, COMPANY_JOBTYPE_TAXONOMY);
    if ( ! $existing ) {
      $existing = term_exists($name, COMPANY_JOBTYPE_TAXONOMY);
    }

    if ( $existing ) {
      $term = wp_update_term((int) $existing['term_id'], COMPANY_JOBTYPE_TAXONOMY, [
        'name'        => $name,
        'slug'        => $slug,
        'description' => $description,
        'parent'      => $parent_id,
      ]);
    } else {
      $term = wp_insert_term($name, COMPANY_JOBTYPE_TAXONOMY, [
        'slug'        => $slug,
        'description' => $description,
        'parent'      => $parent_id,
      ]);
    }

    if ( is_wp_error($term) ) {
      $errors++;
      $messages[] = sprintf('%d行目 (%s): 登録失敗 - %s', $line_no, $name, $term->get_error_message());
      continue;
    }

    $success++;
  }

  fclose($handle);

  return [
    'success'  => $success,
    'errors'   => $errors,
    'messages' => array_slice($messages, -20),
  ];
}

/**
 * 名前またはスラッグでタームを解決（term_exists は slug→name の順で検索）
 */
function company_jobtype_resolve_term($taxonomy, $value) {
  $value = trim((string) $value);
  if ( $value === '' ) return null;

  $term = term_exists($value, $taxonomy);
  return is_array($term) ? $term : null;
}
