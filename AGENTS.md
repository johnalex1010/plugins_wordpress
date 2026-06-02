# AGENTS.md

Gobernanza obligatoria para cualquier agente de IA, asistente de desarrollo, entorno automatizado o sistema de generación de código que trabaje sobre este repositorio de plugins WordPress para UNIMINUTO Virtual.

Este archivo es la autoridad principal del proyecto. Los `AGENTS.md` dentro de cada plugin agregan contexto local y prevalecen únicamente para archivos de esa carpeta.

## 0. Gobernanza del proyecto

### Jerarquía documental

El agente debe respetar este orden:

1. `AGENTS.md` local de la carpeta donde se trabaja.
2. Este `AGENTS.md` raíz.
3. `README.md` raíz y `README.md` del plugin afectado.
4. Código fuente real y configuración existente.
5. Reglas globales de Codex como fallback.

Ante contradicción, gana la regla más cercana al archivo modificado, siempre que no contradiga seguridad, estabilidad o arquitectura del proyecto.

### Alcance del repositorio

Este repositorio contiene plugins personalizados para WordPress:

- `spec-header-banner`: banners full width bajo breadcrumbs o header.
- `spec-floating-banner`: banners flotantes por página.
- `spec-modal-checklist`: modales promocionales por página y rol.

Cada plugin debe poder copiarse de forma independiente a `wp-content/plugins/`.

## 1. Principios globales

Todo cambio debe proteger, en este orden:

1. seguridad
2. estabilidad
3. arquitectura existente
4. SEO / GEO / AEO
5. performance
6. accesibilidad
7. mantenibilidad
8. consistencia visual y funcional

El agente debe actuar con criterio conservador, hacer cambios mínimos y reversibles, validar antes de finalizar y explicar cualquier incertidumbre relevante.

## 2. Reglas absolutas

- No escribir código sin comprender el requerimiento.
- No implementar funcionalidad sin SPEC previa proporcional al cambio.
- No asumir lógica de negocio no documentada.
- No inventar endpoints, APIs, hooks, servicios, tablas, modelos, schemas ni contratos.
- No introducir dependencias nuevas sin justificación técnica y actualización documental.
- No modificar manualmente archivos generados, minificados, compilados o binarios.
- No degradar seguridad, SEO, GEO, AEO, accesibilidad, responsive, performance ni estabilidad.
- No eliminar código sin validar impacto.
- No hacer refactors masivos para cambios puntuales.
- No dejar `console.log`, debugging, logs temporales, código muerto, mocks accidentales ni comentarios basura.
- No hardcodear secretos, tokens, credenciales ni rutas sensibles.
- No asumir estructura del proyecto sin evidencia real.

## 2.1 Código limpio, mantenible y escalable

Todo cambio debe dejar el código más fácil de entender o, como mínimo, igual de claro que antes. Se prioriza código limpio sobre soluciones rápidas difíciles de mantener.

- Usar nombres descriptivos para funciones, variables, clases, metadatos y helpers.
- Mantener funciones pequeñas, enfocadas en una sola responsabilidad y con entradas/salidas comprensibles.
- Evitar abreviaturas crípticas, nombres genéricos (`data`, `temp`, `x`, `ct`) y lógica compactada que dificulte depuración.
- Preferir claridad explícita sobre trucos sintácticos, anidamientos profundos o expresiones demasiado densas.
- Reutilizar helpers existentes cuando reduzcan duplicación real y mantengan bajo acoplamiento.
- Extraer helpers nuevos solo si mejoran legibilidad, pruebas, reutilización o separación de responsabilidades.
- Mantener una estructura de carpetas lógica y coherente con cada plugin.
- Manejar errores y estados vacíos de forma clara, específica y segura.
- Escribir comentarios únicamente cuando aporten contexto útil que el código no puede expresar por sí solo.
- Evitar código muerto, duplicado, temporal o difícil de rastrear.
- Mantener cada plugin escalable sin introducir dependencias innecesarias entre plugins.
- Validar que los cambios sigan siendo legibles para otra persona que deba mantenerlos en el futuro.

La regla práctica es: si un cambio funciona pero queda difícil de leer, mantener o extender, no cumple Definition of Done.

## 3. Idioma, encoding y contenido

- El idioma oficial del proyecto es Español Colombia (`es-CO`), salvo textos de internacionalización o traducciones documentadas.
- Todo archivo textual debe mantenerse en UTF-8.
- No romper acentos, caracteres especiales ni archivos de traducción.
- No introducir mojibake.
- No mezclar saltos de línea de forma innecesaria.
- Todo copy debe cuidar ortografía, gramática, puntuación, tono profesional y terminología técnica consistente.
- Evitar Spanglish innecesario y traducciones literales incorrectas.

## 4. Detección tecnológica obligatoria

Antes de modificar código, el agente debe identificar con evidencia:

- framework o plataforma
- runtime
- package manager, si existe
- sistema de build, si existe
- arquitectura y estructura modular
- linters, formatters y test runners disponibles
- convenciones internas
- estrategia de rendering
- archivos fuente frente a archivos generados

Para este repositorio, la base detectada es:

- Plataforma: WordPress.
- Runtime principal: PHP 7.4+ según cabeceras de plugins.
- Frontend: JS y CSS vanilla en `assets/`.
- Build: no hay build automatizado documentado.
- Package manager: no hay `package.json` ni `composer.json` en la raíz.
- Validación recomendada: `php -l` y `node --check` sobre archivos modificados.

## 5. Flujo obligatorio SDD

Todo desarrollo debe seguir Spec-Driven Development:

1. Comprender requerimiento.
2. Identificar impacto técnico.
3. Definir SPEC proporcional.
4. Validar riesgos.
5. Definir Acceptance Criteria verificables.
6. Validar impacto SEO / GEO / AEO.
7. Solicitar confirmación si existe ambigüedad material.
8. Implementar cambios.
9. Validar resultado.
10. Confirmar Definition of Done.

Si no existe SPEC clara, no se debe escribir código funcional.

## 6. SPEC mínima obligatoria

La SPEC debe cubrir, de forma proporcional:

- Contexto: problema actual, necesidad y objetivo.
- Objetivo funcional: qué debe ocurrir, qué debe mostrarse y qué se mantiene igual.
- Alcance: qué incluye, qué no incluye y archivos potencialmente afectados.
- Impacto técnico: frontend, backend, assets, base de datos, performance, SEO, accesibilidad y seguridad.
- Riesgos: regresión, compatibilidad, responsive, integraciones y datos.
- Acceptance Criteria: criterios respondibles con sí/no.
- Validación: comandos, revisión manual, responsive, errores JS/backend, UTF-8, ortografía y SEO.
- Rollback: archivos afectados, pasos de reversión y riesgos posteriores.

## 7. Clasificación de cambios

### Cambio trivial

Ejemplos: typos, copy, documentación, ajustes visuales menores.

Requiere SPEC breve y validación puntual.

### Cambio funcional

Ejemplos: nuevas secciones, cambios UI, cambios lógicos, nuevos flujos de administración o frontend.

Requiere SPEC completa, validación técnica, validación responsive y rollback.

### Cambio crítico

Ejemplos: autenticación, permisos, seguridad, infraestructura, build, base de datos o migraciones.

Requiere SPEC completa, revisión profunda, validación obligatoria, rollback explícito y confirmación si existe incertidumbre.

## 8. Arquitectura y WordPress

- Mantener separación de responsabilidades entre PHP, CSS, JS, assets, traducciones y documentación.
- Mantener nombres, prefijos y text domains existentes.
- Respetar CPTs, post meta, opciones y hooks ya documentados.
- No crear CPTs, taxonomías, opciones, tablas ni hooks nuevos sin SPEC.
- No cambiar slugs, capacidades, permisos, text domains ni nombres de metadatos sin evaluar migración y compatibilidad.
- Evitar side effects globales.
- Evitar acoplamiento innecesario entre plugins.
- Cada plugin debe poder activarse y desactivarse de forma independiente.

## 9. Seguridad

Toda entrada debe considerarse no confiable.

- Validar permisos con capacidades WordPress adecuadas.
- Usar nonces en formularios y metaboxes.
- Sanitizar entradas con funciones WordPress acordes al dato.
- Escapar salidas según contexto: HTML, atributos, URL o JS.
- Validar IDs, tipos, rangos, targets, URLs y listas permitidas.
- No confiar en validaciones del frontend.
- Evitar `innerHTML` inseguro.
- Mantener `ABSPATH` como bloqueo de acceso directo.
- No exponer secretos ni datos sensibles.
- Mantener `rel="noopener noreferrer"` para enlaces `_blank`.

## 10. SEO / GEO / AEO

- No crear URLs públicas indexables sin SPEC.
- No modificar canonicales, metadata, schema ni configuraciones de Yoast/SEO sin SPEC explícita.
- Mantener HTML semántico y jerarquía clara.
- Evitar contenido duplicado o thin content.
- Mantener entidades, contexto y textos visibles claros.
- Cuidar LCP, CLS e INP.
- Definir dimensiones o estrategias estables para imágenes cuando aplique.
- Mantener `alt` útil en imágenes.

## 11. Frontend

### HTML

- Usar HTML semántico.
- Mantener landmarks, roles y atributos ARIA existentes.
- Mantener jerarquía accesible.

### CSS

- Evitar CSS inline salvo necesidad justificada.
- Evitar `!important` salvo caso documentado.
- Mantener responsive en mobile, tablet y desktop.
- Evitar overflow horizontal y cambios que generen CLS.

### JS

- Evitar contaminación global.
- Validar existencia de elementos antes de usarlos.
- Manejar estados vacíos y errores esperables.
- Evitar listeners duplicados o globales innecesarios.
- No dejar debugging.

## 12. Archivos generados y traducciones

- No modificar manualmente bundles, minificados, builds automáticos ni binarios.
- Los `.mo` son archivos binarios de traducción: no editarlos manualmente.
- Los `.po`, `.pot` y `.l10n.php` deben tratarse con especial cuidado por encoding y text domain.
- Si cambia copy visible, validar si requiere actualización de traducciones.

## 13. README obligatorio

El `README.md` raíz y el `README.md` del plugin afectado deben actualizarse si el cambio modifica:

- arquitectura
- instalación
- comandos
- infraestructura
- dependencias
- variables de entorno
- CI/CD
- flujos operativos
- build
- testing
- comportamiento funcional documentado

Si el cambio no afecta esos puntos, reportar que README no requería actualización.

## 14. Validación obligatoria

Antes de finalizar, validar lo que aplique:

- `php -l archivo-principal.php`
- `node --check assets/js/admin.js`
- `node --check assets/js/frontend.js`
- revisión de responsive cuando el cambio toque frontend
- revisión de accesibilidad cuando el cambio toque UI
- ausencia de errores JS/backend
- UTF-8 y acentos correctos
- ortografía
- impacto SEO / GEO / AEO
- ausencia de logs, debugging, secretos y TODO críticos

Si una validación no puede ejecutarse, explicar el motivo.

## 15. Git y trabajo concurrente

- No revertir cambios ajenos sin autorización explícita.
- Revisar `git status` antes y después de cambios.
- Mantener edits acotados al requerimiento.
- No usar comandos destructivos como `git reset --hard` o restauraciones masivas sin aprobación.
- No mezclar refactors no solicitados con cambios funcionales.

## 16. Rollback

Todo cambio debe indicar cómo revertirse:

- archivos afectados
- pasos seguros de reversión
- validaciones posteriores
- riesgos remanentes

Para plugins WordPress, el rollback mínimo suele ser restaurar archivos modificados del plugin, limpiar cachés si aplica y validar carga del administrador/frontend.

## 17. Definition of Done

Un cambio solo está terminado cuando:

- existe SPEC proporcional
- Acceptance Criteria cumplidos
- validaciones ejecutadas o justificadas
- README actualizado o marcado como no aplicable
- responsive validado si aplica
- performance validada si aplica
- seguridad validada
- SEO no degradado
- encoding UTF-8 correcto
- ortografía revisada
- rollback definido
- no existe código temporal
- build o validaciones equivalentes funcionales

## 18. Formato de respuesta final

Al finalizar un cambio, responder:

1. Qué se cambió.
2. Archivos modificados.
3. Validación realizada.
4. Riesgos o pendientes.
5. Estado del README.
6. Confirmación del DoD.

## 19. Regla final

Ante conflicto entre velocidad y seguridad, gana seguridad. Ante conflicto entre rapidez y arquitectura, gana arquitectura. Ante conflicto entre comodidad y SPEC, gana SPEC. Ante duda razonable, detenerse, explicar la incertidumbre y solicitar confirmación.
