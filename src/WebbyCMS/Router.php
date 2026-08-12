<?php

declare(strict_types=1);

namespace WebbyCMS;

/**
 * Resolves the incoming request to a page under /pages and renders it.
 *
 * URL scheme (kept from WebbyCMS 1.x):
 *   /page
 *   /page/cate
 *   /page/cate/action
 *   /page/cate/action/id
 *   /page/cate/action/id/sub
 *   ... optionally ending in .html, e.g. /about/team/5.html
 */
final class Router
{
    private const SEGMENTS = ['page', 'cate', 'action', 'id', 'sub', 'subsub'];

    public function __construct(private readonly string $root)
    {
    }

    /**
     * @return array<string, string>
     */
    public function resolve(Request $request): array
    {
        $path = rtrim($request->path(), '/');

        if ($path === '' || $path === '/index.php' || $path === 'index.php') {
            return $this->fromQuery($request);
        }

        $path = preg_replace('/\.html$/i', '', $path) ?? $path;
        $segments = array_values(array_filter(explode('/', trim($path, '/')), static fn (string $s): bool => $s !== ''));

        if (isset($segments[0]) && $segments[0] === 'index.php') {
            array_shift($segments);
        }

        $params = [];

        foreach (self::SEGMENTS as $index => $key) {
            $params[$key] = isset($segments[$index]) ? rawurldecode($segments[$index]) : '';
        }

        if ($params['page'] === '') {
            $params['page'] = 'home';
        }

        return $params;
    }

    /**
     * Dispatch the request to a page, form handler, or error page.
     */
    public function dispatch(Request $request, Bootstrap $app): Response
    {
        $params = $this->resolve($request);
        $request->setSegments($params);
        $page = $params['page'];

        // /error/404 and /error/500 are served as status-mapped error pages.
        if ($page === 'error' && in_array($params['cate'], ['404', '500'], true)) {
            $file = $this->root . '/pages/error/' . $params['cate'] . '.php';

            if (is_file($file)) {
                return $this->render($file, $params, (int) $params['cate'], $app);
            }
        }

        // Enforce CSRF on POST once the site has been migrated.
        if ($request->isPost() && $app->config()->bool('CSRF_ENFORCE', false)) {
            if (!$app->csrf()->validateRequest($request)) {
                $app->flash()->error('Invalid security token. Please refresh the page and try again.');

                return Response::redirect($this->referer($request) ?: '/', 303);
            }
        }

        $form = (string) $request->input('form', '');

        if ($request->isPost() && $form !== '') {
            return $this->handleForm($form, $params, $request, $app);
        }

        $file = $this->root . '/pages/' . $page . '/index.php';

        if (!is_file($file)) {
            $notFound = $this->root . '/pages/error/404.php';

            return $this->render($notFound, $params, 404, $app);
        }

        return $this->render($file, $params, 200, $app);
    }

    /**
     * @param array<string, string> $params
     */
    private function handleForm(string $form, array $params, Request $request, Bootstrap $app): Response
    {
        $formFile = $this->root . '/pages/' . $params['page'] . '/forms/' . basename($form) . '.php';

        $result = null;

        if (is_file($formFile)) {
            ob_start();
            $this->includeLegacy();
            $result = include $formFile;
            ob_end_clean();
        } else {
            $app->flash()->error('Form handler not found: ' . htmlspecialchars($form, ENT_QUOTES));
        }

        if ($result instanceof Response) {
            return $result;
        }

        if ($request->isAjax()) {
            return Response::json([
                'success' => $app->flash()->peek('success'),
                'error' => $app->flash()->peek('error'),
            ]);
        }

        $redirectUrl = (string) $request->input('redirectURL', '');
        $url = $redirectUrl !== '' && $redirectUrl !== 'noredirect'
            ? $redirectUrl
            : ($this->referer($request) ?: '/');

        return Response::redirect($url, 303);
    }

    /**
     * @param array<string, string> $params
     */
    private function render(string $pageFile, array $params, int $status, Bootstrap $app): Response
    {
        $vars = $params + [
            'request' => $app->request(),
            'app' => $app,
            'view' => $app->view(),
            'strWebTitle' => (string) $app->config()->get('APP_NAME', 'Welcome'),
            'strStartDate' => '2012-12-31',
        ];

        ob_start();
        $this->includeLegacy();
        extract($vars, EXTR_SKIP);
        include $this->root . '/includes/htmlstart.php';
        include $pageFile;
        include $this->root . '/includes/javascripts.php';
        include $this->root . '/includes/htmlend.php';

        return new Response($status, (string) ob_get_clean());
    }

    /**
     * Load the transitional legacy globals so pre-2.0 pages keep working.
     */
    private function includeLegacy(): void
    {
        $file = $this->root . '/controller/legacy.php';

        if (is_file($file)) {
            include_once $file;
        }
    }

    private function referer(Request $request): string
    {
        $referer = $request->header('Referer');

        return is_string($referer) ? $referer : '';
    }

    /**
     * @return array<string, string>
     */
    private function fromQuery(Request $request): array
    {
        $query = $request->query();
        $params = [];

        foreach (self::SEGMENTS as $key) {
            $params[$key] = isset($query[$key]) ? (string) $query[$key] : '';
        }

        if ($params['page'] === '' || $params['page'] === 'index') {
            $params['page'] = 'home';
        }

        return $params;
    }
}
