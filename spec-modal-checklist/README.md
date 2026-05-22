# SPEC Modal Pro

Plugin WordPress para gestionar modales promocionales segmentados por página y rol.

## Descripción

SPEC Modal Pro crea un Custom Post Type privado (`smp_modal`) para administrar modales con contenido editorial, imagen, CTA, frecuencia, roles y páginas asignadas. El plugin no crea URLs públicas propias y no modifica metadata SEO, canonicales, schema ni configuración de Yoast SEO.

## Funcionalidades

- CPT privado para modales.
- Estado activo/inactivo por modal.
- Segmentación por páginas.
- Segmentación por roles de usuario.
- Delay configurable.
- Frecuencia `session` o `persistent` de 1 hora.
- CTA con URL y target para enlazar la imagen del modal.
- Imagen opcional, clickeable cuando existe CTA.
- Columnas administrativas de estado, activo y páginas asignadas.
- Tabla informativa de páginas donde hay modales activos.
- Assets separados para admin y frontend.

## Seguridad

- Bloqueo de acceso directo con `ABSPATH`.
- Nonce en guardado de metabox.
- Validación de permisos con `current_user_can()`.
- Sanitización con `absint()`, `sanitize_key()`, `sanitize_text_field()` y `esc_url_raw()`.
- Allowlists para frecuencia, target y modo de imagen.
- Validación de páginas tipo `page`.
- Validación de roles contra roles existentes de WordPress.
- Escape de salida con `esc_html()`, `esc_attr()`, `esc_url()` y `wp_kses_post()`.
- `rel="noopener noreferrer"` cuando el CTA abre en nueva pestaña.

## SEO / GEO / AEO

- No genera CPT público ni URLs indexables propias.
- No duplica metadata, canonicales ni schema de Yoast.
- Usa HTML accesible para el modal con `role="dialog"` y `aria-modal="true"`.
- Las imágenes se renderizan mediante `wp_get_attachment_image()`.

## Estructura

```text
spec-modal-checklist/
  spec-modal-checklist.php
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
php -l spec-modal-checklist.php
node --check assets/js/admin.js
node --check assets/js/frontend.js
```

Validar en WordPress:

- Crear y editar modal.
- Seleccionar imagen.
- Guardar CTA y target.
- Asignar páginas y roles.
- Revisar columnas administrativas.
- Verificar render frontend, cierre y frecuencia.

## Rollback

Restaurar `spec-modal-checklist.php` y los archivos en `assets/`. No hay migraciones de base de datos; el plugin usa post meta estándar:

- `_smp_enabled`
- `_smp_delay`
- `_smp_frequency`
- `_smp_pages`
- `_smp_roles`
- `_smp_cta_url`
- `_smp_cta_target`
- `_smp_image_id`
