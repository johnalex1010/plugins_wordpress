# Plugins WordPress - UNIMINUTO Virtual

Repositorio local de plugins personalizados para WordPress. Los plugins incluidos permiten administrar banners y modales promocionales desde el panel de WordPress, con segmentación por páginas y controles de seguridad básicos.

## Plugins incluidos

| Plugin | Carpeta | Versión | Propósito |
| --- | --- | --- | --- |
| SPEC Header Banner | `spec-header-banner` | 4.4 | Banners full width por página, ubicados bajo breadcrumbs si existen o bajo el header como fallback. |
| SPEC Floating Banner | `spec-floating-banner` | 1.9 | Banners flotantes por página, con imagen, enlace, target y cierre temporal. |
| SPEC Modal Pro | `spec-modal-checklist` | 3.3 | Modales promocionales por página y rol, con imagen clickeable, frecuencia y estado activo/inactivo. |

## SPEC Header Banner

Plugin para crear múltiples banners de ancho completo. Cada banner se asigna a páginas específicas y una página asignada a un banner publicado queda bloqueada para otros banners, evitando duplicidad visual.

Características principales:

- CPT privado `shb_banner`.
- Imagen obligatoria.
- Enlace opcional con soporte para URL completa o anclas como `#formulario_inscripcion`.
- Target `_self` o `_blank`.
- Checklist de páginas con buscador.
- Columnas administrativas de estado y páginas asignadas.
- Inserción bajo breadcrumbs si existen; si no, bajo el header.
- Soporte de idioma inglés para textos administrativos y controles visibles.

Documentación específica: [spec-header-banner/README.md](spec-header-banner/README.md)

## SPEC Floating Banner

Plugin para crear banners flotantes por página. Se muestra como un banner fijo en pantalla con botón de cierre temporal.

Características principales:

- CPT privado `sfb_banner`.
- Imagen y enlace obligatorios para publicar.
- Target `_self` o `_blank`.
- Checklist de páginas con buscador.
- Bloqueo de páginas usadas por otros banners publicados.
- Columnas administrativas de estado y páginas asignadas.
- Frontend semántico con `aside` y botón accesible.
- Soporte de idioma inglés para textos administrativos y controles visibles.

Documentación específica: [spec-floating-banner/README.md](spec-floating-banner/README.md)

## SPEC Modal Pro

Plugin para crear modales promocionales con segmentación por página y rol. El modal muestra una imagen clickeable y respeta frecuencia por sesión o persistencia de una hora.

Características principales:

- CPT privado `smp_modal`.
- Estado activo/inactivo.
- Segmentación por páginas y roles.
- Delay configurable.
- Frecuencia `session` o `persistent` de 1 hora.
- Imagen opcional clickeable mediante CTA URL.
- Checklist de páginas con buscador.
- Bloqueo de páginas usadas por otros modales publicados.
- Columnas administrativas de estado, activo y páginas asignadas.
- Soporte de idioma inglés para textos administrativos, controles visibles y cierre del modal.

Documentación específica: [spec-modal-checklist/README.md](spec-modal-checklist/README.md)

## Instalación

1. Copiar la carpeta del plugin requerido dentro de:

```text
wp-content/plugins/
```

2. En el administrador de WordPress, ir a:

```text
Plugins > Plugins instalados
```

3. Activar el plugin correspondiente:

- `SPEC Header Banner`
- `SPEC Floating Banner`
- `SPEC Modal Pro`

4. Configurar desde el menú creado por cada plugin en el administrador.

## Activación recomendada

Activar solo los plugins que se usarán en producción. Si dos plugins muestran piezas visuales sobre la misma página, revisar su prioridad visual para evitar saturación.

## Idioma inglés

Los tres plugins soportan inglés mediante internacionalización WordPress y archivos en `languages/`:

- `spec-header-banner`: text domain `spec-header-banner`.
- `spec-floating-banner`: text domain `spec-floating-banner`.
- `spec-modal-checklist`: text domain `spec-modal-pro`.

Además, cada plugin incluye un fallback interno para locales `en*`, de modo que los textos del administrador y los controles visibles se muestran en inglés aunque WordPress no cargue el archivo de traducción correspondiente.

Para validar:

1. Cambiar el idioma del sitio o del usuario administrador a `English (United States)`.
2. Abrir el CPT de cada plugin.
3. Confirmar que metaboxes, columnas administrativas, buscadores, avisos y controles visibles aparecen en inglés.
4. Volver a Español y confirmar que los textos originales se mantienen.

## Seguridad

Los plugins siguen prácticas WordPress:

- Bloqueo de acceso directo con `ABSPATH`.
- CPTs no públicos cuando aplica.
- Nonces en formularios/metaboxes.
- Validación de permisos con `current_user_can()`.
- Sanitización de entradas con funciones WordPress.
- Escape de salida con `esc_html()`, `esc_attr()`, `esc_url()` y `wp_kses_post()`.
- `rel="noopener noreferrer"` para enlaces `_blank`.
- JS/CSS separados en assets, sin scripts inline.

## SEO / GEO / AEO

Los plugins están diseñados para no interferir con Yoast SEO:

- No modifican canonicales.
- No generan metadescripciones.
- No agregan schema.
- No crean URLs públicas indexables propias.
- Renderizan imágenes visibles y contenido controlado.

## Validación técnica

Ejecutar desde la carpeta de cada plugin:

```bash
php -l archivo-principal.php
node --check assets/js/admin.js
node --check assets/js/frontend.js
```

Notas:

- `SPEC Header Banner` puede no tener JS frontend si la versión futura vuelve a cambiar su estrategia de ubicación.
- Si PHP muestra `openssl already loaded`, corresponde a configuración local de PHP, no necesariamente al plugin.

## Estructura estándar

Cada plugin debe mantener una estructura similar:

```text
plugin-folder/
  plugin-main-file.php
  README.md
  assets/
    css/
      admin.css
      frontend.css
    js/
      admin.js
      frontend.js
  languages/
    plugin-domain.pot
    plugin-domain-en_US.po
    plugin-domain-en_US.mo
    plugin-domain-en_US.l10n.php
```

## Mantenimiento

Antes de modificar:

1. Definir SPEC proporcional al cambio.
2. Identificar si afecta seguridad, SEO, performance o accesibilidad.
3. No editar archivos generados.
4. Mantener cambios mínimos y reversibles.
5. Validar PHP/JS.
6. Actualizar versión y README si cambia comportamiento.

## Rollback

Para revertir un cambio:

1. Restaurar la carpeta del plugin afectado.
2. Limpiar cachés de WordPress/navegador si aplica.
3. Revisar que el plugin siga activo.
4. Validar que los CPTs/metaboxes o páginas de configuración carguen sin errores.

No hay migraciones destructivas documentadas. Los plugins usan opciones o post meta estándar de WordPress.
