<?php

/**
 * Plugin Name: SPEC Header Banner
 * Description: Gestiona múltiples banners full width por página y los ubica bajo breadcrumbs si existen o bajo el header como fallback, con imagen obligatoria, enlace opcional, target configurable y administración con buscador.
 * Version: 4.4
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: Ing John Fandiño - Webmaster
 * Author URI: https://virtual.uniminuto.edu/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: spec-header-banner
 * Domain Path: /languages
 */

defined('ABSPATH') || exit;

add_action('plugins_loaded', function () {
  load_plugin_textdomain('spec-header-banner', false, dirname(plugin_basename(__FILE__)) . '/languages');

  $locale = determine_locale();
  $php_translation_file = plugin_dir_path(__FILE__) . 'languages/spec-header-banner-' . $locale . '.l10n.php';

  if (is_readable($php_translation_file)) {
    load_textdomain('spec-header-banner', $php_translation_file);
  }
});

function shb_translate($text)
{
  $locale = function_exists('determine_locale') ? determine_locale() : get_locale();

  if (strpos($locale, 'en') !== 0) {
    return __($text, 'spec-header-banner');
  }

  $translations = [
    'Header Banners' => 'Header Banners',
    'Banner migrado' => 'Migrated banner',
    'Estado' => 'Status',
    'Páginas del banner' => 'Banner pages',
    'PÃ¡ginas del banner' => 'Banner pages',
    'Publicado' => 'Published',
    'No publicado' => 'Unpublished',
    'Sin páginas asignadas' => 'No assigned pages',
    'Sin pÃ¡ginas asignadas' => 'No assigned pages',
    'Página #%d' => 'Page #%d',
    'PÃ¡gina #%d' => 'Page #%d',
    'Seleccionar Imagen' => 'Select Image',
    'Usar Imagen' => 'Use Image',
    'Configuración del Banner' => 'Banner Settings',
    'ConfiguraciÃ³n del Banner' => 'Banner Settings',
    'La imagen es obligatoria para publicar el banner.' => 'An image is required to publish the banner.',
    'Selecciona una imagen antes de guardar el banner.' => 'Select an image before saving the banner.',
    'Enlace del banner' => 'Banner link',
    'https://... o #seccion' => 'https://... or #section',
    'Acepta URLs completas o anclas internas como #formulario_inscripcion.' => 'Accepts full URLs or internal anchors such as #formulario_inscripcion.',
    'Target del enlace' => 'Link target',
    'Misma ventana (_self)' => 'Same window (_self)',
    'Nueva ventana (_blank)' => 'New window (_blank)',
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
    'Páginas donde está activo un banner' => 'Pages where a banner is active',
    'PÃ¡ginas donde estÃ¡ activo un banner' => 'Pages where a banner is active',
    'Banner' => 'Banner',
    'No hay banners activos con páginas asignadas.' => 'There are no active banners with assigned pages.',
    'No hay banners activos con pÃ¡ginas asignadas.' => 'There are no active banners with assigned pages.',
    'Banner #%d' => 'Banner #%d',
    'El banner quedó como borrador porque la imagen es obligatoria para publicarlo.' => 'The banner was saved as a draft because an image is required to publish it.',
    'El banner quedÃ³ como borrador porque la imagen es obligatoria para publicarlo.' => 'The banner was saved as a draft because an image is required to publish it.',
  ];

  return isset($translations[$text]) ? $translations[$text] : __($text, 'spec-header-banner');
}

function shb_esc_html($text)
{
  return esc_html(shb_translate($text));
}

function shb_esc_attr($text)
{
  return esc_attr(shb_translate($text));
}

function shb_get_asset_url($path)
{
  return plugin_dir_url(__FILE__) . ltrim($path, '/');
}

function shb_get_asset_version($path)
{
  $file = plugin_dir_path(__FILE__) . ltrim($path, '/');

  return file_exists($file) ? (string) filemtime($file) : '4.4';
}

function shb_sanitize_link($link)
{
  $link = trim(sanitize_text_field($link));

  if ($link === '') {
    return '';
  }

  if (strpos($link, '#') === 0) {
    return preg_match('/^#[A-Za-z0-9_-]+$/', $link) ? $link : '';
  }

  return esc_url_raw($link);
}

function shb_sanitize_target($target)
{
  $target = sanitize_key($target);

  return in_array($target, ['_self', '_blank'], true) ? $target : '_self';
}

function shb_sanitize_pages($pages)
{
  $pages = array_filter(array_map('absint', (array) $pages));

  return array_values(array_filter($pages, function ($page_id) {
    return get_post_type($page_id) === 'page';
  }));
}

/* =====================================================
   1. CUSTOM POST TYPE
===================================================== */
add_action('init', function () {
  register_post_type('shb_banner', [
    'label' => __('Header Banners', 'spec-header-banner'),
    'public' => false,
    'show_ui' => true,
    'menu_icon' => 'dashicons-format-image',
    'supports' => ['title']
  ]);
});

/* =====================================================
   2. MIGRATION FROM LEGACY OPTIONS
===================================================== */
add_action('admin_init', function () {
  if (get_option('shb_migrated_to_cpt')) {
    return;
  }

  $legacy_pages = get_option('shb_pages', get_option('banner_pages', []));
  $legacy_image_id = absint(get_option('shb_image_id', 0));
  $legacy_image_url = get_option('banner_image', '');
  $legacy_link = get_option('shb_link', get_option('banner_link', ''));
  $legacy_target = get_option('shb_target', get_option('banner_target', '_self'));

  if (!$legacy_image_id && $legacy_image_url) {
    $legacy_image_id = absint(attachment_url_to_postid($legacy_image_url));
  }

  $legacy_pages = shb_sanitize_pages($legacy_pages);

  if ($legacy_image_id || $legacy_pages || $legacy_link) {
    $banner_id = wp_insert_post([
      'post_type' => 'shb_banner',
      'post_status' => 'draft',
      'post_title' => __('Banner migrado', 'spec-header-banner')
    ], true);

    if (!is_wp_error($banner_id)) {
      update_post_meta($banner_id, '_shb_image_id', $legacy_image_id);
      update_post_meta($banner_id, '_shb_link', shb_sanitize_link($legacy_link));
      update_post_meta($banner_id, '_shb_target', shb_sanitize_target($legacy_target));
      update_post_meta($banner_id, '_shb_pages', $legacy_pages);
    }
  }

  update_option('shb_migrated_to_cpt', '1');
});

/* =====================================================
   3. ADMIN COLUMNS
===================================================== */
add_filter('manage_shb_banner_posts_columns', function ($columns) {
  $custom_columns = [];

  foreach ($columns as $key => $label) {
    $custom_columns[$key] = $label;

    if ($key === 'title') {
      $custom_columns['shb_publication_status'] = shb_translate('Estado');
      $custom_columns['shb_target_pages'] = shb_translate('Páginas del banner');
    }
  }

  return $custom_columns;
});

add_action('manage_shb_banner_posts_custom_column', function ($column, $post_id) {
  if ($column === 'shb_publication_status') {
    $is_published = get_post_status($post_id) === 'publish';
    $status_class = $is_published ? 'shb-status-badge--published' : 'shb-status-badge--unpublished';
    $status_label = $is_published ? shb_translate('Publicado') : shb_translate('No publicado');

    echo '<span class="shb-status-badge ' . esc_attr($status_class) . '">' . esc_html($status_label) . '</span>';
    return;
  }

  if ($column === 'shb_target_pages') {
    $page_ids = shb_sanitize_pages(get_post_meta($post_id, '_shb_pages', true));

    if (!$page_ids) {
      echo '<span class="shb-empty-column">' . shb_esc_html('Sin páginas asignadas') . '</span>';
      return;
    }

    $page_links = [];

    foreach ($page_ids as $page_id) {
      $page_title = get_the_title($page_id);
      $page_title = $page_title ? $page_title : sprintf(shb_translate('Página #%d'), $page_id);
      $edit_link = get_edit_post_link($page_id);
      $page_links[] = $edit_link
        ? '<a href="' . esc_url($edit_link) . '">' . esc_html($page_title) . '</a>'
        : esc_html($page_title);
    }

    echo wp_kses_post(implode(', ', $page_links));
  }
}, 10, 2);

/* =====================================================
   4. ASSETS
===================================================== */
add_action('admin_enqueue_scripts', function ($hook) {
  $screen = function_exists('get_current_screen') ? get_current_screen() : null;

  if (!$screen || $screen->post_type !== 'shb_banner') {
    return;
  }

  wp_enqueue_style(
    'shb-admin',
    shb_get_asset_url('assets/css/admin.css'),
    [],
    shb_get_asset_version('assets/css/admin.css')
  );

  if ($hook !== 'post.php' && $hook !== 'post-new.php') {
    return;
  }

  wp_enqueue_media();
  wp_enqueue_script(
    'shb-admin',
    shb_get_asset_url('assets/js/admin.js'),
    ['jquery', 'media-editor'],
    shb_get_asset_version('assets/js/admin.js'),
    true
  );

  wp_localize_script('shb-admin', 'SHB_ADMIN_I18N', [
    'mediaTitle' => shb_translate('Seleccionar Imagen'),
    'mediaButton' => shb_translate('Usar Imagen'),
  ]);
});

add_action('wp_enqueue_scripts', function () {
  if (!shb_get_current_page_banner_ids()) {
    return;
  }

  wp_enqueue_style(
    'shb-frontend',
    shb_get_asset_url('assets/css/frontend.css'),
    [],
    shb_get_asset_version('assets/css/frontend.css')
  );
  wp_enqueue_script(
    'shb-frontend',
    shb_get_asset_url('assets/js/frontend.js'),
    [],
    shb_get_asset_version('assets/js/frontend.js'),
    true
  );
});

/* =====================================================
   5. META BOX
===================================================== */
add_action('add_meta_boxes', function () {
  add_meta_box('shb_config', shb_translate('Configuración del Banner'), 'shb_render_metabox', 'shb_banner', 'normal', 'high');
});

function shb_get_used_pages_by_banners($exclude_banner_id)
{
  $banner_ids = get_posts([
    'post_type' => 'shb_banner',
    'post_status' => 'publish',
    'post__not_in' => [absint($exclude_banner_id)],
    'posts_per_page' => -1,
    'fields' => 'ids',
    'no_found_rows' => true
  ]);

  $used_pages = [];

  foreach ($banner_ids as $banner_id) {
    foreach (shb_sanitize_pages(get_post_meta($banner_id, '_shb_pages', true)) as $page_id) {
      $used_pages[$page_id] = true;
    }
  }

  return array_keys($used_pages);
}

function shb_get_active_banners_with_pages()
{
  $banner_ids = get_posts([
    'post_type' => 'shb_banner',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'fields' => 'ids',
    'no_found_rows' => true
  ]);

  $items = [];

  foreach ($banner_ids as $banner_id) {
    $pages = shb_sanitize_pages(get_post_meta($banner_id, '_shb_pages', true));

    if (!$pages) {
      continue;
    }

    $items[] = [
      'id' => absint($banner_id),
      'title' => get_the_title($banner_id),
      'pages' => $pages
    ];
  }

  return $items;
}

function shb_render_metabox($post)
{
  $image_id = absint(get_post_meta($post->ID, '_shb_image_id', true));
  $link = get_post_meta($post->ID, '_shb_link', true);
  $target = shb_sanitize_target(get_post_meta($post->ID, '_shb_target', true));
  $selected_pages = shb_sanitize_pages(get_post_meta($post->ID, '_shb_pages', true));
  $used_pages = shb_get_used_pages_by_banners($post->ID);
  $active_items = shb_get_active_banners_with_pages();

  wp_nonce_field('shb_save_banner', 'shb_nonce');
?>
  <div class="shb-admin">
    <button type="button" class="button" id="shb_upload_image_button"><?php echo shb_esc_html('Seleccionar Imagen'); ?></button>
    <input type="hidden" id="shb_image_id" name="shb_image_id" value="<?php echo esc_attr($image_id); ?>">
    <p class="description"><?php echo shb_esc_html('La imagen es obligatoria para publicar el banner.'); ?></p>
    <div id="shb_required_notice" class="notice notice-error shb-required-notice">
      <p><?php echo shb_esc_html('Selecciona una imagen antes de guardar el banner.'); ?></p>
    </div>
    <div id="shb_image_preview" class="shb-image-preview">
      <?php
      if ($image_id) {
        echo wp_kses_post(wp_get_attachment_image($image_id, 'large'));
      }
      ?>
    </div>

    <p class="shb-field">
      <label for="shb_link"><strong><?php echo shb_esc_html('Enlace del banner'); ?></strong></label>
      <input type="text" id="shb_link" name="shb_link" class="shb-field__control" value="<?php echo esc_attr($link); ?>" placeholder="<?php echo shb_esc_attr('https://... o #seccion'); ?>">
      <span class="description"><?php echo shb_esc_html('Acepta URLs completas o anclas internas como #formulario_inscripcion.'); ?></span>
    </p>

    <p class="shb-field">
      <label for="shb_target"><strong><?php echo shb_esc_html('Target del enlace'); ?></strong></label>
      <select name="shb_target" id="shb_target" class="shb-field__select">
        <option value="_self" <?php selected($target, '_self'); ?>><?php echo shb_esc_html('Misma ventana (_self)'); ?></option>
        <option value="_blank" <?php selected($target, '_blank'); ?>><?php echo shb_esc_html('Nueva ventana (_blank)'); ?></option>
      </select>
    </p>

    <h4><?php echo shb_esc_html('Páginas'); ?></h4>
    <div class="shb-page-selector">
      <label for="shb_page_search" class="screen-reader-text"><?php echo shb_esc_html('Buscar páginas'); ?></label>
      <input type="search" id="shb_page_search" class="shb-field__control shb-page-selector__search" placeholder="<?php echo shb_esc_attr('Buscar páginas...'); ?>">

      <div class="shb-page-selector__list" role="group" aria-label="<?php echo shb_esc_attr('Páginas disponibles'); ?>">
        <?php foreach (get_pages() as $page) : ?>
          <?php
          $page_id = absint($page->ID);
          $page_title = get_the_title($page_id);
          $page_title = $page_title ? $page_title : sprintf(shb_translate('Página #%d'), $page_id);
          $is_selected = in_array($page_id, $selected_pages, true);
          $is_used = in_array($page_id, $used_pages, true);
          ?>
          <label class="shb-page-selector__item" data-shb-page-title="<?php echo esc_attr(strtolower($page_title)); ?>">
            <input type="checkbox" name="shb_pages[]" value="<?php echo esc_attr($page_id); ?>" <?php checked($is_selected); ?> <?php disabled($is_used); ?>>
            <span><?php echo esc_html($page_title); ?></span>
          </label>
        <?php endforeach; ?>
      </div>

      <p class="description shb-page-selector__empty"><?php echo shb_esc_html('No se encontraron páginas con ese criterio.'); ?></p>
    </div>

    <h4><?php echo shb_esc_html('Páginas donde está activo un banner'); ?></h4>
    <table class="widefat striped shb-active-table">
      <thead>
        <tr>
          <th scope="col"><?php echo shb_esc_html('Banner'); ?></th>
          <th scope="col"><?php echo shb_esc_html('Páginas'); ?></th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$active_items) : ?>
          <tr>
            <td colspan="2"><?php echo shb_esc_html('No hay banners activos con páginas asignadas.'); ?></td>
          </tr>
        <?php else : ?>
          <?php foreach ($active_items as $item) : ?>
            <?php
            $banner_title = $item['title'] ? $item['title'] : sprintf(shb_translate('Banner #%d'), $item['id']);
            $page_titles = [];

            foreach ($item['pages'] as $page_id) {
              $page_title = get_the_title($page_id);
              $page_titles[] = $page_title ? $page_title : sprintf(shb_translate('Página #%d'), $page_id);
            }
            ?>
            <tr>
              <td><?php echo esc_html($banner_title); ?></td>
              <td><?php echo esc_html(implode(', ', $page_titles)); ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
<?php
}

/* =====================================================
   6. SAVE
===================================================== */
add_action('save_post', function ($post_id) {
  if (get_post_type($post_id) !== 'shb_banner') return;
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
  if (!current_user_can('edit_post', $post_id)) return;
  if (!isset($_POST['shb_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['shb_nonce'])), 'shb_save_banner')) return;

  $image_id = isset($_POST['shb_image_id']) ? absint(wp_unslash($_POST['shb_image_id'])) : 0;
  update_post_meta($post_id, '_shb_image_id', $image_id);

  $link = isset($_POST['shb_link']) ? shb_sanitize_link(wp_unslash($_POST['shb_link'])) : '';
  update_post_meta($post_id, '_shb_link', $link);

  $target = isset($_POST['shb_target']) ? shb_sanitize_target(wp_unslash($_POST['shb_target'])) : '_self';
  update_post_meta($post_id, '_shb_target', $target);

  $pages = isset($_POST['shb_pages']) ? (array) wp_unslash($_POST['shb_pages']) : [];
  $pages = shb_sanitize_pages($pages);
  $used_pages = shb_get_used_pages_by_banners($post_id);
  update_post_meta($post_id, '_shb_pages', array_values(array_diff($pages, $used_pages)));
});

add_filter('wp_insert_post_data', function ($data, $postarr) {
  if (empty($data['post_type']) || $data['post_type'] !== 'shb_banner') {
    return $data;
  }

  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return $data;
  }

  $post_id = !empty($postarr['ID']) ? absint($postarr['ID']) : 0;
  $image_id = $post_id ? absint(get_post_meta($post_id, '_shb_image_id', true)) : 0;

  if (isset($_POST['shb_image_id'])) {
    $image_id = absint(wp_unslash($_POST['shb_image_id']));
  }

  if (in_array($data['post_status'], ['publish', 'future'], true) && !$image_id) {
    $data['post_status'] = 'draft';

    if (is_admin()) {
      set_transient('shb_required_fields_notice_' . get_current_user_id(), 1, 60);
    }
  }

  return $data;
}, 10, 2);

add_action('admin_notices', function () {
  $notice_key = 'shb_required_fields_notice_' . get_current_user_id();

  if (!get_transient($notice_key)) {
    return;
  }

  delete_transient($notice_key);
?>
  <div class="notice notice-error is-dismissible">
    <p><?php echo shb_esc_html('El banner quedó como borrador porque la imagen es obligatoria para publicarlo.'); ?></p>
  </div>
<?php
});

/* =====================================================
   7. FRONTEND
===================================================== */
function shb_get_current_context_page_ids()
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

function shb_get_current_page_banner_ids()
{
  static $banner_ids = null;

  if ($banner_ids !== null) {
    return $banner_ids;
  }

  $banner_ids = [];
  $context_page_ids = shb_get_current_context_page_ids();

  if (!$context_page_ids) {
    return $banner_ids;
  }

  $candidate_ids = get_posts([
    'post_type' => 'shb_banner',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'fields' => 'ids',
    'no_found_rows' => true
  ]);

  foreach ($candidate_ids as $banner_id) {
    $pages = shb_sanitize_pages(get_post_meta($banner_id, '_shb_pages', true));

    if ($pages && array_intersect($pages, $context_page_ids)) {
      $banner_ids[] = absint($banner_id);
    }
  }

  return $banner_ids;
}

add_action('wp_body_open', 'shb_render_header_banners');
add_action('wp_footer', 'shb_render_header_banners');

function shb_render_header_banners()
{
  static $rendered = false;

  if ($rendered) {
    return;
  }

  $banner_ids = shb_get_current_page_banner_ids();

  if (!$banner_ids) {
    return;
  }

  $rendered = true;

  foreach ($banner_ids as $banner_id) {
    $image_id = absint(get_post_meta($banner_id, '_shb_image_id', true));

    if (!$image_id) {
      continue;
    }

    $image_html = wp_get_attachment_image($image_id, 'full', false, [
      'class' => 'shb-header-banner__image',
      'loading' => 'eager',
      'decoding' => 'async',
    ]);

    if (!$image_html) {
      continue;
    }

    $link = esc_url(get_post_meta($banner_id, '_shb_link', true));
    $target = shb_sanitize_target(get_post_meta($banner_id, '_shb_target', true));
    $rel = $target === '_blank' ? 'noopener noreferrer' : '';
?>
    <div id="shb-header-banner-<?php echo esc_attr($banner_id); ?>" class="shb-header-banner" data-shb-header-banner>
      <?php if ($link) : ?>
        <a href="<?php echo esc_url($link); ?>" target="<?php echo esc_attr($target); ?>"<?php echo $rel ? ' rel="' . esc_attr($rel) . '"' : ''; ?>>
          <?php echo wp_kses_post($image_html); ?>
        </a>
      <?php else : ?>
        <?php echo wp_kses_post($image_html); ?>
      <?php endif; ?>
    </div>
<?php
  }
}
