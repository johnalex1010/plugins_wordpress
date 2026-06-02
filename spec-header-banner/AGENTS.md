# AGENTS.md

Reglas locales para cualquier agente que trabaje en `spec-header-banner`.

Este archivo complementa el `AGENTS.md` raíz del repositorio. Si hay contradicción sobre archivos dentro de esta carpeta, gana esta regla local salvo que degrade seguridad, estabilidad o arquitectura global.

## Contexto del plugin

- Nombre: SPEC Header Banner.
- Archivo principal: `spec-header-banner.php`.
- Text domain: `spec-header-banner`.
- CPT privado: `shb_banner`.
- Versión documentada: 4.5.
- Requiere WordPress 6.0+ y PHP 7.4+.
- Render frontend: banner full width bajo breadcrumbs si existen o bajo el header como fallback.

## Reglas específicas

- Mantener el prefijo `shb_` para funciones, metadatos y helpers propios.
- No cambiar el CPT `shb_banner` sin SPEC completa y plan de migración.
- No cambiar post meta existentes sin validar compatibilidad:
  - `_shb_image_id`
  - `_shb_link`
  - `_shb_target`
  - `_shb_pages`
- No modificar la estrategia de ubicación bajo breadcrumbs/header sin validar Yoast, Rank Math, Breadcrumb NavXT y fallback.
- Mantener bloqueo de páginas asignadas a banners publicados.
- Mantener imagen obligatoria para publicar.
- Mantener enlace opcional con soporte para URL completa o anclas internas.
- Mantener target con allowlist `_self` y `_blank`.
- No degradar el comportamiento independiente del plugin.

## Código limpio local

- Mantener funciones `shb_` pequeñas y separadas por responsabilidad: registro, metaboxes, guardado, migración, consulta y render.
- Usar nombres explícitos para variables de imagen, enlace, target, páginas asignadas, breadcrumbs y fallback bajo header.
- Mantener la lógica de ubicación del banner legible y fácil de auditar, sin anidamientos innecesarios.
- Evitar duplicar validaciones de páginas, target o enlaces si existe un helper reutilizable.
- Mantener JS del admin y frontend encapsulado, sin variables globales innecesarias ni listeners duplicados.
- Mantener CSS separado por contexto y evitar reglas que generen efectos laterales sobre el tema.
- Si se modifica migración o compatibilidad con breadcrumbs, documentar el impacto y la validación realizada.

## Seguridad y accesibilidad

- Validar capacidades antes de guardar.
- Mantener nonces en metaboxes.
- Sanitizar IDs, URL, target y páginas.
- Escapar toda salida.
- Mantener `rel="noopener noreferrer"` para `_blank`.
- Mantener HTML semántico y evitar CLS por reubicación del banner.

## Traducciones

- Si cambia texto visible, revisar `languages/`.
- No editar manualmente `.mo`.
- Mantener UTF-8 y text domain `spec-header-banner`.
- Validar español e inglés cuando cambie copy.

## Validación local

Después de cambios relevantes ejecutar, según aplique:

```bash
php -l spec-header-banner.php
node --check assets/js/admin.js
node --check assets/js/frontend.js
```

Validar manualmente en WordPress si cambia UI o render:

- creación y edición de banners
- publicación con imagen obligatoria
- bloqueo de páginas ya usadas
- jerarquía padre/hija en el selector de páginas
- ubicación bajo breadcrumbs
- fallback bajo header
- targets `_self` y `_blank`
- ausencia de errores en consola

## Rollback local

Restaurar los archivos modificados dentro de `spec-header-banner/`, limpiar cachés si aplica y validar que el CPT `shb_banner` cargue en el administrador.
