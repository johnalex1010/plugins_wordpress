# AGENTS.md

Reglas locales para cualquier agente que trabaje en `spec-modal-checklist`.

Este archivo complementa el `AGENTS.md` raíz del repositorio. Si hay contradicción sobre archivos dentro de esta carpeta, gana esta regla local salvo que degrade seguridad, estabilidad o arquitectura global.

## Contexto del plugin

- Nombre: SPEC Modal Pro.
- Archivo principal: `spec-modal-checklist.php`.
- Text domain: `spec-modal-pro`.
- CPT privado: `smp_modal`.
- Versión documentada: 3.4.
- Requiere WordPress 6.0+ y PHP 7.4+.
- Render frontend: modal promocional segmentado por página y rol.

## Reglas específicas

- Mantener el prefijo `smp_` para funciones, metadatos y helpers propios.
- No cambiar el CPT `smp_modal` sin SPEC completa y plan de migración.
- No cambiar post meta existentes sin validar compatibilidad:
  - `_smp_enabled`
  - `_smp_delay`
  - `_smp_frequency`
  - `_smp_pages`
  - `_smp_cta_url`
  - `_smp_cta_target`
  - `_smp_image_id`
- Mantener estado activo/inactivo por modal.
- Mantener segmentación por páginas y roles.
- Mantener frecuencia `session` o `persistent` de 1 hora salvo SPEC contraria.
- Mantener imagen obligatoria para publicar cuando el comportamiento documentado lo requiera.
- Mantener target con allowlist `_self` y `_blank`.

## Código limpio local

- Mantener funciones `smp_` pequeñas y separadas por responsabilidad: registro, metaboxes, guardado, segmentación, frecuencia, consulta y render.
- Usar nombres explícitos para variables de estado, delay, frecuencia, páginas, roles, CTA, imagen y target.
- Mantener la lógica de frecuencia y segmentación legible, con estados vacíos y errores esperables tratados de forma explícita.
- Evitar duplicar validaciones de roles, páginas, target, frecuencia o URLs si existe un helper claro.
- Mantener JS del modal encapsulado, sin variables globales innecesarias, listeners duplicados ni manipulación insegura del DOM.
- Mantener CSS del modal acotado al namespace del plugin para evitar conflictos con el tema.
- Si se modifica control de foco, cierre o persistencia, documentar impacto accesible y validación manual.

## Seguridad y accesibilidad

- Validar capacidades antes de guardar.
- Mantener nonces en metaboxes.
- Sanitizar IDs, URL, target, delay, frecuencia, roles y páginas.
- Escapar toda salida.
- Mantener `rel="noopener noreferrer"` para `_blank`.
- Mantener `role="dialog"` y `aria-modal="true"` en frontend.
- Mantener cierre accesible y control de foco cuando se modifique el modal.

## Traducciones

- Si cambia texto visible, revisar `languages/`.
- No editar manualmente `.mo`.
- Mantener UTF-8 y text domain `spec-modal-pro`.
- Validar español e inglés cuando cambie copy.
- Mantener fallback interno para locales `en*` si se modifica traducción PHP.

## Validación local

Después de cambios relevantes ejecutar, según aplique:

```bash
php -l spec-modal-checklist.php
node --check assets/js/admin.js
node --check assets/js/frontend.js
```

Validar manualmente en WordPress si cambia UI o render:

- creación y edición de modales
- selección de imagen
- URL y target de CTA
- asignación de páginas y roles
- jerarquía padre/hija en el selector de páginas
- columnas administrativas
- render frontend
- cierre y frecuencia
- responsive mobile, tablet y desktop
- ausencia de errores en consola

## Rollback local

Restaurar los archivos modificados dentro de `spec-modal-checklist/`, limpiar cachés si aplica y validar que el CPT `smp_modal` cargue en el administrador.
