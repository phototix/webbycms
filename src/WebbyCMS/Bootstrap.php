<?php

declare(strict_types=1);

namespace WebbyCMS;

use WebbyCMS\Auth\AuthManager;
use WebbyCMS\Auth\Csrf;
use WebbyCMS\Auth\Gate;
use WebbyCMS\Cache\CacheInterface;
use WebbyCMS\Cache\FilesystemCache;
use WebbyCMS\Db\Database;
use WebbyCMS\Error\ErrorHandler;

/**
 * The WebbyCMS kernel: wires configuration, error handling, session, auth,
 * cache and routing together, and handles the request lifecycle.
 */
final class Bootstrap
{
    private static ?self $instance = null;

    private readonly Config $config;
    private readonly Request $request;
    private readonly Session $session;
    private readonly Flash $flash;
    private readonly AuthManager $auth;
    private readonly Csrf $csrf;
    private readonly Gate $gate;
    private readonly Router $router;
    private readonly View $view;
    private readonly CacheInterface $cache;
    private readonly Logger $logger;
    private readonly ErrorHandler $errorHandler;
    private readonly ?Database $db;

    private function __construct(private readonly string $root)
    {
        $this->config = Config::fromEnv($root);

        $sessionName = (string) $this->config->get('SESSION_NAME', 'PHPSESSID');
        $this->request = Request::capture($sessionName);
        $this->logger = new Logger($root . '/storage/logs', (string) $this->config->get('APP_NAME', 'webbycms'));
        $this->errorHandler = new ErrorHandler($this->logger->get(), $this->config->bool('APP_DEBUG', false));
        $this->errorHandler->register();

        $this->session = new Session($this->config, $sessionName, $this->request->isSecure());
        $this->flash = new Flash($this->session);
        $this->auth = new AuthManager($this->session);
        $this->csrf = new Csrf($this->session);
        $this->gate = new Gate($this->auth);

        $this->cache = new FilesystemCache($root . '/storage/cache');
        $this->db = $this->config->bool('DB_ENABLED', false) ? new Database($this->config) : null;

        $this->view = new View($root);
        $this->router = new Router($root);
    }

    public static function boot(string $root): self
    {
        return self::$instance ??= new self($root);
    }

    public static function instance(): self
    {
        if (self::$instance === null) {
            throw new \RuntimeException('WebbyCMS has not been booted. Call Bootstrap::boot() first.');
        }

        return self::$instance;
    }

    public static function booted(): bool
    {
        return self::$instance !== null;
    }

    /**
     * @internal test-only
     */
    public static function reset(): void
    {
        self::$instance = null;
    }

    public function handle(Request $request): Response
    {
        $cacheable = $request->isGet()
            && !$request->hasSessionCookie()
            && $this->config->bool('PAGE_CACHE_ENABLED', true);

        $cacheKey = 'page:' . md5($request->uri());

        if ($cacheable) {
            $cached = $this->cache->get($cacheKey);

            if ($cached !== null) {
                return Response::html($cached);
            }
        }

        $response = $this->router->dispatch($request, $this);

        if ($cacheable && !$this->session->isStarted() && $response->isCacheable()) {
            $this->cache->set($cacheKey, $response->body(), $this->config->int('PAGE_CACHE_TTL', 60));
        }

        return $response;
    }

    public function root(): string
    {
        return $this->root;
    }

    public function config(): Config
    {
        return $this->config;
    }

    public function request(): Request
    {
        return $this->request;
    }

    public function session(): Session
    {
        return $this->session;
    }

    public function flash(): Flash
    {
        return $this->flash;
    }

    public function auth(): AuthManager
    {
        return $this->auth;
    }

    public function csrf(): Csrf
    {
        return $this->csrf;
    }

    public function gate(): Gate
    {
        return $this->gate;
    }

    public function router(): Router
    {
        return $this->router;
    }

    public function view(): View
    {
        return $this->view;
    }

    public function cache(): CacheInterface
    {
        return $this->cache;
    }

    public function logger(): Logger
    {
        return $this->logger;
    }

    public function db(): ?Database
    {
        return $this->db;
    }
}
