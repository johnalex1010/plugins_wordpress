<?php

/**
 * Plugin Name: SPEC Floating Banner
 * Description: Gestiona banners flotantes por página con imagen o video, CTA, target configurable, cierre temporal y columnas administrativas de estado/asignación.
 * Version: 1.14
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Text Domain: spec-floating-banner
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
  exit;
}

add_action('plugins_loaded', function () {
  load_plugin_textdomain('spec-floating-banner', false, dirname(plugin_basename(__FILE__)) . '/languages');

  $locale = determine_locale();
  $php_translation_file = plugin_dir_path(__FILE__) . 'languages/spec-floating-banner-' . $locale . '.l10n.php';

  if (is_readable($php_translation_file)) {
    load_textdomain('spec-floating-banner', $php_translation_file);
  }
});

function sfb_translate($text)
{
  $locale = function_exists('determine_locale') ? determine_locale() : get_locale();

  if (strpos($locale, 'en') !== 0) {
    return __($text, 'spec-floating-banner');
  }

  $translations = [
    'Configuración del Banner' => 'Banner Settings',
    'ConfiguraciÃ³n del Banner' => 'Banner Settings',
    'Tipo de banner' => 'Banner type',
    'Tipo de pieza' => 'Piece type',
    'Imagen' => 'Image',
    'Video' => 'Video',
    'Seleccionar Imagen' => 'Select Image',
    'Seleccionar imagen' => 'Select image',
    'Cargar video' => 'Upload video',
    'Seleccionar video' => 'Select video',
    'La imagen es obligatoria para publicar el banner.' => 'An image is required to publish the banner.',
    'Selecciona una imagen antes de guardar el banner.' => 'Select an image before saving the banner.',
    'El video es obligatorio para publicar este banner.' => 'A video is required to publish this banner.',
    'Selecciona un video MP4 o WebM antes de guardar el banner.' => 'Select an MP4 or WebM video before saving the banner.',
    'Enlace del banner' => 'Banner link',
    'El enlace es obligatorio y debe ser una URL válida.' => 'The link is required and must be a valid URL.',
    'El enlace es obligatorio y debe ser una URL vÃ¡lida.' => 'The link is required and must be a valid URL.',
    'El enlace es opcional. Puede ser una URL válida, # o un ancla como #formulario.' => 'The link is optional. It can be a valid URL, #, or an anchor like #formulario.',
    'Nombre CTA' => 'CTA label',
    'El nombre del CTA es obligatorio para banners de video.' => 'The CTA label is required for video banners.',
    'Link CTA' => 'CTA link',
    'El link del CTA es obligatorio y debe ser una URL válida.' => 'The CTA link is required and must be a valid URL.',
    'El link del CTA es opcional. Puede ser una URL válida, # o un ancla como #formulario.' => 'The CTA link is optional. It can be a valid URL, #, or an anchor like #formulario.',
    'Target CTA' => 'CTA target',
    'ID del CTA' => 'CTA ID',
    'Opcional. Este ID se puede configurar con GTM para seguimiento de clics.' => 'Optional. This ID can be configured with GTM for click tracking.',
    'Target del enlace' => 'Link target',
    'Misma ventana (_self)' => 'Same window (_self)',
    'Nueva ventana (_blank)' => 'New window (_blank)',
    'Páginas donde está activo un banner flotante' => 'Pages where a floating banner is active',
    'PÃ¡ginas donde estÃ¡ activo un banner flotante' => 'Pages where a floating banner is active',
    'Programación' => 'Schedule',
    'Programación de publicación' => 'Publication schedule',
    'Deja las fechas vacías para mantener el banner siempre visible mientras esté publicado.' => 'Leave the dates empty to keep the banner always visible while it is published.',
    'Fecha de inicio' => 'Start date',
    'Fecha de fin' => 'End date',
    'Sin programación' => 'No schedule',
    'Sin inicio' => 'No start date',
    'Sin fin' => 'No end date',
    'Banner flotante' => 'Floating banner',
    'Páginas' => 'Pages',
    'PÃ¡ginas' => 'Pages',
    'No hay banners flotantes activos con páginas asignadas.' => 'There are no active floating banners with assigned pages.',
    'No hay banners flotantes activos con pÃ¡ginas asignadas.' => 'There are no active floating banners with assigned pages.',
    'Buscar páginas' => 'Search pages',
    'Buscar pÃ¡ginas' => 'Search pages',
    'Buscar páginas...' => 'Search pages...',
    'Buscar pÃ¡ginas...' => 'Search pages...',
    'Páginas disponibles' => 'Available pages',
    'PÃ¡ginas disponibles' => 'Available pages',
    'Página #%d' => 'Page #%d',
    'PÃ¡gina #%d' => 'Page #%d',
    'No se encontraron páginas con ese criterio.' => 'No pages were found with that criterion.',
    'No se encontraron pÃ¡ginas con ese criterio.' => 'No pages were found with that criterion.',
    'El banner quedó como borrador porque la imagen y el enlace son obligatorios para publicarlo.' => 'The banner was saved as a draft because the image and link are required to publish it.',
    'El banner quedÃ³ como borrador porque la imagen y el enlace son obligatorios para publicarlo.' => 'The banner was saved as a draft because the image and link are required to publish it.',
    'El banner quedó como borrador porque faltan campos obligatorios para publicarlo.' => 'The banner was saved as a draft because required fields are missing.',
  ];

  return isset($translations[$text]) ? $translations[$text] : __($text, 'spec-floating-banner');
}

function sfb_esc_html($text)
{
  return esc_html(sfb_translate($text));
}

function sfb_esc_attr($text)
{
  return esc_attr(sfb_translate($text));
}

function sfb_sanitize_media_type($media_type)
{
  $media_type = sanitize_key($media_type);

  return in_array($media_type, ['image', 'video'], true) ? $media_type : 'image';
}

function sfb_get_media_type_label($media_type)
{
  $media_type = sfb_sanitize_media_type($media_type);

  return $media_type === 'video' ? sfb_translate('Video') : sfb_translate('Imagen');
}

function sfb_get_page_hierarchy_label($page_id)
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
    $page_titles[] = $page_title ? $page_title : sprintf(sfb_translate('Página #%d'), $hierarchy_page_id);
  }

  return implode(' / ', $page_titles);
}

function sfb_sanitize_target($target)
{
  $target = sanitize_key($target);

  return in_array($target, ['_self', '_blank'], true) ? $target : '_blank';
}

function sfb_sanitize_link_url($link)
{
  $link = trim(sanitize_text_field($link));

  if ($link === '') {
    return '';
  }

  if (preg_match('/^#[A-Za-z0-9_\-:.\/?=&%]*$/', $link)) {
    return $link;
  }

  return esc_url_raw($link);
}

function sfb_esc_link_url($link)
{
  $link = (string) $link;

  if (strpos($link, '#') === 0) {
    return esc_attr($link);
  }

  return esc_url($link);
}

function sfb_is_valid_video_attachment($video_id)
{
  $video_id = absint($video_id);

  if (!$video_id) {
    return false;
  }

  return in_array(get_post_mime_type($video_id), ['video/mp4', 'video/webm'], true);
}

function sfb_sanitize_schedule_date($date)
{
  $date = trim(sanitize_text_field($date));

  if ($date === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    return '';
  }

  [$year, $month, $day] = array_map('absint', explode('-', $date));

  return checkdate($month, $day, $year) ? $date : '';
}

function sfb_get_schedule_timestamp($date, $boundary)
{
  $date = sfb_sanitize_schedule_date($date);

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

function sfb_get_publication_schedule($banner_id)
{
  return [
    'start_date' => sfb_sanitize_schedule_date(get_post_meta($banner_id, '_sfb_start_date', true)),
    'end_date' => sfb_sanitize_schedule_date(get_post_meta($banner_id, '_sfb_end_date', true)),
  ];
}

function sfb_is_banner_in_publication_window($banner_id, $timestamp = null)
{
  $timestamp = $timestamp ?: time();
  $schedule = sfb_get_publication_schedule($banner_id);
  $start_timestamp = $schedule['start_date'] ? sfb_get_schedule_timestamp($schedule['start_date'], 'start') : 0;
  $end_timestamp = $schedule['end_date'] ? sfb_get_schedule_timestamp($schedule['end_date'], 'end') : PHP_INT_MAX;

  return $timestamp >= $start_timestamp && $timestamp <= $end_timestamp;
}

function sfb_schedules_overlap($first_start_date, $first_end_date, $second_start_date, $second_end_date)
{
  $first_start = $first_start_date ? sfb_get_schedule_timestamp($first_start_date, 'start') : 0;
  $first_end = $first_end_date ? sfb_get_schedule_timestamp($first_end_date, 'end') : PHP_INT_MAX;
  $second_start = $second_start_date ? sfb_get_schedule_timestamp($second_start_date, 'start') : 0;
  $second_end = $second_end_date ? sfb_get_schedule_timestamp($second_end_date, 'end') : PHP_INT_MAX;

  return $first_start <= $second_end && $second_start <= $first_end;
}

/* =====================================================
   1. CUSTOM POST TYPE
===================================================== */
add_action('init', function () {
  register_post_type('sfb_banner', [
    'label' => __('Floating Banners', 'spec-floating-banner'),
    'public' => false,
    'show_ui' => true,
    'menu_icon' => 'dashicons-format-image',
    'supports' => ['title']
  ]);
});

/* =====================================================
   2. METABOX
===================================================== */
add_action('add_meta_boxes', function () {
  add_meta_box('sfb_config', sfb_translate('Configuración del Banner'), 'sfb_render_metabox', 'sfb_banner');
});

/* =====================================================
   2.1 ADMIN COLUMNS
===================================================== */
add_filter('manage_sfb_banner_posts_columns', function ($columns) {
  $custom_columns = [];

  foreach ($columns as $key => $label) {
    $custom_columns[$key] = $label;

    if ($key === 'title') {
      $custom_columns['sfb_media_type'] = __('Tipo de pieza', 'spec-floating-banner');
      $custom_columns['sfb_publication_status'] = __('Estado', 'spec-floating-banner');
      $custom_columns['sfb_publication_schedule'] = sfb_translate('Programación');
      $custom_columns['sfb_target_pages'] = __('Páginas del banner', 'spec-floating-banner');
    }
  }

  return $custom_columns;
});

add_action('manage_sfb_banner_posts_custom_column', function ($column, $post_id) {
  if ($column === 'sfb_media_type') {
    echo esc_html(sfb_get_media_type_label(get_post_meta($post_id, '_sfb_media_type', true)));
    return;
  }

  if ($column === 'sfb_publication_status') {
    $is_published = get_post_status($post_id) === 'publish';
    $status_class = $is_published ? 'sfb-status-badge--published' : 'sfb-status-badge--unpublished';
    $status_label = $is_published ? __('Publicado', 'spec-floating-banner') : __('No publicado', 'spec-floating-banner');

    echo '<span class="sfb-status-badge ' . esc_attr($status_class) . '">' . esc_html($status_label) . '</span>';
    return;
  }

  if ($column === 'sfb_publication_schedule') {
    $schedule = sfb_get_publication_schedule($post_id);

    if (!$schedule['start_date'] && !$schedule['end_date']) {
      echo '<span class="sfb-empty-column">' . esc_html(sfb_translate('Sin programación')) . '</span>';
      return;
    }

    $start_label = $schedule['start_date'] ? $schedule['start_date'] : sfb_translate('Sin inicio');
    $end_label = $schedule['end_date'] ? $schedule['end_date'] : sfb_translate('Sin fin');

    echo esc_html($start_label . ' - ' . $end_label);
    return;
  }

  if ($column === 'sfb_target_pages') {
    $page_ids = array_filter(array_map('absint', (array) get_post_meta($post_id, '_sfb_pages', true)));

    if (!$page_ids) {
      echo '<span class="sfb-empty-column">' . esc_html__('Sin páginas asignadas', 'spec-floating-banner') . '</span>';
      return;
    }

    $page_links = [];

    foreach ($page_ids as $page_id) {
      $page = get_post($page_id);

      if (!$page || $page->post_type !== 'page') {
        continue;
      }

      $page_title = sfb_get_page_hierarchy_label($page_id);
      $page_title = $page_title ? $page_title : sprintf(__('Página #%d', 'spec-floating-banner'), $page_id);
      $edit_link = get_edit_post_link($page_id);

      if ($edit_link) {
        $page_links[] = '<a href="' . esc_url($edit_link) . '">' . esc_html($page_title) . '</a>';
      } else {
        $page_links[] = esc_html($page_title);
      }
    }

    echo $page_links ? wp_kses_post(implode(', ', $page_links)) : '<span class="sfb-empty-column">' . esc_html__('Sin páginas asignadas', 'spec-floating-banner') . '</span>';
  }
}, 10, 2);

function sfb_get_asset_url($path)
{
  return plugin_dir_url(__FILE__) . ltrim($path, '/');
}

function sfb_get_asset_version($path)
{
  $file = plugin_dir_path(__FILE__) . ltrim($path, '/');

  return file_exists($file) ? (string) filemtime($file) : '1.14';
}

function sfb_get_current_page_banners()
{
  static $current_page_banners = null;

  if ($current_page_banners !== null) {
    return $current_page_banners;
  }

  $current_page_banners = [];

  if (!is_page()) {
    return $current_page_banners;
  }

  $page_id = absint(get_queried_object_id());

  if (!$page_id) {
    return $current_page_banners;
  }

  $banners = get_posts([
    'post_type' => 'sfb_banner',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'fields' => 'ids',
    'no_found_rows' => true
  ]);

  foreach ($banners as $banner_id) {
    if (!sfb_is_banner_in_publication_window($banner_id)) {
      continue;
    }

    $pages = array_map('absint', (array) get_post_meta($banner_id, '_sfb_pages', true));

    if (in_array($page_id, $pages, true)) {
      $current_page_banners[] = $banner_id;
    }
  }

  return $current_page_banners;
}

add_action('wp_enqueue_scripts', function () {
  if (!sfb_get_current_page_banners()) {
    return;
  }

  wp_enqueue_style(
    'sfb-frontend',
    sfb_get_asset_url('assets/css/frontend.css'),
    [],
    sfb_get_asset_version('assets/css/frontend.css')
  );

  wp_enqueue_script(
    'sfb-frontend',
    sfb_get_asset_url('assets/js/frontend.js'),
    [],
    sfb_get_asset_version('assets/js/frontend.js'),
    true
  );
});

add_action('admin_enqueue_scripts', function ($hook) {
  $screen = get_current_screen();

  if (!$screen || $screen->post_type !== 'sfb_banner') {
    return;
  }

  wp_enqueue_style(
    'sfb-admin',
    sfb_get_asset_url('assets/css/admin.css'),
    [],
    sfb_get_asset_version('assets/css/admin.css')
  );

  if ($hook !== 'post.php' && $hook !== 'post-new.php') {
    return;
  }

  wp_enqueue_media();

  wp_enqueue_script(
    'sfb-admin',
    sfb_get_asset_url('assets/js/admin.js'),
    ['jquery', 'media-editor'],
    sfb_get_asset_version('assets/js/admin.js'),
    true
  );

  wp_localize_script('sfb-admin', 'SFB_ADMIN_I18N', [
    'mediaTitle' => sfb_translate('Seleccionar imagen'),
    'videoTitle' => sfb_translate('Seleccionar video'),
  ]);
});

function sfb_render_metabox($post)
{
  $media_type = sfb_sanitize_media_type(get_post_meta($post->ID, '_sfb_media_type', true));
  $image_id = absint(get_post_meta($post->ID, '_sfb_image_id', true));
  $video_id = absint(get_post_meta($post->ID, '_sfb_video_id', true));
  $cta_label = get_post_meta($post->ID, '_sfb_cta_label', true);
  $cta_id = get_post_meta($post->ID, '_sfb_cta_id', true);
  $link = get_post_meta($post->ID, '_sfb_link', true);
  $target = sfb_sanitize_target(get_post_meta($post->ID, '_sfb_target', true));
  $selected_pages = array_map('absint', (array) get_post_meta($post->ID, '_sfb_pages', true));
  $schedule = sfb_get_publication_schedule($post->ID);

  $image_url = $image_id ? wp_get_attachment_url($image_id) : '';
  $video_url = $video_id ? wp_get_attachment_url($video_id) : '';

  $args = [
    'post_type' => 'sfb_banner',
    'post_status' => 'publish',
    'post__not_in' => [$post->ID],
    'posts_per_page' => -1,
    'fields' => 'ids',
    'no_found_rows' => true
  ];

  $used_pages = [];

  foreach (get_posts($args) as $banner_id) {
    $banner_schedule = sfb_get_publication_schedule($banner_id);

    if (!sfb_schedules_overlap($schedule['start_date'], $schedule['end_date'], $banner_schedule['start_date'], $banner_schedule['end_date'])) {
      continue;
    }

    $pages = array_map('absint', (array) get_post_meta($banner_id, '_sfb_pages', true));
    $used_pages = array_merge($used_pages, $pages);
  }

  $used_pages = array_unique($used_pages);

  $active_banner_rows = [];
  $active_banners = get_posts([
    'post_type' => 'sfb_banner',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'fields' => 'ids',
    'no_found_rows' => true
  ]);

  foreach ($active_banners as $active_banner_id) {
    if (!sfb_is_banner_in_publication_window($active_banner_id)) {
      continue;
    }

    $active_page_ids = array_filter(array_map('absint', (array) get_post_meta($active_banner_id, '_sfb_pages', true)));

    if (!$active_page_ids) {
      continue;
    }

    $active_page_titles = [];

    foreach ($active_page_ids as $active_page_id) {
      $active_page = get_post($active_page_id);

      if (!$active_page || $active_page->post_type !== 'page') {
        continue;
      }

      $active_page_title = sfb_get_page_hierarchy_label($active_page_id);
      $active_page_titles[] = $active_page_title ? $active_page_title : sprintf(__('Página #%d', 'spec-floating-banner'), $active_page_id);
    }

    if (!$active_page_titles) {
      continue;
    }

    $active_banner_rows[] = [
      'title' => get_the_title($active_banner_id),
      'media_type' => sfb_get_media_type_label(get_post_meta($active_banner_id, '_sfb_media_type', true)),
      'pages' => $active_page_titles
    ];
  }

  wp_nonce_field('sfb_save_banner', 'sfb_nonce');
?>

  <div>
    <fieldset class="sfb-field">
      <legend><strong><?php echo sfb_esc_html('Tipo de banner'); ?></strong></legend>
      <label class="sfb-option">
        <input type="radio" name="sfb_media_type" value="image" <?php checked($media_type, 'image'); ?>>
        <span><?php echo sfb_esc_html('Imagen'); ?></span>
      </label>
      <label class="sfb-option">
        <input type="radio" name="sfb_media_type" value="video" <?php checked($media_type, 'video'); ?>>
        <span><?php echo sfb_esc_html('Video'); ?></span>
      </label>
    </fieldset>

    <div class="sfb-media-panel" data-sfb-media-panel="image">
      <button type="button" class="button" id="sfb_upload_btn"><?php echo sfb_esc_html('Seleccionar Imagen'); ?></button>
      <input type="hidden" name="sfb_image_id" id="sfb_image_id" value="<?php echo esc_attr($image_id); ?>">
      <p class="description"><?php echo sfb_esc_html('La imagen es obligatoria para publicar el banner.'); ?></p>
      <div id="sfb_image_required_notice" class="notice notice-error sfb-required-notice">
        <p><?php echo sfb_esc_html('Selecciona una imagen antes de guardar el banner.'); ?></p>
      </div>

      <div class="sfb-preview">
        <img id="sfb_preview" class="sfb-preview__image" src="<?php echo esc_url($image_url); ?>" alt="">
      </div>
    </div>

    <div class="sfb-media-panel" data-sfb-media-panel="video">
      <button type="button" class="button" id="sfb_video_upload_btn"><?php echo sfb_esc_html('Cargar video'); ?></button>
      <input type="hidden" name="sfb_video_id" id="sfb_video_id" value="<?php echo esc_attr($video_id); ?>">
      <p class="description"><?php echo sfb_esc_html('El video es obligatorio para publicar este banner.'); ?></p>
      <div id="sfb_video_required_notice" class="notice notice-error sfb-required-notice">
        <p><?php echo sfb_esc_html('Selecciona un video MP4 o WebM antes de guardar el banner.'); ?></p>
      </div>

      <div class="sfb-preview">
        <video id="sfb_video_preview" class="sfb-preview__video" src="<?php echo esc_url($video_url); ?>" controls></video>
      </div>

      <p class="sfb-field">
        <label for="sfb_cta_label"><strong><?php echo sfb_esc_html('Nombre CTA'); ?></strong></label>
        <input type="text" name="sfb_cta_label" id="sfb_cta_label" class="sfb-field__control" value="<?php echo esc_attr($cta_label); ?>">
        <span class="description"><?php echo sfb_esc_html('El nombre del CTA es obligatorio para banners de video.'); ?></span>
      </p>

      <p class="sfb-field">
        <label for="sfb_cta_id"><strong><?php echo sfb_esc_html('ID del CTA'); ?></strong></label>
        <input type="text" name="sfb_cta_id" id="sfb_cta_id" class="sfb-field__control" value="<?php echo esc_attr($cta_id); ?>">
        <span class="description"><?php echo sfb_esc_html('Opcional. Este ID se puede configurar con GTM para seguimiento de clics.'); ?></span>
      </p>
    </div>

    <p class="sfb-field">
      <label for="sfb_link"><strong><span data-sfb-label="image"><?php echo sfb_esc_html('Enlace del banner'); ?></span><span data-sfb-label="video"><?php echo sfb_esc_html('Link CTA'); ?></span></strong></label>
      <input type="text" name="sfb_link" id="sfb_link" class="sfb-field__control" placeholder="<?php echo sfb_esc_attr('https://...'); ?>" value="<?php echo esc_attr($link); ?>">
      <span class="description" data-sfb-label="image"><?php echo sfb_esc_html('El enlace es opcional. Puede ser una URL válida, # o un ancla como #formulario.'); ?></span>
      <span class="description" data-sfb-label="video"><?php echo sfb_esc_html('El link del CTA es opcional. Puede ser una URL válida, # o un ancla como #formulario.'); ?></span>
    </p>

    <p class="sfb-field">
      <label for="sfb_target"><strong><span data-sfb-label="image"><?php echo sfb_esc_html('Target del enlace'); ?></span><span data-sfb-label="video"><?php echo sfb_esc_html('Target CTA'); ?></span></strong></label>
      <select name="sfb_target" id="sfb_target" class="sfb-field__select">
        <option value="_self" <?php selected($target, '_self'); ?>><?php echo sfb_esc_html('Misma ventana (_self)'); ?></option>
        <option value="_blank" <?php selected($target, '_blank'); ?>><?php echo sfb_esc_html('Nueva ventana (_blank)'); ?></option>
      </select>
    </p>

    <fieldset class="sfb-field sfb-schedule">
      <legend><strong><?php echo sfb_esc_html('Programación de publicación'); ?></strong></legend>
      <p class="description"><?php echo sfb_esc_html('Deja las fechas vacías para mantener el banner siempre visible mientras esté publicado.'); ?></p>
      <label class="sfb-schedule__field" for="sfb_start_date">
        <span><?php echo sfb_esc_html('Fecha de inicio'); ?></span>
        <input type="date" id="sfb_start_date" name="sfb_start_date" value="<?php echo esc_attr($schedule['start_date']); ?>">
      </label>
      <label class="sfb-schedule__field" for="sfb_end_date">
        <span><?php echo sfb_esc_html('Fecha de fin'); ?></span>
        <input type="date" id="sfb_end_date" name="sfb_end_date" value="<?php echo esc_attr($schedule['end_date']); ?>">
      </label>
    </fieldset>

    <h4><?php echo sfb_esc_html('Páginas donde está activo un banner flotante'); ?></h4>
    <table class="widefat striped sfb-active-table">
      <thead>
        <tr>
          <th scope="col"><?php echo sfb_esc_html('Banner flotante'); ?></th>
          <th scope="col"><?php echo sfb_esc_html('Tipo de pieza'); ?></th>
          <th scope="col"><?php echo sfb_esc_html('Páginas'); ?></th>
        </tr>
      </thead>
      <tbody>
        <?php if ($active_banner_rows) : ?>
          <?php foreach ($active_banner_rows as $active_banner_row) : ?>
            <tr>
              <td><?php echo esc_html($active_banner_row['title']); ?></td>
              <td><?php echo esc_html($active_banner_row['media_type']); ?></td>
              <td><?php echo esc_html(implode(', ', $active_banner_row['pages'])); ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else : ?>
          <tr>
            <td colspan="3"><?php echo sfb_esc_html('No hay banners flotantes activos con páginas asignadas.'); ?></td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>

    <h4><?php echo sfb_esc_html('Páginas'); ?></h4>
    <div class="sfb-page-selector">
      <label for="sfb_page_search" class="screen-reader-text"><?php echo sfb_esc_html('Buscar páginas'); ?></label>
      <input type="search" id="sfb_page_search" class="sfb-field__control sfb-page-selector__search" placeholder="<?php echo sfb_esc_attr('Buscar páginas...'); ?>">

      <div class="sfb-page-list" role="group" aria-label="<?php echo sfb_esc_attr('Páginas disponibles'); ?>">
        <?php
        $pages = get_pages();

        foreach ($pages as $page) {
          $page_id = absint($page->ID);
          $page_title = sfb_get_page_hierarchy_label($page_id);
          $page_title = $page_title ? $page_title : sprintf(sfb_translate('Página #%d'), $page_id);
          $disabled = in_array($page_id, $used_pages, true) ? 'disabled' : '';
          $checked = in_array($page_id, $selected_pages, true) ? 'checked' : '';

          echo "<label class='sfb-page-list__item' data-sfb-page-title='" . esc_attr(strtolower($page_title)) . "'>
                          <input type='checkbox' name='sfb_pages[]' value='" . esc_attr($page_id) . "' " . esc_attr($checked) . " " . esc_attr($disabled) . ">
                          <span>" . esc_html($page_title) . "</span>
                        </label>";
        }
        ?>
      </div>

      <p class="description sfb-page-selector__empty"><?php echo sfb_esc_html('No se encontraron páginas con ese criterio.'); ?></p>
    </div>
  </div>

<?php
}

/* =====================================================
   3. GUARDADO
===================================================== */
add_action('save_post', function ($post_id) {

  if (get_post_type($post_id) !== 'sfb_banner') return;

  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
  if (!current_user_can('edit_post', $post_id)) return;
  if (!isset($_POST['sfb_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['sfb_nonce'])), 'sfb_save_banner')) return;

  if (isset($_POST['sfb_media_type'])) {
    update_post_meta($post_id, '_sfb_media_type', sfb_sanitize_media_type(wp_unslash($_POST['sfb_media_type'])));
  }

  if (isset($_POST['sfb_image_id'])) {
    update_post_meta($post_id, '_sfb_image_id', absint(wp_unslash($_POST['sfb_image_id'])));
  }

  if (isset($_POST['sfb_video_id'])) {
    $video_id = absint(wp_unslash($_POST['sfb_video_id']));

    if (sfb_is_valid_video_attachment($video_id)) {
      update_post_meta($post_id, '_sfb_video_id', $video_id);
    } else {
      delete_post_meta($post_id, '_sfb_video_id');
    }
  }

  if (isset($_POST['sfb_cta_label'])) {
    update_post_meta($post_id, '_sfb_cta_label', sanitize_text_field(wp_unslash($_POST['sfb_cta_label'])));
  }

  if (isset($_POST['sfb_cta_id'])) {
    update_post_meta($post_id, '_sfb_cta_id', sanitize_html_class(wp_unslash($_POST['sfb_cta_id'])));
  }

  if (isset($_POST['sfb_link'])) {
    update_post_meta($post_id, '_sfb_link', sfb_sanitize_link_url(wp_unslash($_POST['sfb_link'])));
  }

  if (isset($_POST['sfb_target'])) {
    update_post_meta($post_id, '_sfb_target', sfb_sanitize_target(wp_unslash($_POST['sfb_target'])));
  }

  $start_date = isset($_POST['sfb_start_date']) ? sfb_sanitize_schedule_date(wp_unslash($_POST['sfb_start_date'])) : '';
  update_post_meta($post_id, '_sfb_start_date', $start_date);

  $end_date = isset($_POST['sfb_end_date']) ? sfb_sanitize_schedule_date(wp_unslash($_POST['sfb_end_date'])) : '';
  update_post_meta($post_id, '_sfb_end_date', $end_date);

  if (isset($_POST['sfb_pages'])) {
    $pages = array_map('absint', (array) wp_unslash($_POST['sfb_pages']));
    $valid_pages = array_filter($pages, function ($page_id) {
      return get_post_type($page_id) === 'page';
    });

    $used_pages = [];
    $banner_ids = get_posts([
      'post_type' => 'sfb_banner',
      'post_status' => 'publish',
      'post__not_in' => [absint($post_id)],
      'posts_per_page' => -1,
      'fields' => 'ids',
      'no_found_rows' => true
    ]);

    foreach ($banner_ids as $banner_id) {
      $schedule = sfb_get_publication_schedule($banner_id);

      if (!sfb_schedules_overlap($start_date, $end_date, $schedule['start_date'], $schedule['end_date'])) {
        continue;
      }

      $used_pages = array_merge($used_pages, array_map('absint', (array) get_post_meta($banner_id, '_sfb_pages', true)));
    }

    update_post_meta($post_id, '_sfb_pages', array_values(array_diff($valid_pages, array_unique($used_pages))));
  } else {
    delete_post_meta($post_id, '_sfb_pages');
  }
});

add_filter('wp_insert_post_data', function ($data, $postarr) {
  if (empty($data['post_type']) || $data['post_type'] !== 'sfb_banner') {
    return $data;
  }

  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return $data;
  }

  $post_id = !empty($postarr['ID']) ? absint($postarr['ID']) : 0;
  $media_type = $post_id ? sfb_sanitize_media_type(get_post_meta($post_id, '_sfb_media_type', true)) : 'image';
  $image_id = $post_id ? absint(get_post_meta($post_id, '_sfb_image_id', true)) : 0;
  $video_id = $post_id ? absint(get_post_meta($post_id, '_sfb_video_id', true)) : 0;
  $cta_label = $post_id ? sanitize_text_field(get_post_meta($post_id, '_sfb_cta_label', true)) : '';
  $link = $post_id ? sfb_sanitize_link_url(get_post_meta($post_id, '_sfb_link', true)) : '';

  if (isset($_POST['sfb_media_type'])) {
    $media_type = sfb_sanitize_media_type(wp_unslash($_POST['sfb_media_type']));
  }

  if (isset($_POST['sfb_image_id'])) {
    $image_id = absint(wp_unslash($_POST['sfb_image_id']));
  }

  if (isset($_POST['sfb_video_id'])) {
    $video_id = absint(wp_unslash($_POST['sfb_video_id']));
  }

  if (isset($_POST['sfb_cta_label'])) {
    $cta_label = sanitize_text_field(wp_unslash($_POST['sfb_cta_label']));
  }

  if (isset($_POST['sfb_link'])) {
    $link = sfb_sanitize_link_url(wp_unslash($_POST['sfb_link']));
  }

  $requires_publication = in_array($data['post_status'], ['publish', 'future'], true);
  $missing_required_fields = $media_type === 'video'
    ? (!$video_id || !sfb_is_valid_video_attachment($video_id) || !$cta_label)
    : (!$image_id);

  if ($requires_publication && $missing_required_fields) {
    $data['post_status'] = 'draft';

    if (is_admin()) {
      set_transient('sfb_required_fields_notice_' . get_current_user_id(), 1, 60);
    }
  }

  return $data;
}, 10, 2);

add_action('admin_notices', function () {
  $notice_key = 'sfb_required_fields_notice_' . get_current_user_id();

  if (!get_transient($notice_key)) {
    return;
  }

  delete_transient($notice_key);
?>
  <div class="notice notice-error is-dismissible">
    <p><?php echo sfb_esc_html('El banner quedó como borrador porque faltan campos obligatorios para publicarlo.'); ?></p>
  </div>
  <?php
});

/* =====================================================
   4. FRONTEND RENDER + CLOSE (SIN PERSISTENCIA)
===================================================== */
add_action('wp_footer', function () {

  if (!is_page()) return;

  $banners = sfb_get_current_page_banners();

  $rendered_banner = false;

  foreach ($banners as $banner_id) {
    $media_type = sfb_sanitize_media_type(get_post_meta($banner_id, '_sfb_media_type', true));
    $image_id = absint(get_post_meta($banner_id, '_sfb_image_id', true));
    $video_id = absint(get_post_meta($banner_id, '_sfb_video_id', true));
    $cta_label = get_post_meta($banner_id, '_sfb_cta_label', true);
    $cta_id = sanitize_html_class(get_post_meta($banner_id, '_sfb_cta_id', true));
    $link_url = sfb_sanitize_link_url(get_post_meta($banner_id, '_sfb_link', true));
    $target = sfb_sanitize_target(get_post_meta($banner_id, '_sfb_target', true));
    $link_rel = $target === '_blank' ? 'noopener noreferrer' : '';

    $banner_id = absint($banner_id);
    $banner_title = get_the_title($banner_id);
    $banner_label = $banner_title ? $banner_title : __('Banner promocional', 'spec-floating-banner');

    if ($media_type === 'video') {
      if (!sfb_is_valid_video_attachment($video_id) || !$cta_label) continue;

      $video_url = wp_get_attachment_url($video_id);

      if (!$video_url) continue;

      $rendered_banner = true;
  ?>
      <aside id="sfb-banner-<?php echo esc_attr($banner_id); ?>" class="sfb-floating-banner sfb-floating-banner--video" role="complementary" aria-label="<?php echo esc_attr($banner_label); ?>">

        <div class="sfb-floating-banner__inner">

          <button type="button" class="sfb-floating-banner__close" data-sfb-banner-id="<?php echo esc_attr($banner_id); ?>" aria-label="<?php echo esc_attr__('Cerrar banner', 'spec-floating-banner'); ?>">&times;</button>

          <video class="sfb-floating-banner__video" controls playsinline preload="metadata">
            <source src="<?php echo esc_url($video_url); ?>" type="<?php echo esc_attr(get_post_mime_type($video_id)); ?>">
          </video>

          <?php if ($link_url) : ?>
          <a class="sfb-floating-banner__cta" href="<?php echo sfb_esc_link_url($link_url); ?>" target="<?php echo esc_attr($target); ?>" <?php echo $link_rel ? ' rel="' . esc_attr($link_rel) . '"' : ''; ?><?php echo $cta_id ? ' id="' . esc_attr($cta_id) . '"' : ''; ?>>
            <?php echo esc_html($cta_label); ?>
          </a>
          <?php endif; ?>

        </div>
      </aside>
    <?php
      continue;
    }

    if (!$image_id) continue;

    $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
    $image_alt = $image_alt ? $image_alt : get_the_title($image_id);
    $image_alt = $image_alt ? $image_alt : $banner_label;
    $image_markup = wp_get_attachment_image($image_id, 'medium', false, [
      'alt' => $image_alt,
      'class' => 'sfb-floating-banner__image',
      'loading' => 'lazy',
      'decoding' => 'async'
    ]);

    if (!$image_markup) continue;

    $rendered_banner = true;
    ?>
    <aside id="sfb-banner-<?php echo esc_attr($banner_id); ?>" class="sfb-floating-banner" role="complementary" aria-label="<?php echo esc_attr($banner_label); ?>">

      <div class="sfb-floating-banner__inner">

        <button type="button" class="sfb-floating-banner__close" data-sfb-banner-id="<?php echo esc_attr($banner_id); ?>" aria-label="<?php echo esc_attr__('Cerrar banner', 'spec-floating-banner'); ?>">&times;</button>

        <?php if ($link_url) : ?>
        <a href="<?php echo sfb_esc_link_url($link_url); ?>" target="<?php echo esc_attr($target); ?>" <?php echo $link_rel ? ' rel="' . esc_attr($link_rel) . '"' : ''; ?>>
          <?php echo wp_kses_post($image_markup); ?>
        </a>
        <?php else : ?>
          <?php echo wp_kses_post($image_markup); ?>
        <?php endif; ?>

      </div>
    </aside>
<?php
  }

  if (!$rendered_banner) return;
});
