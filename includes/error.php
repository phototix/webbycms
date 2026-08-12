<?php

use WebbyCMS\Bootstrap;

$__flashApp = Bootstrap::instance();

/*
 * Only consult the session for visitors who already carry a session cookie.
 * Anonymous page views must stay session-free so the page cache works.
 */
$__flashEnabled = $__flashApp !== null && $__flashApp->request()->hasSessionCookie();
?>
<?php if ($__flashEnabled && $__flashApp->flash()->has('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= e($__flashApp->flash()->get('error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
<?php if ($__flashEnabled && $__flashApp->flash()->has('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= e($__flashApp->flash()->get('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
