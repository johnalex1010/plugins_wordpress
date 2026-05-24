<?php

/**
 * Plugin Name: SPEC Floating Banner
 * Description: Gestiona banners flotantes por página con imagen, enlace obligatorio, target configurable, cierre temporal y columnas administrativas de estado/asignación.
 * Version: 1.9
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
    'Seleccionar Imagen' => 'Select Image',
    'Seleccionar imagen' => 'Select image',
    'La imagen es obligatoria para publicar el banner.' => 'An image is required to publish the banner.',
    'Selecciona una imagen antes de guardar el banner.' => 'Select an image before saving the banner.',
    'Enlace del banner' => 'Banner link',
    'El enlace es obligatorio y debe ser una URL válida.' => 'The link is required and must be a valid URL.',
    'El enlace es obligatorio y debe ser una URL vÃ¡lida.' => 'The link is required and must be a valid URL.',
    'Target del enlace' => 'Link target',
    'Misma ventana (_self)' => 'Same window (_self)',
    'Nueva ventana (_blank)' => 'New window (_blank)',
    'Páginas donde está activo un banner flotante' => 'Pages where a floating banner is active',
    'PÃ¡ginas donde estÃ¡ activo un banner flotante' => 'Pages where a floating banner is active',
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
      $custom_columns['sfb_publication_status'] = __('Estado', 'spec-floating-banner');
      $custom_columns['sfb_target_pages'] = __('Páginas del banner', 'spec-floating-banner');
    }
  }

  return $custom_columns;
});

add_action('manage_sfb_banner_posts_custom_column', function ($column, $post_id) {
  if ($column === 'sfb_publication_status') {
    $is_published = get_post_status($post_id) === 'publish';
    $status_class = $is_published ? 'sfb-status-badge--published' : 'sfb-status-badge--unpublished';
    $status_label = $is_published ? __('Publicado', 'spec-floating-banner') : __('No publicado', 'spec-floating-banner');

    echo '<span class="sfb-status-badge ' . esc_attr($status_class) . '">' . esc_html($status_label) . '</span>';
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

      $page_title = get_the_title($page_id);
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

  return file_exists($file) ? (string) filemtime($file) : '1.9';
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
  ]);
});

function sfb_render_metabox($post)
{
  $image_id = absint(get_post_meta($post->ID, '_sfb_image_id', true));
  $link = get_post_meta($post->ID, '_sfb_link', true);
  $target = get_post_meta($post->ID, '_sfb_target', true);
  $target = in_array($target, ['_self', '_blank'], true) ? $target : '_blank';
  $selected_pages = array_map('absint', (array) get_post_meta($post->ID, '_sfb_pages', true));

  $image_url = $image_id ? wp_get_attachment_url($image_id) : '';

  $args = [
    'post_type' => 'sfb_banner',
    'post__not_in' => [$post->ID],
    'posts_per_page' => -1,
    'fields' => 'ids',
    'no_found_rows' => true
  ];

  $used_pages = [];

  foreach (get_posts($args) as $banner_id) {
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

      $active_page_title = get_the_title($active_page_id);
      $active_page_titles[] = $active_page_title ? $active_page_title : sprintf(__('Página #%d', 'spec-floating-banner'), $active_page_id);
    }

    if (!$active_page_titles) {
      continue;
    }

    $active_banner_rows[] = [
      'title' => get_the_title($active_banner_id),
      'pages' => $active_page_titles
    ];
  }

  wp_nonce_field('sfb_save_banner', 'sfb_nonce');
?>

  <div>
    <button type="button" class="button" id="sfb_upload_btn"><?php echo sfb_esc_html('Seleccionar Imagen'); ?></button>
    <input type="hidden" name="sfb_image_id" id="sfb_image_id" value="<?php echo esc_attr($image_id); ?>">
    <p class="description"><?php echo sfb_esc_html('La imagen es obligatoria para publicar el banner.'); ?></p>
    <div id="sfb_required_notice" class="notice notice-error sfb-required-notice">
      <p><?php echo sfb_esc_html('Selecciona una imagen antes de guardar el banner.'); ?></p>
    </div>

    <div class="sfb-preview">
      <img id="sfb_preview" class="sfb-preview__image" src="<?php echo esc_url($image_url); ?>" alt="">
    </div>

    <p class="sfb-field">
      <label for="sfb_link"><strong><?php echo sfb_esc_html('Enlace del banner'); ?></strong></label>
      <input type="url" name="sfb_link" id="sfb_link" class="sfb-field__control" placeholder="<?php echo sfb_esc_attr('https://...'); ?>" value="<?php echo esc_attr($link); ?>" required>
      <span class="description"><?php echo sfb_esc_html('El enlace es obligatorio y debe ser una URL válida.'); ?></span>
    </p>

    <p class="sfb-field">
      <label for="sfb_target"><strong><?php echo sfb_esc_html('Target del enlace'); ?></strong></label>
      <select name="sfb_target" id="sfb_target" class="sfb-field__select">
        <option value="_self" <?php selected($target, '_self'); ?>><?php echo sfb_esc_html('Misma ventana (_self)'); ?></option>
        <option value="_blank" <?php selected($target, '_blank'); ?>><?php echo sfb_esc_html('Nueva ventana (_blank)'); ?></option>
      </select>
    </p>

    <h4><?php echo sfb_esc_html('Páginas donde está activo un banner flotante'); ?></h4>
    <table class="widefat striped sfb-active-table">
      <thead>
        <tr>
          <th scope="col"><?php echo sfb_esc_html('Banner flotante'); ?></th>
          <th scope="col"><?php echo sfb_esc_html('Páginas'); ?></th>
        </tr>
      </thead>
      <tbody>
        <?php if ($active_banner_rows) : ?>
          <?php foreach ($active_banner_rows as $active_banner_row) : ?>
            <tr>
              <td><?php echo esc_html($active_banner_row['title']); ?></td>
              <td><?php echo esc_html(implode(', ', $active_banner_row['pages'])); ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else : ?>
          <tr>
            <td colspan="2"><?php echo sfb_esc_html('No hay banners flotantes activos con páginas asignadas.'); ?></td>
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
          $page_title = get_the_title($page_id);
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

  if (isset($_POST['sfb_image_id'])) {
    update_post_meta($post_id, '_sfb_image_id', absint(wp_unslash($_POST['sfb_image_id'])));
  }

  if (isset($_POST['sfb_link'])) {
    update_post_meta($post_id, '_sfb_link', esc_url_raw(wp_unslash($_POST['sfb_link'])));
  }

  if (isset($_POST['sfb_target'])) {
    $target = sanitize_key(wp_unslash($_POST['sfb_target']));
    $target = in_array($target, ['_self', '_blank'], true) ? $target : '_blank';
    update_post_meta($post_id, '_sfb_target', $target);
  }

  if (isset($_POST['sfb_pages'])) {
    $pages = array_map('absint', (array) wp_unslash($_POST['sfb_pages']));
    $valid_pages = array_filter($pages, function ($page_id) {
      return get_post_type($page_id) === 'page';
    });

    update_post_meta($post_id, '_sfb_pages', array_values($valid_pages));
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
  $image_id = $post_id ? absint(get_post_meta($post_id, '_sfb_image_id', true)) : 0;
  $link = $post_id ? esc_url_raw(get_post_meta($post_id, '_sfb_link', true)) : '';

  if (isset($_POST['sfb_image_id'])) {
    $image_id = absint(wp_unslash($_POST['sfb_image_id']));
  }

  if (isset($_POST['sfb_link'])) {
    $link = esc_url_raw(wp_unslash($_POST['sfb_link']));
  }

  $requires_publication = in_array($data['post_status'], ['publish', 'future'], true);

  if ($requires_publication && (!$image_id || !$link)) {
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
    <p><?php echo sfb_esc_html('El banner quedó como borrador porque la imagen y el enlace son obligatorios para publicarlo.'); ?></p>
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
    $image_id = absint(get_post_meta($banner_id, '_sfb_image_id', true));
    $link = get_post_meta($banner_id, '_sfb_link', true);
    $link_url = esc_url($link);
    $target = get_post_meta($banner_id, '_sfb_target', true);
    $target = in_array($target, ['_self', '_blank'], true) ? $target : '_blank';
    $link_rel = $target === '_blank' ? 'noopener noreferrer' : '';

    if (!$image_id || !$link_url) continue;

    $banner_id = absint($banner_id);
    $banner_title = get_the_title($banner_id);
    $banner_label = $banner_title ? $banner_title : __('Banner promocional', 'spec-floating-banner');
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

                    <a href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($target); ?>"<?php echo $link_rel ? ' rel="' . esc_attr($link_rel) . '"' : ''; ?>>
                        <?php echo wp_kses_post($image_markup); ?>
                    </a>

            </div>
        </aside>
<?php
  }

  if (!$rendered_banner) return;

});
