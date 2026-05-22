# SPEC Header Banner

Plugin WordPress para gestionar múltiples banners full width y ubicarlos bajo breadcrumbs cuando existan o bajo el header como fallback.

## Descripción

SPEC Header Banner usa un Custom Post Type privado (`shb_banner`) para administrar varios banners independientes. Cada banner tiene imagen obligatoria, enlace opcional, target configurable y selección de páginas con buscador.

Para evitar conflictos visuales, una página asignada a un banner publicado queda bloqueada al configurar otros banners, siguiendo el mismo criterio usado en los plugins de modales y banners flotantes.

## Funcionalidades

- CPT privado `shb_banner`.
- Múltiples banners.
- Imagen obligatoria.
- Enlace opcional.
- Target `_self` o `_blank`.
- Checklist de páginas con buscador.
- Bloqueo de páginas ya usadas por otros banners publicados.
- Tabla informativa de banners activos y páginas asignadas.
- Columnas administrativas:
  - Estado.
  - Páginas del banner.
- Banner full width en frontend.
- Inserción inicial con `wp_body_open` y fallback en `wp_footer`.
- Reubicación frontend: primero bajo breadcrumbs propios, Yoast, Rank Math o Breadcrumb NavXT; si no existen, bajo el header.

## Seguridad

- Bloqueo de acceso directo con `ABSPATH`.
- CPT no público.
- Nonce en guardado.
- Validación de permisos con `current_user_can()`.
- Sanitización con `absint()`, `esc_url_raw()` y allowlist de target.
- Validación de páginas tipo `page`.
- Escape de salida con `esc_html()`, `esc_attr()`, `esc_url()` y `wp_kses_post()`.
- `rel="noopener noreferrer"` cuando el enlace abre en nueva ventana.
- Sin CSS/JS inline.

## SEO / GEO / AEO

- No crea URLs públicas ni páginas indexables propias.
- No modifica canonicales, metadata, schema ni Yoast SEO.
- Renderiza una imagen visible a ancho completo.
- Usa `wp_get_attachment_image()` para preservar atributos generados por WordPress.

## Estructura

```text
spec-header-banner/
  spec-header-banner.php
  README.md
  assets/
    css/
      admin.css
      frontend.css
    js/
      admin.js
      frontend.js
```

## Validación recomendada

```bash
php -l spec-header-banner.php
node --check assets/js/admin.js
node --check assets/js/frontend.js
```

Validar en WordPress:

- Crear dos banners.
- Asignar páginas al primero.
- Confirmar que esas páginas quedan bloqueadas en el segundo.
- Publicar un banner con imagen.
- Confirmar que aparece full width bajo breadcrumbs si existen.
- Confirmar que aparece bajo el header cuando no hay breadcrumbs.
- Probar target `_self` y `_blank`.

## Migración

El plugin intenta migrar una configuración antigua basada en opciones a un banner tipo `shb_banner` en borrador. Opciones antiguas reconocidas:

- `shb_pages`
- `shb_image_id`
- `shb_link`
- `shb_target`
- `banner_pages`
- `banner_image`
- `banner_link`
- `banner_target`

## Rollback

Restaurar `spec-header-banner.php`, `README.md` y los archivos dentro de `assets/`. El plugin usa post meta:

- `_shb_image_id`
- `_shb_link`
- `_shb_target`
- `_shb_pages`
