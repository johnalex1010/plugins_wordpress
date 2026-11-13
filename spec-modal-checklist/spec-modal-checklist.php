<?php

/**
 * Plugin Name: SPEC Modal Pro
 * Plugin URI: https://virtual.uniminuto.edu/
 * Description: Gestiona modales promocionales por página, con imagen clickeable, video MP4/WebM o YouTube con botón, estado activo, frecuencia configurable y columnas administrativas de estado/asignación.
 * Version: 3.7
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: Ing John Fandiño - Webmaster
 * Author URI: https://virtual.uniminuto.edu/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: spec-modal-pro
 * Domain Path: /languages
 */

defined('ABSPATH') || exit;

add_action('plugins_loaded', function () {
  load_plugin_textdomain('spec-modal-pro', false, dirname(plugin_basename(__FILE__)) . '/languages');

  $locale = determine_locale();
  $php_translation_file = plugin_dir_path(__FILE__) . 'languages/spec-modal-pro-' . $locale . '.l10n.php';

  if (is_readable($php_translation_file)) {
    load_textdomain('spec-modal-pro', $php_translation_file);
  }
});

function smp_translate($text)
{
  $locale = function_exists('determine_locale') ? determine_locale() : get_locale();

  if (strpos($locale, 'en') !== 0) {
    return __($text, 'spec-modal-pro');
  }

  $translations = [
    'Modales' => 'Modals',
    'Estado' => 'Status',
    'Activo' => 'Active',
    'Páginas del modal' => 'Modal pages',
    'PÃ¡ginas del modal' => 'Modal pages',
    'Publicado' => 'Published',
    'No publicado' => 'Unpublished',
    'Borrador' => 'Draft',
    'Programado' => 'Scheduled',
    'Sí' => 'Yes',
    'SÃ­' => 'Yes',
    'No' => 'No',
    'Sin páginas asignadas' => 'No assigned pages',
    'Sin pÃ¡ginas asignadas' => 'No assigned pages',
    'Página #%d' => 'Page #%d',
    'PÃ¡gina #%d' => 'Page #%d',
    'Seleccionar Imagen' => 'Select Image',
    'Usar Imagen' => 'Use Image',
    'Configuración del Modal' => 'Modal Settings',
    'ConfiguraciÃ³n del Modal' => 'Modal Settings',
    'Modal activo' => 'Active modal',
    'Delay (ms)' => 'Delay (ms)',
    'Frecuencia' => 'Frequency',
    'Una vez por sesión' => 'Once per session',
    'Una vez por sesiÃ³n' => 'Once per session',
    'Persistente (1 hora)' => 'Persistent (1 hour)',
    'Páginas' => 'Pages',
    'PÃ¡ginas' => 'Pages',
    'Buscar páginas' => 'Search pages',
    'Buscar pÃ¡ginas' => 'Search pages',
    'Buscar páginas...' => 'Search pages...',
    'Buscar pÃ¡ginas...' => 'Search pages...',
    'Páginas disponibles' => 'Available pages',
    'PÃ¡ginas disponibles' => 'Available pages',
    'No se encontraron páginas con ese criterio.' => 'No pages were found with that criterion.',
    'No se encontraron pÃ¡ginas con ese criterio.' => 'No pages were found with that criterion.',
    'Páginas donde está activo un modal' => 'Pages where a modal is active',
    'PÃ¡ginas donde estÃ¡ activo un modal' => 'Pages where a modal is active',
    'Programación' => 'Schedule',
    'Programación de publicación' => 'Publication schedule',
    'Deja las fechas vacías para mantener el modal siempre visible mientras esté publicado y activo.' => 'Leave the dates empty to keep the modal always visible while it is published and active.',
    'Fecha de inicio' => 'Start date',
    'Fecha de fin' => 'End date',
    'Sin programación' => 'No schedule',
    'Sin inicio' => 'No start date',
    'Sin fin' => 'No end date',
    'Modal' => 'Modal',
    'No hay modales activos con páginas asignadas.' => 'There are no active modals with assigned pages.',
    'No hay modales activos con pÃ¡ginas asignadas.' => 'There are no active modals with assigned pages.',
    'Modal #%d' => 'Modal #%d',
    'URL del Botón' => 'Button URL',
    'URL del BotÃ³n' => 'Button URL',
    'ID Tag Manager' => 'Tag Manager ID',
    'Target' => 'Target',
    'Misma pestaña' => 'Same tab',
    'Misma pestaÃ±a' => 'Same tab',
    'Nueva pestaña' => 'New tab',
    'Nueva pestaÃ±a' => 'New tab',
    'Imagen del Modal' => 'Modal Image',
    'Cerrar modal' => 'Close modal',
    'Tipo de contenido' => 'Content type',
    'Imagen' => 'Image',
    'Video MP4/WebM' => 'MP4/WebM video',
    'Video de YouTube' => 'YouTube video',
    'Video del Modal' => 'Modal Video',
    'Seleccionar Video' => 'Select Video',
    'Usar Video' => 'Use Video',
    'Formatos permitidos: MP4 y WebM.' => 'Allowed formats: MP4 and WebM.',
    'URL de YouTube' => 'YouTube URL',
    'Acepta enlaces de youtube.com, youtu.be y YouTube Shorts.' => 'Accepts youtube.com, youtu.be and YouTube Shorts links.',
    'Texto del botón' => 'Button text',
    'Más información' => 'Learn more',
    'Con imagen, la URL enlaza la imagen. Con video o YouTube, se muestra un botón debajo del video con esta URL y el ID Tag Manager.' => 'With an image, the URL links the image. With video or YouTube, a button is shown below the video with this URL and the Tag Manager ID.',
  ];

  return isset($translations[$text]) ? $translations[$text] : __($text, 'spec-modal-pro');
}

function smp_esc_html($text)
{
  return esc_html(smp_translate($text));
}

function smp_esc_attr($text)
{
  return esc_attr(smp_translate($text));
}

function smp_get_asset_url($path)
{
  return plugin_dir_url(__FILE__) . ltrim($path, '/');
}

function smp_get_asset_version($path)
{
  $file = plugin_dir_path(__FILE__) . ltrim($path, '/');

  return file_exists($file) ? (string) filemtime($file) : '3.7';
}

function smp_get_allowed_media_types()
{
  return ['image', 'video', 'youtube'];
}

function smp_get_allowed_video_mime_types()
{
  return ['video/mp4', 'video/webm'];
}

function smp_sanitize_media_type($media_type)
{
  $media_type = sanitize_key((string) $media_type);

  // Los modales creados antes de la versión 3.7 no tienen tipo y se tratan como imagen.
  return in_array($media_type, smp_get_allowed_media_types(), true) ? $media_type : 'image';
}

function smp_get_modal_media_type($modal_id)
{
  return smp_sanitize_media_type(get_post_meta($modal_id, '_smp_media_type', true));
}

function smp_sanitize_video_attachment_id($attachment_id)
{
  $attachment_id = absint($attachment_id);

  if (!$attachment_id || get_post_type($attachment_id) !== 'attachment') {
    return 0;
  }

  return in_array(get_post_mime_type($attachment_id), smp_get_allowed_video_mime_types(), true) ? $attachment_id : 0;
}

function smp_get_youtube_video_id($url)
{
  $url = trim((string) $url);

  if ($url === '') {
    return '';
  }

  $host = strtolower((string) wp_parse_url($url, PHP_URL_HOST));
  $host = preg_replace('/^(www\.|m\.)/', '', $host);
  $path = (string) wp_parse_url($url, PHP_URL_PATH);
  $video_id = '';

  if ($host === 'youtu.be') {
    $video_id = trim($path, '/');
  } elseif (in_array($host, ['youtube.com', 'youtube-nocookie.com'], true)) {
    if (preg_match('#^/(?:embed|shorts|live)/([^/]+)#', $path, $path_matches)) {
      $video_id = $path_matches[1];
    } else {
      parse_str((string) wp_parse_url($url, PHP_URL_QUERY), $query_args);
      $video_id = isset($query_args['v']) && is_string($query_args['v']) ? $query_args['v'] : '';
    }
  }

  return preg_match('/^[A-Za-z0-9_-]{11}$/', $video_id) ? $video_id : '';
}

function smp_is_youtube_short_url($url)
{
  $path = (string) wp_parse_url((string) $url, PHP_URL_PATH);

  return smp_get_youtube_video_id($url) !== '' && strpos($path, '/shorts/') === 0;
}

function smp_sanitize_youtube_url($url)
{
  $url = esc_url_raw(trim((string) $url));

  return smp_get_youtube_video_id($url) ? $url : '';
}

function smp_get_page_hierarchy_label($page_id)
{
  $page_id = absint($page_id);
  $page = get_post($page_id);

  if (!$page || $page->post_type !== 'page') {
    return '';
  }

  $ancestor_ids = array_reverse(get_post_ancestors($page_id));
  $hierarchy_ids = array_merge($ancestor_ids, [$page_id]);
  $page_titles = [];

  foreach ($hierarchy_ids as $hierarchy_page_id) {
    $hierarchy_page_id = absint($hierarchy_page_id);
    $hierarchy_page = get_post($hierarchy_page_id);

    if (!$hierarchy_page || $hierarchy_page->post_type !== 'page') {
      continue;
    }

    $page_title = get_the_title($hierarchy_page_id);
    $page_titles[] = $page_title ? $page_title : sprintf(smp_translate('Página #%d'), $hierarchy_page_id);
  }

  return implode(' / ', $page_titles);
}

function smp_sanitize_schedule_date($date)
{
  $date = trim(sanitize_text_field($date));

  if ($date === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    return '';
  }

  [$year, $month, $day] = array_map('absint', explode('-', $date));

  return checkdate($month, $day, $year) ? $date : '';
}

function smp_get_schedule_timestamp($date, $boundary)
{
  $date = smp_sanitize_schedule_date($date);

  if ($date === '') {
    return 0;
  }

  $time = $boundary === 'end' ? '23:59:59' : '00:00:00';

  $timezone = function_exists('wp_timezone') ? wp_timezone() : null;
  $date_time = $timezone
    ? date_create_immutable_from_format('Y-m-d H:i:s', $date . ' ' . $time, $timezone)
    : date_create_immutable_from_format('Y-m-d H:i:s', $date . ' ' . $time);

  return $date_time ? $date_time->getTimestamp() : 0;
}

function smp_get_publication_schedule($modal_id)
{
  return [
    'start_date' => smp_sanitize_schedule_date(get_post_meta($modal_id, '_smp_start_date', true)),
    'end_date' => smp_sanitize_schedule_date(get_post_meta($modal_id, '_smp_end_date', true)),
  ];
}

function smp_is_modal_in_publication_window($modal_id, $timestamp = null)
{
  $timestamp = $timestamp ?: time();
  $schedule = smp_get_publication_schedule($modal_id);
  $start_timestamp = $schedule['start_date'] ? smp_get_schedule_timestamp($schedule['start_date'], 'start') : 0;
  $end_timestamp = $schedule['end_date'] ? smp_get_schedule_timestamp($schedule['end_date'], 'end') : PHP_INT_MAX;

  return $timestamp >= $start_timestamp && $timestamp <= $end_timestamp;
}

function smp_has_publication_schedule($modal_id)
{
  $schedule = smp_get_publication_schedule($modal_id);

  return $schedule['start_date'] !== '' || $schedule['end_date'] !== '';
}

function smp_is_publication_schedule_expired($modal_id, $timestamp = null)
{
  $timestamp = $timestamp ?: time();
  $schedule = smp_get_publication_schedule($modal_id);

  if (!$schedule['end_date']) {
    return false;
  }

  return $timestamp > smp_get_schedule_timestamp($schedule['end_date'], 'end');
}

function smp_get_publication_status_badge($modal_id)
{
  $post_status = get_post_status($modal_id);

  if ($post_status !== 'publish') {
    return [
      'class' => 'smp-status-badge--unpublished',
      'label' => smp_translate('Borrador'),
    ];
  }

  if (!smp_has_publication_schedule($modal_id)) {
    return [
      'class' => 'smp-status-badge--published',
      'label' => smp_translate('Publicado'),
    ];
  }

  if (smp_is_publication_schedule_expired($modal_id)) {
    return [
      'class' => 'smp-status-badge--unpublished',
      'label' => smp_translate('Borrador'),
    ];
  }

  if (!smp_is_modal_in_publication_window($modal_id)) {
    return [
      'class' => 'smp-status-badge--scheduled',
      'label' => smp_translate('Programado'),
    ];
  }

  return [
    'class' => 'smp-status-badge--published',
    'label' => smp_translate('Publicado'),
  ];
}

function smp_schedules_overlap($first_start_date, $first_end_date, $second_start_date, $second_end_date)
{
  $first_start = $first_start_date ? smp_get_schedule_timestamp($first_start_date, 'start') : 0;
  $first_end = $first_end_date ? smp_get_schedule_timestamp($first_end_date, 'end') : PHP_INT_MAX;
  $second_start = $second_start_date ? smp_get_schedule_timestamp($second_start_date, 'start') : 0;
  $second_end = $second_end_date ? smp_get_schedule_timestamp($second_end_date, 'end') : PHP_INT_MAX;

  return $first_start <= $second_end && $second_start <= $first_end;
}

/* =====================================================
   1. CUSTOM POST TYPE
===================================================== */
function smp_register_cpt()
{
  register_post_type('smp_modal', [
    'label' => smp_translate('Modales'),
    'public' => false,
    'show_ui' => true,
    'menu_icon' => 'dashicons-format-image',
    'supports' => ['title']
  ]);
}
add_action('init', 'smp_register_cpt');

add_action('admin_init', function () {
  $modal_ids = get_posts([
    'post_type' => 'smp_modal',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'fields' => 'ids',
    'no_found_rows' => true
  ]);

  foreach ($modal_ids as $modal_id) {
    if (!smp_has_publication_schedule($modal_id) || !smp_is_publication_schedule_expired($modal_id)) {
      continue;
    }

    wp_update_post([
      'ID' => absint($modal_id),
      'post_status' => 'draft',
    ]);
  }
});

/* =====================================================
   2. ADMIN COLUMNS
===================================================== */
add_filter('manage_smp_modal_posts_columns', function ($columns) {
  $new_columns = [];

  foreach ($columns as $key => $label) {
    $new_columns[$key] = $label;

    if ($key === 'title') {
      $new_columns['smp_publication_status'] = smp_translate('Estado');
      $new_columns['smp_enabled'] = smp_translate('Activo');
      $new_columns['smp_publication_schedule'] = smp_translate('Programación');
      $new_columns['smp_target_pages'] = smp_translate('Páginas del modal');
    }
  }

  return $new_columns;
});

add_action('manage_smp_modal_posts_custom_column', function ($column, $post_id) {
  if ($column === 'smp_publication_status') {
    $status_badge = smp_get_publication_status_badge($post_id);

    echo '<span class="smp-status-badge ' . esc_attr($status_badge['class']) . '">' . esc_html($status_badge['label']) . '</span>';
    return;
  }

  if ($column === 'smp_publication_schedule') {
    $schedule = smp_get_publication_schedule($post_id);

    if (!$schedule['start_date'] && !$schedule['end_date']) {
      echo '<span class="smp-empty-column">' . esc_html(smp_translate('Sin programación')) . '</span>';
      return;
    }

    $start_label = $schedule['start_date'] ? $schedule['start_date'] : smp_translate('Sin inicio');
    $end_label = $schedule['end_date'] ? $schedule['end_date'] : smp_translate('Sin fin');

    echo esc_html($start_label . ' - ' . $end_label);
    return;
  }

  if ($column === 'smp_enabled') {
    $enabled = get_post_meta($post_id, '_smp_enabled', true);
    $enabled = ($enabled === '' || $enabled === null) ? '1' : $enabled;
    $status_class = $enabled === '1' ? 'smp-status-badge--published' : 'smp-status-badge--unpublished';
    $status_label = $enabled === '1' ? smp_translate('Sí') : smp_translate('No');

    echo '<span class="smp-status-badge ' . esc_attr($status_class) . '">' . esc_html($status_label) . '</span>';
    return;
  }

  if ($column === 'smp_target_pages') {
    $page_ids = array_filter(array_map('absint', (array) get_post_meta($post_id, '_smp_pages', true)));

    if (!$page_ids) {
      echo '<span class="smp-empty-column">' . smp_esc_html('Sin páginas asignadas') . '</span>';
      return;
    }

    $page_links = [];

    foreach ($page_ids as $page_id) {
      if (get_post_type($page_id) !== 'page') {
        continue;
      }

      $page_title = smp_get_page_hierarchy_label($page_id);
      $page_title = $page_title ? $page_title : sprintf(smp_translate('Página #%d'), $page_id);
      $edit_link = get_edit_post_link($page_id);

      $page_links[] = $edit_link
        ? '<a href="' . esc_url($edit_link) . '">' . esc_html($page_title) . '</a>'
        : esc_html($page_title);
    }

    echo $page_links ? wp_kses_post(implode(', ', $page_links)) : '<span class="smp-empty-column">' . smp_esc_html('Sin páginas asignadas') . '</span>';
  }
}, 10, 2);

/* =====================================================
   3. ASSETS
===================================================== */
add_action('admin_enqueue_scripts', function ($hook) {
  $screen = function_exists('get_current_screen') ? get_current_screen() : null;

  if (!$screen || $screen->post_type !== 'smp_modal') {
    return;
  }

  wp_enqueue_style(
    'smp-admin',
    smp_get_asset_url('assets/css/admin.css'),
    [],
    smp_get_asset_version('assets/css/admin.css')
  );

  if ($hook !== 'post.php' && $hook !== 'post-new.php') {
    return;
  }

  wp_enqueue_media();
  wp_enqueue_script(
    'smp-admin',
    smp_get_asset_url('assets/js/admin.js'),
    ['jquery', 'media-editor'],
    smp_get_asset_version('assets/js/admin.js'),
    true
  );

  wp_localize_script('smp-admin', 'SMP_ADMIN_I18N', [
    'mediaTitle' => smp_translate('Seleccionar Imagen'),
    'mediaButton' => smp_translate('Usar Imagen'),
    'videoMediaTitle' => smp_translate('Seleccionar Video'),
    'videoMediaButton' => smp_translate('Usar Video'),
    'videoMimeTypes' => smp_get_allowed_video_mime_types(),
  ]);
});

add_action('wp_enqueue_scripts', function () {
  wp_enqueue_style(
    'smp-frontend',
    smp_get_asset_url('assets/css/frontend.css'),
    [],
    smp_get_asset_version('assets/css/frontend.css')
  );

  wp_enqueue_script(
    'smp-frontend',
    smp_get_asset_url('assets/js/frontend.js'),
    [],
    smp_get_asset_version('assets/js/frontend.js'),
    false
  );
});

/* =====================================================
   4. META BOX
===================================================== */
function smp_add_meta_boxes()
{
  add_meta_box('smp_settings', smp_translate('Configuración del Modal'), 'smp_meta_callback', 'smp_modal', 'normal', 'high');
}
add_action('add_meta_boxes', 'smp_add_meta_boxes');

function smp_get_used_pages_by_active_modals($exclude_modal_id, $start_date = '', $end_date = '')
{
  $exclude_modal_id = absint($exclude_modal_id);

  $modal_ids = get_posts([
    'post_type' => 'smp_modal',
    'posts_per_page' => -1,
    'post_status' => 'publish',
    'fields' => 'ids',
    'post__not_in' => [$exclude_modal_id],
    'no_found_rows' => true,
    'meta_query' => [
      'relation' => 'OR',
      [
        'key' => '_smp_enabled',
        'compare' => 'NOT EXISTS'
      ],
      [
        'key' => '_smp_enabled',
        'value' => '1',
        'compare' => '='
      ]
    ]
  ]);

  $used_pages = [];

  foreach ($modal_ids as $modal_id) {
    $schedule = smp_get_publication_schedule($modal_id);

    if (!smp_schedules_overlap($start_date, $end_date, $schedule['start_date'], $schedule['end_date'])) {
      continue;
    }

    $pages = array_filter(array_map('absint', (array) get_post_meta($modal_id, '_smp_pages', true)));

    foreach ($pages as $page_id) {
      if (get_post_type($page_id) === 'page') {
        $used_pages[$page_id] = true;
      }
    }
  }

  return array_keys($used_pages);
}

function smp_get_active_modals_with_pages()
{
  $modal_ids = get_posts([
    'post_type' => 'smp_modal',
    'posts_per_page' => -1,
    'post_status' => 'publish',
    'fields' => 'ids',
    'no_found_rows' => true,
    'meta_query' => [
      'relation' => 'OR',
      [
        'key' => '_smp_enabled',
        'compare' => 'NOT EXISTS'
      ],
      [
        'key' => '_smp_enabled',
        'value' => '1',
        'compare' => '='
      ]
    ]
  ]);

  $items = [];

  foreach ($modal_ids as $modal_id) {
    if (!smp_is_modal_in_publication_window($modal_id)) {
      continue;
    }

    $pages = array_filter(array_map('absint', (array) get_post_meta($modal_id, '_smp_pages', true)));
    $pages = array_values(array_filter($pages, function ($page_id) {
      return get_post_type($page_id) === 'page';
    }));

    if (!$pages) {
      continue;
    }

    $items[] = [
      'id' => absint($modal_id),
      'title' => get_the_title($modal_id),
      'pages' => $pages,
    ];
  }

  return $items;
}

function smp_meta_callback($post)
{
  wp_nonce_field('smp_save_meta', 'smp_nonce');

  $delay = absint(get_post_meta($post->ID, '_smp_delay', true));
  $delay = $delay ?: 2000;
  $pages = array_map('absint', (array) get_post_meta($post->ID, '_smp_pages', true));
  $frequency = get_post_meta($post->ID, '_smp_frequency', true);
  $frequency = in_array($frequency, ['session', 'persistent'], true) ? $frequency : 'session';
  $cta_url = get_post_meta($post->ID, '_smp_cta_url', true);
  $cta_id = get_post_meta($post->ID, '_smp_cta_id', true);
  $cta_target = get_post_meta($post->ID, '_smp_cta_target', true);
  $cta_target = in_array($cta_target, ['_self', '_blank'], true) ? $cta_target : '_self';
  $cta_label = get_post_meta($post->ID, '_smp_cta_label', true);
  $image_id = absint(get_post_meta($post->ID, '_smp_image_id', true));
  $media_type = smp_get_modal_media_type($post->ID);
  $video_id = smp_sanitize_video_attachment_id(get_post_meta($post->ID, '_smp_video_id', true));
  $video_url = $video_id ? wp_get_attachment_url($video_id) : '';
  $youtube_url = get_post_meta($post->ID, '_smp_youtube_url', true);
  $media_type_labels = [
    'image' => smp_translate('Imagen'),
    'video' => smp_translate('Video MP4/WebM'),
    'youtube' => smp_translate('Video de YouTube'),
  ];
  $enabled = get_post_meta($post->ID, '_smp_enabled', true);
  $enabled = ($enabled === '' || $enabled === null) ? '1' : $enabled;
  $schedule = smp_get_publication_schedule($post->ID);
  $used_pages = smp_get_used_pages_by_active_modals($post->ID, $schedule['start_date'], $schedule['end_date']);
  $active_items = smp_get_active_modals_with_pages();
?>

  <div class="smp-admin-fields">
    <p class="smp-field">
      <label>
        <input type="checkbox" name="smp_enabled" value="1" <?php checked($enabled, '1'); ?>>
        <strong><?php echo smp_esc_html('Modal activo'); ?></strong>
      </label>
    </p>

    <p class="smp-field">
      <label for="smp_delay"><strong><?php echo smp_esc_html('Delay (ms)'); ?></strong></label>
      <input type="number" name="smp_delay" id="smp_delay" class="smp-field__number" min="0" step="100" value="<?php echo esc_attr($delay); ?>">
    </p>

    <p class="smp-field">
      <label for="smp_frequency"><strong><?php echo smp_esc_html('Frecuencia'); ?></strong></label>
      <select name="smp_frequency" id="smp_frequency" class="smp-field__select">
        <option value="session" <?php selected($frequency, 'session'); ?>><?php echo smp_esc_html('Una vez por sesión'); ?></option>
        <option value="persistent" <?php selected($frequency, 'persistent'); ?>><?php echo smp_esc_html('Persistente (1 hora)'); ?></option>
      </select>
    </p>

    <fieldset class="smp-field smp-schedule">
      <legend><strong><?php echo smp_esc_html('Programación de publicación'); ?></strong></legend>
      <p class="description"><?php echo smp_esc_html('Deja las fechas vacías para mantener el modal siempre visible mientras esté publicado y activo.'); ?></p>
      <label class="smp-schedule__field" for="smp_start_date">
        <span><?php echo smp_esc_html('Fecha de inicio'); ?></span>
        <input type="date" id="smp_start_date" name="smp_start_date" value="<?php echo esc_attr($schedule['start_date']); ?>">
      </label>
      <label class="smp-schedule__field" for="smp_end_date">
        <span><?php echo smp_esc_html('Fecha de fin'); ?></span>
        <input type="date" id="smp_end_date" name="smp_end_date" value="<?php echo esc_attr($schedule['end_date']); ?>">
      </label>
    </fieldset>

    <hr>

    <h4><?php echo smp_esc_html('Páginas'); ?></h4>
    <div class="smp-page-selector">
      <label for="smp_page_search" class="screen-reader-text"><?php echo smp_esc_html('Buscar páginas'); ?></label>
      <input type="search" id="smp_page_search" class="smp-field__control smp-page-selector__search" placeholder="<?php echo smp_esc_attr('Buscar páginas...'); ?>">

      <div class="smp-page-selector__list" role="group" aria-label="<?php echo smp_esc_attr('Páginas disponibles'); ?>">
        <?php foreach (get_pages() as $page) : ?>
          <?php
          $page_id = absint($page->ID);
          $page_title = smp_get_page_hierarchy_label($page_id);
          $page_title = $page_title ? $page_title : sprintf(smp_translate('Página #%d'), $page_id);
          $is_selected = in_array($page_id, $pages, true);
          $is_used = in_array($page_id, $used_pages, true);
          ?>
          <label class="smp-page-selector__item" data-smp-page-title="<?php echo esc_attr(strtolower($page_title)); ?>">
            <input type="checkbox" name="smp_pages[]" value="<?php echo esc_attr($page_id); ?>" <?php checked($is_selected); ?> <?php disabled($is_used); ?>>
            <span><?php echo esc_html($page_title); ?></span>
          </label>
        <?php endforeach; ?>
      </div>

      <p class="description smp-page-selector__empty"><?php echo smp_esc_html('No se encontraron páginas con ese criterio.'); ?></p>
    </div>

    <h4><?php echo smp_esc_html('Páginas donde está activo un modal'); ?></h4>
    <table class="widefat striped smp-active-table">
      <thead>
        <tr>
          <th scope="col"><?php echo smp_esc_html('Modal'); ?></th>
          <th scope="col"><?php echo smp_esc_html('Páginas'); ?></th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$active_items) : ?>
          <tr>
            <td colspan="2"><?php echo smp_esc_html('No hay modales activos con páginas asignadas.'); ?></td>
          </tr>
        <?php else : ?>
          <?php foreach ($active_items as $item) : ?>
            <?php
            $modal_title = $item['title'] ? $item['title'] : sprintf(smp_translate('Modal #%d'), $item['id']);
            $page_titles = [];

            foreach ($item['pages'] as $page_id) {
              $page_title = smp_get_page_hierarchy_label($page_id);
              $page_titles[] = $page_title ? $page_title : sprintf(smp_translate('Página #%d'), $page_id);
            }
            ?>
            <tr>
              <td><?php echo esc_html($modal_title); ?></td>
              <td><?php echo esc_html(implode(', ', $page_titles)); ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>

    <hr>

    <fieldset class="smp-field smp-media-type">
      <legend><strong><?php echo smp_esc_html('Tipo de contenido'); ?></strong></legend>
      <?php foreach ($media_type_labels as $media_type_value => $media_type_label) : ?>
        <label class="smp-media-type__option">
          <input type="radio" name="smp_media_type" value="<?php echo esc_attr($media_type_value); ?>" <?php checked($media_type, $media_type_value); ?>>
          <span><?php echo esc_html($media_type_label); ?></span>
        </label>
      <?php endforeach; ?>
    </fieldset>

    <div class="smp-media-section" data-smp-media-section="image" <?php echo $media_type === 'image' ? '' : 'hidden'; ?>>
      <h4><?php echo smp_esc_html('Imagen del Modal'); ?></h4>
      <input type="hidden" id="smp_image_id" name="smp_image_id" value="<?php echo esc_attr($image_id); ?>">
      <button type="button" class="button" id="smp_upload_image_button"><?php echo smp_esc_html('Seleccionar Imagen'); ?></button>

      <div id="smp_image_preview" class="smp-image-preview">
        <?php
        if ($image_id) {
          echo wp_kses_post(wp_get_attachment_image($image_id, 'medium'));
        }
        ?>
      </div>
    </div>

    <div class="smp-media-section" data-smp-media-section="video" <?php echo $media_type === 'video' ? '' : 'hidden'; ?>>
      <h4><?php echo smp_esc_html('Video del Modal'); ?></h4>
      <p class="description"><?php echo smp_esc_html('Formatos permitidos: MP4 y WebM.'); ?></p>
      <input type="hidden" id="smp_video_id" name="smp_video_id" value="<?php echo esc_attr($video_id); ?>">
      <button type="button" class="button" id="smp_upload_video_button"><?php echo smp_esc_html('Seleccionar Video'); ?></button>

      <div id="smp_video_preview" class="smp-image-preview">
        <?php if ($video_url) : ?>
          <video src="<?php echo esc_url($video_url); ?>" controls preload="metadata"></video>
        <?php endif; ?>
      </div>
    </div>

    <div class="smp-media-section" data-smp-media-section="youtube" <?php echo $media_type === 'youtube' ? '' : 'hidden'; ?>>
      <p class="smp-field">
        <label for="smp_youtube_url"><strong><?php echo smp_esc_html('URL de YouTube'); ?></strong></label>
        <input type="url" name="smp_youtube_url" id="smp_youtube_url" class="smp-field__control" placeholder="https://www.youtube.com/watch?v=" value="<?php echo esc_attr($youtube_url); ?>">
        <span class="description"><?php echo smp_esc_html('Acepta enlaces de youtube.com, youtu.be y YouTube Shorts.'); ?></span>
      </p>
    </div>

    <hr>

    <p class="description"><?php echo smp_esc_html('Con imagen, la URL enlaza la imagen. Con video o YouTube, se muestra un botón debajo del video con esta URL y el ID Tag Manager.'); ?></p>

    <p class="smp-field">
      <label for="smp_cta_url"><strong><?php echo smp_esc_html('URL del Botón'); ?></strong></label>
      <input type="url" name="smp_cta_url" id="smp_cta_url" class="smp-field__control" value="<?php echo esc_attr($cta_url); ?>">
    </p>

    <p class="smp-field smp-media-section" data-smp-media-section="video youtube" <?php echo $media_type === 'image' ? 'hidden' : ''; ?>>
      <label for="smp_cta_label"><strong><?php echo smp_esc_html('Texto del botón'); ?></strong></label>
      <input type="text" name="smp_cta_label" id="smp_cta_label" class="smp-field__control" placeholder="<?php echo smp_esc_attr('Más información'); ?>" value="<?php echo esc_attr($cta_label); ?>">
    </p>

    <p class="smp-field">
      <label for="smp_cta_id"><strong><?php echo smp_esc_html('ID Tag Manager'); ?></strong></label>
      <input type="text" name="smp_cta_id" id="smp_cta_id" class="smp-field__control" value="<?php echo esc_attr($cta_id); ?>">
    </p>

    <p class="smp-field">
      <label for="smp_cta_target"><strong><?php echo smp_esc_html('Target'); ?></strong></label>
      <select name="smp_cta_target" id="smp_cta_target" class="smp-field__select">
        <option value="_self" <?php selected($cta_target, '_self'); ?>><?php echo smp_esc_html('Misma pestaña'); ?></option>
        <option value="_blank" <?php selected($cta_target, '_blank'); ?>><?php echo smp_esc_html('Nueva pestaña'); ?></option>
      </select>
    </p>
  </div>
<?php
}

/* =====================================================
   5. GUARDAR META
===================================================== */
function smp_save_meta($post_id)
{
  if (get_post_type($post_id) !== 'smp_modal') return;
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
  if (!current_user_can('edit_post', $post_id)) return;
  if (!isset($_POST['smp_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['smp_nonce'])), 'smp_save_meta')) return;

  update_post_meta($post_id, '_smp_enabled', isset($_POST['smp_enabled']) ? '1' : '0');

  $delay = isset($_POST['smp_delay']) ? absint(wp_unslash($_POST['smp_delay'])) : 2000;
  update_post_meta($post_id, '_smp_delay', $delay);

  $frequency = isset($_POST['smp_frequency']) ? sanitize_key(wp_unslash($_POST['smp_frequency'])) : 'session';
  $frequency = in_array($frequency, ['session', 'persistent'], true) ? $frequency : 'session';
  update_post_meta($post_id, '_smp_frequency', $frequency);

  $start_date = isset($_POST['smp_start_date']) ? smp_sanitize_schedule_date(wp_unslash($_POST['smp_start_date'])) : '';
  update_post_meta($post_id, '_smp_start_date', $start_date);

  $end_date = isset($_POST['smp_end_date']) ? smp_sanitize_schedule_date(wp_unslash($_POST['smp_end_date'])) : '';
  update_post_meta($post_id, '_smp_end_date', $end_date);

  $requested_pages = isset($_POST['smp_pages']) ? (array) wp_unslash($_POST['smp_pages']) : [];
  $requested_pages = array_values(array_filter(array_map('absint', $requested_pages)));
  $requested_pages = array_values(array_filter($requested_pages, function ($page_id) {
    return get_post_type($page_id) === 'page';
  }));

  $used_pages = smp_get_used_pages_by_active_modals($post_id, $start_date, $end_date);
  update_post_meta($post_id, '_smp_pages', array_values(array_diff($requested_pages, $used_pages)));

  $cta_url = isset($_POST['smp_cta_url']) ? esc_url_raw(wp_unslash($_POST['smp_cta_url'])) : '';
  update_post_meta($post_id, '_smp_cta_url', $cta_url);

  $cta_id = isset($_POST['smp_cta_id']) ? sanitize_html_class(wp_unslash($_POST['smp_cta_id'])) : '';
  update_post_meta($post_id, '_smp_cta_id', $cta_id);

  $cta_target = isset($_POST['smp_cta_target']) ? sanitize_key(wp_unslash($_POST['smp_cta_target'])) : '_self';
  $cta_target = in_array($cta_target, ['_self', '_blank'], true) ? $cta_target : '_self';
  update_post_meta($post_id, '_smp_cta_target', $cta_target);

  $cta_label = isset($_POST['smp_cta_label']) ? sanitize_text_field(wp_unslash($_POST['smp_cta_label'])) : '';
  update_post_meta($post_id, '_smp_cta_label', $cta_label);

  $image_id = isset($_POST['smp_image_id']) ? absint(wp_unslash($_POST['smp_image_id'])) : 0;
  update_post_meta($post_id, '_smp_image_id', $image_id);

  $media_type = isset($_POST['smp_media_type']) ? smp_sanitize_media_type(wp_unslash($_POST['smp_media_type'])) : 'image';
  update_post_meta($post_id, '_smp_media_type', $media_type);

  $video_id = isset($_POST['smp_video_id']) ? smp_sanitize_video_attachment_id(wp_unslash($_POST['smp_video_id'])) : 0;
  update_post_meta($post_id, '_smp_video_id', $video_id);

  $youtube_url = isset($_POST['smp_youtube_url']) ? smp_sanitize_youtube_url(wp_unslash($_POST['smp_youtube_url'])) : '';
  update_post_meta($post_id, '_smp_youtube_url', $youtube_url);

}
add_action('save_post', 'smp_save_meta');

add_filter('wp_insert_post_data', function ($data, $postarr) {
  if (empty($data['post_type']) || $data['post_type'] !== 'smp_modal') {
    return $data;
  }

  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return $data;
  }

  $post_id = !empty($postarr['ID']) ? absint($postarr['ID']) : 0;
  $end_date = isset($_POST['smp_end_date'])
    ? smp_sanitize_schedule_date(wp_unslash($_POST['smp_end_date']))
    : ($post_id ? smp_sanitize_schedule_date(get_post_meta($post_id, '_smp_end_date', true)) : '');

  if (in_array($data['post_status'], ['publish', 'future'], true) && $end_date && time() > smp_get_schedule_timestamp($end_date, 'end')) {
    $data['post_status'] = 'draft';
  }

  return $data;
}, 10, 2);

/* =====================================================
   6. MOTOR DE REGLAS
===================================================== */
function smp_get_current_context_page_ids()
{
  $page_ids = [];
  $queried_id = absint(get_queried_object_id());

  if ($queried_id && get_post_type($queried_id) === 'page') {
    $page_ids[] = $queried_id;
  }

  if (is_home()) {
    $posts_page_id = absint(get_option('page_for_posts'));

    if ($posts_page_id) {
      $page_ids[] = $posts_page_id;
    }
  }

  if (is_front_page()) {
    $front_page_id = absint(get_option('page_on_front'));

    if ($front_page_id) {
      $page_ids[] = $front_page_id;
    }
  }

  global $post;

  if ($post instanceof WP_Post && $post->post_type === 'page') {
    $page_ids[] = absint($post->ID);
  }

  return array_values(array_unique(array_filter($page_ids)));
}

function smp_get_current_page_modal_ids()
{
  static $modal_ids = null;

  if ($modal_ids !== null) {
    return $modal_ids;
  }

  $modal_ids = [];
  $context_page_ids = smp_get_current_context_page_ids();

  if (!$context_page_ids) {
    return $modal_ids;
  }

  $candidate_ids = get_posts([
    'post_type' => 'smp_modal',
    'posts_per_page' => -1,
    'post_status' => 'publish',
    'fields' => 'ids',
    'no_found_rows' => true,
    'meta_query' => [
      'relation' => 'OR',
      [
        'key' => '_smp_enabled',
        'compare' => 'NOT EXISTS'
      ],
      [
        'key' => '_smp_enabled',
        'value' => '1',
        'compare' => '='
      ]
    ]
  ]);

  foreach ($candidate_ids as $modal_id) {
    if (!smp_is_modal_in_publication_window($modal_id)) {
      continue;
    }

    $pages = array_map('absint', (array) get_post_meta($modal_id, '_smp_pages', true));

    if ($pages && !array_intersect($pages, $context_page_ids)) {
      continue;
    }

    $modal_ids[] = absint($modal_id);
  }

  return $modal_ids;
}

/* =====================================================
   7. FRONTEND
===================================================== */
function smp_get_modal_cta($modal_id)
{
  $cta_target = get_post_meta($modal_id, '_smp_cta_target', true);
  $cta_target = in_array($cta_target, ['_self', '_blank'], true) ? $cta_target : '_self';
  $cta_label = sanitize_text_field(get_post_meta($modal_id, '_smp_cta_label', true));

  return [
    'url' => esc_url_raw(get_post_meta($modal_id, '_smp_cta_url', true)),
    'id' => sanitize_html_class(get_post_meta($modal_id, '_smp_cta_id', true)),
    'target' => $cta_target,
    'rel' => $cta_target === '_blank' ? 'noopener noreferrer' : '',
    'label' => $cta_label !== '' ? $cta_label : smp_translate('Más información'),
  ];
}

function smp_get_cta_link_attributes($cta)
{
  $attributes = ' href="' . esc_url($cta['url']) . '" target="' . esc_attr($cta['target']) . '"';

  if ($cta['rel']) {
    $attributes .= ' rel="' . esc_attr($cta['rel']) . '"';
  }

  if ($cta['id']) {
    $attributes .= ' id="' . esc_attr($cta['id']) . '"';
  }

  return $attributes;
}

function smp_get_modal_image_html($modal_id, $cta)
{
  $image_id = absint(get_post_meta($modal_id, '_smp_image_id', true));
  $image_html = $image_id ? wp_get_attachment_image($image_id, 'large', false, [
    'class' => 'smp-modal__image',
    'loading' => 'lazy',
    'decoding' => 'async',
  ]) : '';

  if (!$image_html) {
    return '';
  }

  $image_html = wp_kses_post($image_html);

  if ($cta['url']) {
    $image_html = '<a' . smp_get_cta_link_attributes($cta) . '>' . $image_html . '</a>';
  }

  return '<div class="smp-image-wrapper">' . $image_html . '</div>';
}

function smp_get_modal_video_html($modal_id, $modal_title)
{
  $video_id = smp_sanitize_video_attachment_id(get_post_meta($modal_id, '_smp_video_id', true));
  $video_url = $video_id ? wp_get_attachment_url($video_id) : '';

  if (!$video_url) {
    return '';
  }

  // preload="none" evita descargar el video mientras el modal está oculto; el JS lo activa al mostrarlo.
  return '<div class="smp-media smp-media--video">'
    . '<video class="smp-modal__video" controls playsinline preload="none" aria-label="' . esc_attr($modal_title) . '">'
    . '<source src="' . esc_url($video_url) . '" type="' . esc_attr(get_post_mime_type($video_id)) . '">'
    . '</video>'
    . '</div>';
}

function smp_get_modal_youtube_html($modal_id, $modal_title)
{
  $youtube_video_id = smp_get_youtube_video_id(get_post_meta($modal_id, '_smp_youtube_url', true));

  if (!$youtube_video_id) {
    return '';
  }

  $embed_url = 'https://www.youtube-nocookie.com/embed/' . rawurlencode($youtube_video_id) . '?rel=0';

  // El iframe se carga con data-smp-src para no pedir recursos a YouTube hasta que el modal se muestre.
  return '<div class="smp-media smp-media--youtube">'
    . '<iframe class="smp-modal__youtube" data-smp-src="' . esc_url($embed_url) . '" title="' . esc_attr($modal_title) . '"'
    . ' allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"'
    . ' referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>'
    . '</div>';
}

function smp_get_modal_cta_button_html($cta)
{
  if (!$cta['url']) {
    return '';
  }

  return '<div class="smp-modal__actions">'
    . '<a class="smp-modal__cta"' . smp_get_cta_link_attributes($cta) . '>' . esc_html($cta['label']) . '</a>'
    . '</div>';
}

function smp_get_modal_content_html($modal_id, $media_type, $modal_title)
{
  $cta = smp_get_modal_cta($modal_id);

  if ($media_type === 'image') {
    return smp_get_modal_image_html($modal_id, $cta);
  }

  $video_html = $media_type === 'youtube'
    ? smp_get_modal_youtube_html($modal_id, $modal_title)
    : smp_get_modal_video_html($modal_id, $modal_title);

  if (!$video_html) {
    return '';
  }

  return $video_html . smp_get_modal_cta_button_html($cta);
}

function smp_get_modal_classes($modal_id, $media_type)
{
  if ($media_type === 'image') {
    return 'smp-modal';
  }

  $modal_classes = ['smp-modal', 'smp-modal--video'];

  // El iframe de YouTube no expone sus dimensiones, por eso los Shorts se marcan como verticales desde PHP.
  if ($media_type === 'youtube' && smp_is_youtube_short_url(get_post_meta($modal_id, '_smp_youtube_url', true))) {
    $modal_classes[] = 'smp-modal--portrait';
    $modal_classes[] = 'smp-modal--youtube-short';
  }

  return implode(' ', $modal_classes);
}

function smp_render_modals()
{
  static $rendered = false;

  if ($rendered) {
    return;
  }

  $modal_ids = smp_get_current_page_modal_ids();

  if (!$modal_ids) {
    return;
  }

  $rendered = true;

  foreach ($modal_ids as $modal_id) {
    $modal = get_post($modal_id);

    if (!$modal || $modal->post_type !== 'smp_modal') {
      continue;
    }

    $delay = absint(get_post_meta($modal_id, '_smp_delay', true));
    $delay = $delay ?: 2000;
    $frequency = get_post_meta($modal_id, '_smp_frequency', true);
    $frequency = in_array($frequency, ['session', 'persistent'], true) ? $frequency : 'session';
    $modal_title = get_the_title($modal_id);
    $media_type = smp_get_modal_media_type($modal_id);
    $modal_classes = smp_get_modal_classes($modal_id, $media_type);
    $content_html = smp_get_modal_content_html($modal_id, $media_type, $modal_title);
?>
    <div class="smp-overlay" id="smp-<?php echo esc_attr($modal_id); ?>" data-smp-delay="<?php echo esc_attr($delay); ?>" data-smp-frequency="<?php echo esc_attr($frequency); ?>">
      <div class="<?php echo esc_attr($modal_classes); ?>" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr($modal_title); ?>">
        <button type="button" class="smp-close" aria-label="<?php echo smp_esc_attr('Cerrar modal'); ?>">&times;</button>

        <?php
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Cada helper smp_get_modal_*_html escapa su salida.
        echo $content_html;
        ?>
      </div>
    </div>
<?php
  }
}
add_action('wp_body_open', 'smp_render_modals');
add_action('wp_footer', 'smp_render_modals');
