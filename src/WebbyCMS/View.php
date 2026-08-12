<?php

declare(strict_types=1);

namespace WebbyCMS;

/**
 * PHP template renderer.
 */
final class View
{
    public function __construct(private readonly string $root)
    {
    }

    /**
     * Render a view file (relative to the project root) with local variables.
     *
     * @param array<string, mixed> $vars
     */
    public function render(string $view, array $vars = []): string
    {
        $file = $this->root . '/' . ltrim($view, '/');

        if (!is_file($file)) {
            throw new \RuntimeException("View not found: {$view}");
        }

        extract($vars, EXTR_SKIP);

        ob_start();

        include $file;

        return (string) ob_get_clean();
    }

    public function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
