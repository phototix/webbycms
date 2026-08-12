<?php
/** @var \WebbyCMS\Request $request */
?>
<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/">WebbyCMS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="/">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#how-hosted">How this site is hosted</a></li>
                    <li class="nav-item"><a class="nav-link" href="#hosting-guide">Hosting guide</a></li>
                    <li class="nav-item"><a class="nav-link" href="#form-demo">Form demo</a></li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<main class="container my-5">
    <div class="p-5 mb-4 bg-light rounded-3">
        <div class="container-fluid py-3">
            <h1 class="display-5 fw-bold">WebbyCMS 2.0</h1>
            <p class="col-md-8 fs-5">
                A lightweight PHP framework for simple, scalable and stable websites. <?= e(initWebbyCMS()) ?>
                This page is the live demo, hosted at <strong>https://webbycms.brandon.my</strong>.
            </p>
        </div>
    </div>

    <div class="row g-4" id="how-hosted">
        <div class="col-12">
            <h2 class="h3 mb-3">How this site is hosted</h2>
            <p class="text-muted">
                webbycms.brandon.my runs the exact code in this repository, served from a single
                instance behind Cloudflare. The full step-by-step guide lives in
                <a href="/HOSTING.md">HOSTING.md</a>.
            </p>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title h5">Architecture</h3>
                    <ol class="mb-0">
                        <li><strong>Browser</strong> loads <code>https://webbycms.brandon.my</code> — TLS is terminated at the Cloudflare edge.</li>
                        <li><strong>Cloudflare edge</strong> follows a <code>CNAME</code> to the <code>webbycms</code> Cloudflare tunnel.</li>
                        <li><strong>cloudflared</strong> (systemd unit <code>cloudflared-webbycms.service</code>) forwards to <code>http://localhost:80</code>.</li>
                        <li><strong>Apache</strong> vhost <code>webbycms.brandon.my</code> on port 80 matches the <code>Host</code> header.</li>
                        <li><strong>PHP 8.3 (mod_php)</strong> executes <code>index.php</code>; <code>.htaccess</code> rewrites friendly URLs to the front controller.</li>
                        <li>The app root is <code>/var/www/webbycms.brandon.my</code>; pages live in <code>pages/{page}/index.php</code>.</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h3 class="card-title h5">Edge &amp; DNS</h3>
                    <ul class="mb-0 small">
                        <li>Domain on Cloudflare (NS <code>bjorn</code>/<code>lisa.ns.cloudflare.com</code>)</li>
                        <li>Per-hostname tunnel <code>webbycms</code> → <code>localhost:80</code></li>
                        <li>Free TLS via Cloudflare; no local 443 needed</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h3 class="card-title h5">Web server</h3>
                    <ul class="mb-0 small">
                        <li>Apache vhost on <code>*:80</code>, <code>AllowOverride All</code></li>
                        <li>PHP 8.3 <code>mod_php</code> + <code>mod_rewrite</code></li>
                        <li><code>.env</code> and dotfiles denied (<code>FilesMatch</code> → 403)</li>
                        <li>Site logs in <code>/var/log/apache2/</code></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h3 class="card-title h5">Application</h3>
                    <ul class="mb-0 small">
                        <li>Production <code>.env</code>: <code>APP_DEBUG=false</code></li>
                        <li>Anonymous page cache → <code>storage/cache</code></li>
                        <li>Lazy sessions; CSRF enforced on POSTs</li>
                        <li>Errors logged to <code>storage/logs/app.log</code></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-12" id="hosting-guide">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title h5">Hosting guide</h3>
                    <p class="card-text">
                        Every step to reproduce this deployment — code deploy, composer install, <code>.env</code>,
                        permissions, the Apache vhost, the Cloudflare tunnel (<code>cloudflared tunnel create</code>,
                        config, <code>route dns</code>, systemd), verification commands, operations and
                        troubleshooting — is documented in <a href="/HOSTING.md">HOSTING.md</a>.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row my-5" id="form-demo">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title h5">Form post handling sample</h2>
                    <p class="text-muted">Submitting this form POSTs to <code>/pages/home/forms/sample.php</code> and redirects back here.</p>
                    <form method="post">
                        <input type="hidden" name="form" value="sample">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label for="send_something" class="form-label">Your message</label>
                            <input type="text" class="form-control" name="send_something" id="send_something" value="<?= e((string) $request->input('send_something', '')) ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
