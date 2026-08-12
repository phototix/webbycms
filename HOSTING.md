# Hosting WebbyCMS on a live website

This guide documents exactly how WebbyCMS 2.0 was deployed at
**https://webbycms.brandon.my**, so it can be repeated for any other domain.

## Architecture

```
Browser
   │  https://webbycms.brandon.my  (Cloudflare edge, TLS terminated there)
   ▼
Cloudflare edge ── CNAME ──> cloudflared tunnel "webbycms"
   │                       (systemd: cloudflared-webbycms.service)
   ▼
http://localhost:80   (Apache vhost: webbycms.brandon.my)
   │
   ▼
/var/www/webbycms.brandon.my  (the app; PHP via mod_php 8.3, .htaccess rewrites)
```

- The domain must be on **Cloudflare** (brandon.my is, nameservers `bjorn`/`lisa.ns.cloudflare.com`).
- The Cloudflare account certificate lives at `/root/.cloudflared/cert.pem` — it is a
  **secret**; never copy it into the repo or logs.

---

## 1. Prerequisites (on the server)

- PHP 8.1+ with `mod_php` loaded in Apache (`apache2ctl -M | grep php`)
- Apache with `mod_rewrite` and `AllowOverride All` support
- `cloudflared` binary installed
- The Cloudflare origin certificate (`~/.cloudflared/cert.pem`) for the account/zone

## 2. Deploy the code

Create the document root and copy the project (the example source is a git clone):

```bash
HOST=webbycms.brandon.my
mkdir -p /var/www/$HOST

rsync -a \
  --exclude='.git' \
  --exclude='vendor' \
  --exclude='.env' \
  --exclude='.phpunit.cache' \
  --exclude='.phpstan-cache' \
  --exclude='storage/cache/*' \
  --exclude='storage/logs/*' \
  /path/to/webbycms/ /var/www/$HOST/
```

## 3. Install production dependencies

```bash
cd /var/www/$HOST
composer install --no-dev --no-interaction --prefer-dist --no-progress
```

## 4. Create the production .env

```bash
cat > /var/www/$HOST/.env <<'EOF'
APP_ENV=production
APP_DEBUG=false
APP_NAME=WebbyCMS
APP_URL=https://webbycms.brandon.my

DB_ENABLED=false
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=
DB_USER=
DB_PASSWORD=

SESSION_NAME=webbycms_session
SESSION_LIFETIME=7200

PAGE_CACHE_ENABLED=true
PAGE_CACHE_TTL=60

CSRF_ENFORCE=true
EOF
```

## 5. Ownership & permissions

The app owner is the deploy user (`webbycms`), group `www-data` so Apache can read
everything and write to `storage/`:

```bash
cd /var/www/$HOST
chown -R webbycms:www-data .
find . -type d -exec chmod 755 {} +
find . -type f -exec chmod 644 {} +
chmod 775 storage storage/logs storage/cache storage/sessions
chmod 640 .env   # keep secrets out of other users' reach
```

## 6. Apache virtual host

Create `/etc/apache2/sites-available/$HOST.conf`:

```apache
<VirtualHost *:80>
    ServerName webbycms.brandon.my
    DocumentRoot /var/www/webbycms.brandon.my

    <Directory /var/www/webbycms.brandon.my>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
        DirectoryIndex index.php
    </Directory>

    # Never serve dotfiles (.env, .git, ...) over HTTP
    <FilesMatch "^\.">
        Require all denied
    </FilesMatch>

    ErrorLog ${APACHE_LOG_DIR}/webbycms.brandon.my-error.log
    CustomLog ${APACHE_LOG_DIR}/webbycms.brandon.my-access.log combined
</VirtualHost>
```

Enable and reload:

```bash
a2ensite webbycms.brandon.my
apache2ctl configtest
systemctl reload apache2
```

Local sanity check (before the tunnel exists):

```bash
curl -H "Host: webbycms.brandon.my" -i http://127.0.0.1/       # expect 200
curl -H "Host: webbycms.brandon.my" -i http://127.0.0.1/.env   # expect 403
curl -H "Host: webbycms.brandon.my" -i http://127.0.0.1/nope   # expect 404
```

## 7. Cloudflare tunnel

### 7.1 Create the tunnel (one per hostname, like the existing brandon.my sites)

```bash
cd /root                 # must be able to find /root/.cloudflared/cert.pem
cloudflared tunnel create webbycms
```

This prints a tunnel ID (e.g. `b5cdaac4-95ac-41a5-9581-fd7ac880701c`) and writes
credentials to `/root/.cloudflared/<tunnel-id>.json` (keep secret).

Copy the credentials to the shared config dir:

```bash
cp /root/.cloudflared/<tunnel-id>.json /etc/cloudflared/
chmod 600 /etc/cloudflared/<tunnel-id>.json
```

### 7.2 Tunnel config

Create `/etc/cloudflared/webbycms.brandon.my.yml`:

```yaml
tunnel: b5cdaac4-95ac-41a5-9581-fd7ac880701c
credentials-file: /etc/cloudflared/b5cdaac4-95ac-41a5-9581-fd7ac880701c.json

ingress:
  - hostname: webbycms.brandon.my
    service: http://localhost:80
  - service: http_status:404
```

### 7.3 Create the DNS CNAME

```bash
cd /root
cloudflared tunnel route dns webbycms webbycms.brandon.my
# -> Added CNAME webbycms.brandon.my which will route to this tunnel
```

### 7.4 systemd service (survives reboots)

Create `/etc/systemd/system/cloudflared-webbycms.service`:

```ini
[Unit]
Description=Cloudflared Tunnel for webbycms.brandon.my
After=network-online.target
Wants=network-online.target

[Service]
Type=simple
ExecStart=/usr/local/bin/cloudflared tunnel --config /etc/cloudflared/webbycms.brandon.my.yml run
Restart=on-failure
RestartSec=5s
LimitNOFILE=65536

[Install]
WantedBy=multi-user.target
```

```bash
systemctl daemon-reload
systemctl enable --now cloudflared-webbycms
systemctl is-active cloudflared-webbycms   # -> active
```

## 8. Verify the live site

```bash
# DNS should resolve to Cloudflare (104.21.x / 172.67.x)
host webbycms.brandon.my bjorn.ns.cloudflare.com

# Full HTTPS round trip through the tunnel
curl -i https://webbycms.brandon.my/          # 200, sample page
curl -i https://webbycms.brandon.my/.env      # 403 (blocked)
curl -i https://webbycms.brandon.my/nope      # 404
```

CSRF form round-trip (sessions work through the tunnel):

```bash
J=$(mktemp)
curl -c $J https://webbycms.brandon.my/ > /tmp/home.html
TOKEN=$(grep -oP 'name="_token" value="\K[^"]+' /tmp/home.html | head -1)
curl -b $J -c $J -i -X POST \
  -d "form=sample&send_something=Hi&_token=$TOKEN" \
  https://webbycms.brandon.my/
# expect 302 redirect, then the flash message on the next page load
```

---

## Operations

| Task                        | Command                                                                                       |
|-----------------------------|-----------------------------------------------------------------------------------------------|
| Tail the Apache error log   | `tail -f /var/log/apache2/webbycms.brandon.my-error.log`                                      |
| Tail the tunnel log         | `journalctl -u cloudflared-webbycms -f`                                                       |
| Restart the tunnel          | `systemctl restart cloudflared-webbycms`                                                      |
| Reload Apache               | `systemctl reload apache2`                                                                   |
| Clear the page cache        | `rm -f /var/www/webbycms.brandon.my/storage/cache/*.cache`                                   |
| Deploy new code             | re-run steps 2, 3, 5; `systemctl reload apache2`                                             |
| Check the app is cached     | `ls /var/www/webbycms.brandon.my/storage/cache/`                                              |

## Troubleshooting

- **NXDOMAIN but Cloudflare NS resolves**: local resolver has a stale negative cache; query
  Cloudflare nameservers directly or wait a minute.
- **Tunnel up but site 502/523**: confirm Apache is serving the hostname locally
  (`curl -H "Host: ..." http://127.0.0.1/`), then check `journalctl -u cloudflared-webbycms`.
- **Cache serving stale content**: delete `storage/cache/*.cache` or wait for the TTL.
- **Session cookie missing**: the tunnel passes `Set-Cookie` untouched; check that
  `APP_URL` matches the real domain and that you are not testing over `localhost`.

## Security notes

- `/root/.cloudflared/cert.pem` and every `/etc/cloudflared/*.json` credentials file are secrets.
- `.env`, `.git`, and all dotfiles are blocked by the Apache `<FilesMatch>` rules.
- Keep `APP_DEBUG=false` in production; errors go to `storage/logs/app.log`.
