# WebbyCMS 2.0

A lightweight PHP framework for building **simple, scalable and stable websites**.
Born in 2012 and modernized for PHP 8.1+, it keeps the friendly page-per-folder
model while fixing the old codebase's security, concurrency and robustness gaps.

## Live demo

A hosted example of this repository runs at
**[https://webbycms.brandon.my](https://webbycms.brandon.my)** — the same Bootstrap 5
sample site you get from this repo, served from Apache behind a Cloudflare tunnel.
See [HOSTING.md](HOSTING.md) for the full deployment steps.

## What's new in 2.0

- **Modern PHP core** — PSR-4 namespaced kernel (`src/WebbyCMS/`), PHP 8.1+, Composer
  autoloading, `.env` configuration.
- **Security fixes** — the forgeable `webbycms_login` cookie login and the `parse_str`
  global-injection were removed; CSRF tokens, session hardening and prepared statements
  were added.
- **Stability fixes** — double `session_start()`, the `mysql_error()` fatal and leaked
  error pages are gone; errors are logged via Monolog.
- **Performance** — anonymous page cache (filesystem) plus lazy sessions keep public
  pages cheap on one instance, with a path to Redis later.
- **Bootstrap 5.3** — upgraded from Bootstrap 3; jQuery dependency dropped.
- **Testing & CI** — PHPUnit suite, PHPStan level 5, lint script, GitHub Actions matrix.

## Features

- **Page-per-folder authoring** — drop a `pages/my-page/index.php` file in and it's live.
- **Clean routing** — `/page`, `/page/cate`, `/page/cate/action/id` and `.html` variants.
- **Hardened sessions** — strict mode, HttpOnly, `SameSite=Lax`, secure-cookie on HTTPS,
  lazily started so anonymous traffic stays cache-friendly.
- **Auth API** — `Password`, `AuthManager`, `Gate` and per-session CSRF tokens (no login pages
  are forced on you).
- **Optional PDO database** — prepared statements only, enabled via `.env`.
- **Anonymous page cache** — filesystem-backed, so public pages skip most PHP work.
- **Proper errors & logs** — PSR-3 logging (Monolog), safe 500 page, stack traces only in debug.
- **Tested** — PHPUnit suite + PHPStan level 5 + a lint script, run in CI.

## Requirements

- PHP >= 8.1 with `pdo` (and `pdo_sqlite`/`pdo_mysql` for database use)
- Apache + `mod_rewrite` (or the PHP built-in server for development)
- Composer

## Quick start

```bash
composer install
cp .env.example .env   # then edit APP_URL, APP_DEBUG, etc.
```

### With the PHP built-in server

```bash
php -S 0.0.0.0:8000 router.php
```

### With Apache

Point your vhost docroot at the project root and make sure `.htaccess` overrides
are allowed (`AllowOverride All`). `/page/cate/action/id` URLs are rewritten to
`index.php`.

### With Docker

```bash
docker build -t webbycms .
docker run -p 8080:80 webbycms
```

Open http://localhost:8080 — you should see the Bootstrap 5 sample page.

## Project structure

```
index.php                 front controller
router.php                dev router for `php -S`
src/WebbyCMS/             the framework kernel
  Bootstrap.php           wires everything together
  Config.php Request.php Response.php Session.php Flash.php Router.php View.php Logger.php
  Auth/                   Password, AuthManager, Csrf, Gate, UserProviderInterface
  Cache/                  CacheInterface, FilesystemCache
  Db/                     Database (PDO)
  Error/                  ErrorHandler
  helpers.php             global helpers (config, e, csrf_field, auth, ...)
pages/{page}/index.php    your pages (each folder is a URL)
pages/{page}/forms/*.php  POST handlers for that page
includes/                 shared HTML head/body/footer templates
controller/               LEGACY shims (deprecated, see UPGRADE.md)
assets/                   Bootstrap 5 CSS/JS + your custom files
storage/                  logs, cache, sessions (git-ignored)
tests/                    PHPUnit suite
```

## Authoring a page

Create `pages/hello/index.php`:

```php
<div class="container my-5">
    <h1>Hello <?= e(request()->input('name', 'world')) ?></h1>
    <?php if (gate()->allows('admin')): ?>
        <p>You are an admin.</p>
    <?php endif; ?>
</div>
```

Visit `/hello` and `/hello?name=Webby`.

### Forms

A POST with a hidden `form` field routes to `pages/{page}/forms/{form}.php`:

```php
<?php
// pages/home/forms/contact.php
$name = (string) $request->input('name', '');
if ($name === '') {
    flash()->error('Name is required.');
} else {
    flash()->success("Thanks, $name!");
}
return redirect('/');
```

Add the hidden field and CSRF token to your form:

```html
<form method="post">
    <input type="hidden" name="form" value="contact">
    <?= csrf_field() ?>
    <input type="text" name="name">
    <button>Send</button>
</form>
```

## Configuration

All configuration lives in `.env` — copy `.env.example` and adjust:

| Variable            | Purpose                                              |
|---------------------|------------------------------------------------------|
| `APP_ENV`           | `development` / `production`                          |
| `APP_DEBUG`         | show stack traces (dev only)                          |
| `APP_URL`           | base URL used by the `url()` helper                   |
| `DB_ENABLED`        | set `true` to use the PDO database layer             |
| `SESSION_NAME`      | session cookie name                                   |
| `PAGE_CACHE_ENABLED`| anonymous page cache on/off                          |
| `PAGE_CACHE_TTL`    | page cache lifetime in seconds                       |
| `CSRF_ENFORCE`      | require a valid `_token` on all POSTs (enable after migrating forms) |

## Concurrency & scale

- Anonymous GET responses are cached to `storage/cache` and served without
  touching the session or database.
- Sessions start only when the page actually needs them, so cached pages never
  create session files.
- The database layer uses prepared statements; the connection is opened lazily.
- To scale out later, point `SESSION_NAME`/session storage at a shared store
  (e.g. Redis) without changing page code.

## Tests

```bash
composer lint      # php -l over src, pages, includes, controller
composer analyse   # PHPStan level 5
composer test      # PHPUnit
```

## Migrating from WebbyCMS 1.x

See **[UPGRADE.md](UPGRADE.md)**.

## License

GPL-3.0-or-later
