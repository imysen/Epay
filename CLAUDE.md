# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project overview

Epay is a self-hosted PHP aggregate payment system (自用改版易支付). It is a traditional multi-page PHP application (no framework), backed by MySQL, and currently being customized into a single-admin mode.

Key facts:

- PHP >= 7.4 is required; PHP 8.x is fine.
- No Node.js/npm build pipeline. The frontend refactor uses plain CSS (`assets/css/ep-ui.css`) + Alpine.js (`assets/js/alpine.min.js`, fallback to CDN).
- Active branch: `customize-single-admin`.
- `admin/` was renamed to `adminlogin/`. The `user/` directory was removed. User-side functionality is disabled; the system is intended for a single administrator.
- `install/install.lock` must exist after installation, or the site refuses to run.
- README.md is very short; this file supersedes it for operational guidance.

## Common commands

### Local development server

Run from the repository root on Windows (adjust path separators on Linux/macOS):

```powershell
php -S 127.0.0.1:8080 -t E:/GithubDev/Epay
```

The app uses URL rewriting for `paypage/` and some payment flows; if those routes return 404 under the built-in server, use the included router file:

```powershell
php -S 127.0.0.1:8080 -t E:/GithubDev/Epay .claude/router.php
```

(`.claude/router.php` is a local-only helper; do not commit production routing changes.)

### PHP syntax lint

```bash
# single file
php -l adminlogin/onecode.php

# all recently changed PHP files
php -l index.php && php -l includes/common.php && for f in $(git diff --name-only -- '*.php'); do php -l "$f"; done
```

### Database install / upgrade

- Initial install: browse to `/install/` and follow the wizard, or import `install/install.sql` and `install/single_admin.sql` manually, then create `install/install.lock`.
- Upgrade: if `DB_VERSION` in `includes/common.php` is higher than the installed version, browse to `/install/update.php`.

### Clearing cache

Many settings are cached. After changing `pre_config` values directly in the database, delete the cache file (default `includes/cache/`) or call `saveSetting()` / `$CACHE->clear()` in code.

## High-level architecture

### Entry points and request lifecycle

```
index.php                 Public front controller; loads template from template/default/ via \lib\Template
adminlogin/*.php          Admin pages (login, order, set, onecode, ...)
includes/pages/*.php      Payment收银页 (qrcode/h5/wap/jspay)
paypage/                  码牌收款入口
plugins/                  Payment plugins; custom收银页 at plugins/<name>/inc/pay.page.php
includes/common.php       Bootstrap: constants, autoloader, DB, session, $conf cache, $cdnpublic
includes/functions.php    Global helpers (curl, order processing, etc.)
includes/member.php       User authentication stubs (disabled in single-admin mode)
includes/ep_ui.php        New design-system helpers: ep_icon(), ep_layout_head(), ep_pay_head(), Alpine components
assets/css/ep-ui.css      New design-system CSS (no build step)
design/DESIGN-SYSTEM.md   Visual/UX specification
```

Request flow:

1. `includes/common.php` defines `IN_CRONLITE`, `ROOT`, `PAYPAGE_ROOT`, etc., registers `Autoloader`, loads `config.php`, connects to MySQL via `\lib\PdoHelper`, and populates `$conf` from cache.
2. Guards in `common.php` block access to `user/` scripts (except `openid.php` / `douyinoauth.php`).
3. Public pages route through `index.php` → `\lib\Template::load($mod)` → `template/default/<mod>.php`.
4. Admin pages are direct PHP files under `adminlogin/`, typically including `head.php` (legacy Bootstrap 3) or calling `ep_layout_head()` (new UI).
5. Payment收银页 are included by plugins when `IN_PLUGIN` is defined; they receive variables such as `$order`, `$code_url`, `$sitename`, `$cdnpublic`.

### Important class responsibilities

- `\lib\PdoHelper` — PDO wrapper and query builder.
- `\lib\Cache` — file-based setting cache.
- `\lib\Order` — order creation and lifecycle.
- `\lib\Payment` — payment channel dispatch.
- `\lib\Channel` — channel configuration and selection.
- `\lib\Transfer` — transfer/payout logic.
- `\lib\Plugin` — plugin loader.
- `\lib\Template` — front-end template loader.
- `\lib\RiskCheck` — risk control hooks.
- `\lib\TOTP` — admin two-factor authentication.
- `\lib\ProfitSharing\*` — profit-sharing adapters for 13+ channels.

### Current branch special behavior

- `adminlogin/` is the admin path. Do not use `admin/`.
- Root path `/` redirects to `$conf['root_redirect_url']` when configured; otherwise returns 404.
- User registration and self-service merchant features are disabled; `reg_open`, `reg_pay`, `test_open` are forced to `0` in `adminlogin/ajax.php`.
- `adminlogin/onecode.php` + table `pre_onecode` implement pre-printed static QR code plates (`/paypage/?ucode=<code>`).

### Frontend conventions

- Legacy admin pages still load Bootstrap 3 + jQuery + layer + bootstrap-table via `adminlogin/head.php`.
- New/refactored pages use:
  - `assets/css/ep-ui.css` — design tokens, layout, components.
  - `includes/ep_ui.php` — `ep_icon()`, `ep_layout_head()`, `ep_pay_head()`, `ep_alpine()`.
  - Alpine.js components defined in `ep_alpine()`: `$fetch`, `epUI`, `poller`.
- Do not use emoji; use inline SVG only. Design spec: `design/DESIGN-SYSTEM.md`.
- Do not introduce npm/build tools unless the user explicitly asks; the project currently has no `package.json`.

### Adding a new admin page

1. Create `adminlogin/<name>.php`.
2. Include `../includes/common.php`.
3. Set `$title` and optionally `$activeNav` / `$crumbs`, then include `head.php` (legacy) or call `ep_layout_head()` (new UI).
4. Add the route in `ep_layout_head()` nav array if using the new UI, or in `adminlogin/head.php` if using legacy.
5. Run `php -l` on the new file.

### Adding a payment收银页

1. Create/edit `includes/pages/<channel>_<type>.php`.
2. Guard with `if(!defined('IN_PLUGIN'))exit();` and `define('IN_EPAY', true);`.
3. Include `includes/ep_ui.php` and call `ep_pay_head()` for the new UI.
4. The plugin that includes the page provides `$order`, `$code_url`, `$sitename`, `$cdnpublic`.
5. Use the `poller` Alpine component for the `getshop.php` payment-status loop (`code == 1` → `backurl`).

### Security notes

- `adminlogin/login.php` handles RSA-encrypted passwords (`enc=1`) and TOTP. The login flow has two steps: `?act=login` and `?act=totp`. Do not weaken or bypass this.
- `includes/common.php` regenerates a key pair for admin login if `$conf['public_key']` is missing.
- `$conf['syskey']` (`SYS_KEY`) is used as a signature key; treat it as sensitive.
- Payment pages are included with `IN_PLUGIN` defined and must guard against direct access.

### Things to watch for

- Many pages still mix old Bootstrap 3 markup with new `ep-*` classes; keep old and new UI separated—do not layer BS3 classes and `ep-*` classes on the same element.
- `paypage/` uses `paypage/index.php` as the entry; pathinfo routing may need a local router.
- The design system is still being rolled out; `design/admin.html` and `design/pay.html` are the browser-previewable prototypes.
