# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

PAVYPRE is a construction/infrastructure project management system for a paving company in Puebla, Mexico. It is a multi-page web application with role-based portals (Admin, Client, Employee) built with static HTML + Tailwind CSS + Vanilla JavaScript, and a lightweight PHP authentication backend.

**No build system is configured.** Tailwind CSS and Chart.js are loaded via CDN. To run locally, serve the files with any static HTTP server:

```bash
python -m http.server 8000
# or
npx http-server
```

## Architecture

### Role-Based Pages

| Page | Role | Purpose |
|------|------|---------|
| `Home.html` | Public | Landing page with company info and services |
| `Login.html` | Public | Authentication form → redirects to role portal |
| `Admin.html` | Admin | Main dashboard — CRUD for obras, empleados, clientes, proveedores + charts |
| `Cliente.html` | Cliente | Read-only view of obra status and payment records |
| `Colaborador.html` | Colaborador | Read-only view of work history and earnings |

### Authentication Flow

`Login.html` POSTs to `php/login.php`, which validates against hardcoded credentials and redirects to the appropriate portal. Mock credentials (all use password `123456`):

- `admin@pavypre.com` → Admin
- `juan.perez@pavypre.com`, `maria.lopez@pavypre.com` → Colaborador
- `obras.publicas@puebla.gob.mx`, `proyectos@disur.com.mx` → Cliente

### Data Layer (Admin.html)

All data is **in-memory JavaScript** — there is no persistence (no localStorage, no database). Data resets on page refresh. The CRUD forms have their `action` attributes commented out (pointing to `php/guardar_*.php` files that don't exist yet) — JavaScript intercepts all form submits via `preventDefault()`.

The render pipeline follows a consistent pattern per entity:

```
render[Entity]() → filtrar[Entity]() → render[Entity]ConDatos(lista)
```

Entities: `Obras`, `Empleados`, `Clientes`, `Proveedores`

Tab navigation is controlled by `switchTab('tab-name')` and `switchSubTab('sub-tab-name')`.

### Validation & UI Utilities (Admin.html)

```javascript
marcarError(inputId, errorMsgId)      // Show validation error on a field
limpiarError(inputId, errorMsgId)     // Clear validation error
limpiarErroresForm(fieldPairs)        // Batch-clear errors before re-validating
```

Currency and date formatting use `es-MX` locale (MXN peso, Spanish date names).

### Design System

`css/style.css` defines CSS custom properties for the full color palette (primary gold `#FFC107`, black backgrounds, gray scales) and typography. All pages reference this file plus Tailwind CSS via CDN. Do not hardcode hex values directly — use the CSS variables defined in `style.css`.

SVG icons are stored in `/img/` and either inlined or referenced directly.

## Database Integration Status

Backend PHP files for persisting data **do not yet exist**. When integrating a database, the entry points are:

- `php/guardar_obra.php`
- `php/guardar_empleado.php`
- `php/guardar_cliente.php`
- `php/guardar_proveedor.php`

The HTML comments in `Admin.html` document the intended POST payloads for each form.

## Language & Localization

All UI text is in **Spanish (es-MX)**. New UI strings should be in Spanish. Date formatting uses `toLocaleDateString('es-MX', ...)` and currency uses `Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' })`.
