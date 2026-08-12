# Upgrading to WebbyCMS 2.0

WebbyCMS 2.0 is a major rewrite of the 1.x plumbing. The page-per-folder model
and the `/page/cate/action/id` URL scheme are unchanged, so most pages can be
carried over — but the way pages read input, talk to the database and manage
sessions/errors has changed.

A transitional compatibility layer (`controller/legacy.php`, loaded
automatically when rendering a page) restores the old `$page`, `$cate`,
`$form`, `$_SESSION["systemError"]`, `initWebbyCMS()`, `stopWeb()` globals and
helpers, so existing pages keep working immediately. That layer is deprecated
and will be removed in a future release.

## What changed

| 1.x                                                          | 2.0                                                                             |
|--------------------------------------------------------------|---------------------------------------------------------------------------------|
| `$_GET` / `$_POST` / globals injected via `parse_str`        | `request()->input('key', $default)`                                             |
| `$page`, `$cate`, `$action`, `$id`, `$sub` globals           | `request()->segment('page')` (globals kept for legacy)                          |
| `$_SESSION["systemError"]` / `"systemSucces"`                | `flash()->error(...)` / `flash()->success(...)` + `flash()->peek('error')`      |
| `setcookie("webbycms_login", $data)` "login"                 | **removed** — use `auth()->login($id, ['roles' => [...]])` with a `UserProviderInterface` |
| `mysqli_connect(...)` / raw queries (or no DB)               | optional `db()` (PDO) with prepared statements                                  |
| `mysql_error()`                                              | removed (was already broken on PHP 7+)                                         |
| `session_start()` at top of `index.php`                      | handled by `Session`, lazily and exactly once                                   |
| echo-based `customError()`                                   | logged to `storage/logs/app.log`, safe 500 page                                 |

## Migrating a page

### Before (1.x)

```php
<?php
// pages/hello/index.php
$name = isset($_GET['name']) ? $_GET['name'] : 'world';
?>
<h1>Hello <?php echo $name; ?></h1>
```

### After (2.0)

```php
<?php
$name = (string) request()->input('name', 'world');
?>
<h1>Hello <?= e($name) ?></h1>
```

### Forms

Form handlers are the same files (`pages/{page}/forms/{form}.php`), but now
they receive the request via `$request` and return a `Response`:

```php
<?php
// pages/home/forms/contact.php
$email = (string) $request->input('email', '');
if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
    flash()->error('A valid email is required.');
} else {
    // optionally: db()->insert('contacts', ['email' => $email]);
    flash()->success('Thanks, we will be in touch.');
}
return redirect('/');
```

Add the hidden `form` field and `<?= csrf_field() ?>` to every form.

### Authentication

There are no built-in login pages. Implement `WebbyCMS\Auth\UserProviderInterface`
against your user table, then:

```php
use WebbyCMS\Auth\AuthManager;

// after boot: $app->auth() is an AuthManager
if ($app->auth()->attempt(['email' => $email, 'password' => $password])) {
    // logged in; $app->auth()->id(), $app->auth()->claims(), $app->auth()->user()
} else {
    flash()->error('Invalid credentials.');
}
```

Password hashes should be created with `WebbyCMS\Auth\Password::hash()`.

### Enable CSRF enforcement

2.0 ships CSRF token generation out of the box (`<?= csrf_field() ?>` in forms)
but `CSRF_ENFORCE=false` by default so legacy forms keep working. Once every
form includes `csrf_field()`, set `CSRF_ENFORCE=true` in `.env`.

## Things that may affect you

- **Assets upgraded to Bootstrap 5.3** — Bootstrap 3 classes and jQuery are gone.
  Grid/card/navbar markup needs updating (the sample home page shows the new syntax).
- **`controller/` files are now shims** — don't rely on them; the front controller
  no longer includes them. `controller/index.php` (cookie login) is removed.
- **Time zone** — set it in `.env` if you need something other than the default.

## Steps

1. `composer install`
2. Copy `.env.example` to `.env` and review the values.
3. Run the sample site and verify your old pages still render (they use the shims).
4. Migrate pages to `request()` / `flash()` / `e()` / `db()` and remove reliance on globals.
5. Add `csrf_field()` to all forms, then set `CSRF_ENFORCE=true`.
6. Update Bootstrap 3 markup to Bootstrap 5.
7. Delete usage of the `controller/` shims and `initWebbyCMS()`/`stopWeb()`.
