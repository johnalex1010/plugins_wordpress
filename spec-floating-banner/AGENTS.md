# AGENTS.md

Reglas locales para cualquier agente que trabaje en `spec-floating-banner`.

Este archivo complementa el `AGENTS.md` raíz del repositorio. Si hay contradicción sobre archivos dentro de esta carpeta, gana esta regla local salvo que degrade seguridad, estabilidad o arquitectura global.

## Contexto del plugin

- Nombre: SPEC Floating Banner.
- Archivo principal: `spec-floating-banner.php`.
- Text domain: `spec-floating-banner`.
- CPT privado: `sfb_banner`.
- Versión documentada: 1.9.
- Requiere WordPress 6.0+ y PHP 7.4+.
- Render frontend: banner flotante por página con cierre temporal.

## Reglas específicas

- Mantener el prefijo `sfb_` para funciones, metadatos y helpers propios.
- No cambiar el CPT `sfb_banner` sin SPEC completa y plan de migración.
- No cambiar post meta existentes sin validar compatibilidad:
  - `_sfb_image_id`
  - `_sfb_link`
  - `_sfb_target`
  - `_sfb_pages`
- Mantener imagen y enlace obligatorios para publicar.
- Mantener target con allowlist `_self` y `_blank`.
- Mantener bloqueo de páginas asignadas a banners publicados.
- Mantener assets frontend cargados solo cuando exista banner aplicable.
- No convertir el cierre temporal en persistencia permanente sin SPEC.

## Código limpio local

- Mantener funciones `sfb_` pequeñas y orientadas a una sola responsabilidad: registro del CPT, metaboxes, guardado, validación, consultas y render frontend.
- Usar nombres explícitos para variables relacionadas con imagen, enlace, target, páginas asignadas y banners publicados.
- Evitar duplicar validaciones de URL, target y páginas si ya existe un helper claro.
- No compactar lógica de render o guardado en expresiones difíciles de leer.
- Mantener JS del admin y frontend encapsulado, sin variables globales innecesarias ni listeners duplicados.
- Mantener CSS organizado por contexto: admin y frontend separados, sin reglas huérfanas.
- Si se agrega comportamiento nuevo, documentar su criterio de mantenimiento en este README o en el README del plugin cuando aplique.

## Seguridad y accesibilidad

- Validar capacidades antes de guardar.
- Mantener nonces en metaboxes.
- Sanitizar IDs, URL, target y páginas.
- Escapar toda salida.
- Mantener `rel="noopener noreferrer"` para `_blank`.
- Mantener `aside`, `role="complementary"` y `aria-label` en frontend.
- Mantener botón de cierre accesible.

## Traducciones

- Si cambia texto visible, revisar `languages/`.
- No editar manualmente `.mo`.
- Mantener UTF-8 y text domain `spec-floating-banner`.
- Validar español e inglés cuando cambie copy.

## Validación local

Después de cambios relevantes ejecutar, según aplique:

```bash
php -l spec-floating-banner.php
node --check assets/js/admin.js
node --check assets/js/frontend.js
```

Validar manualmente en WordPress si cambia UI o render:

- creación y edición de banners
- bloqueo de publicación sin imagen o enlace
- render en página asignada
- cierre temporal
- targets `_self` y `_blank`
- responsive mobile, tablet y desktop
- ausencia de errores en consola

## Rollback local

Restaurar los archivos modificados dentro de `spec-floating-banner/`, limpiar cachés si aplica y validar que el CPT `sfb_banner` cargue en el administrador.
