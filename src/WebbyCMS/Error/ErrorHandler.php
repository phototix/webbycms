<?php

declare(strict_types=1);

namespace WebbyCMS\Error;

use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Registers PHP error/exception handlers that log failures and render a safe
 * 500 page (stack traces only when APP_DEBUG=true).
 */
final class ErrorHandler
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly bool $debug = false,
    ) {
    }

    public function register(): void
    {
        ini_set('display_errors', $this->debug ? '1' : '0');
        error_reporting(E_ALL);

        set_error_handler(function (int $severity, string $message, string $file, int $line): never {
            throw new \ErrorException($message, 0, $severity, $file, $line);
        });

        set_exception_handler([$this, 'handle']);
    }

    public function handle(Throwable $throwable): void
    {
        $this->logger->error($throwable->getMessage(), [
            'file' => $throwable->getFile(),
            'line' => $throwable->getLine(),
            'trace' => $throwable->getTraceAsString(),
        ]);

        if (headers_sent()) {
            echo "\n<!-- uncaught error: " . htmlspecialchars($throwable->getMessage(), ENT_QUOTES) . " -->\n";

            return;
        }

        http_response_code(500);
        header('Content-Type: text/html; charset=utf-8');

        if ($this->debug) {
            echo '<!DOCTYPE html><html><head><title>500 Internal Server Error</title></head><body><h1>500 Internal Server Error</h1><pre>'
                . htmlspecialchars((string) $throwable, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
                . '</pre></body></html>';
        } else {
            echo '<!DOCTYPE html><html><head><title>500 Internal Server Error</title></head><body><h1>500 Internal Server Error</h1>'
                . '<p>Something went wrong. Please try again later.</p></body></html>';
        }
    }
}
