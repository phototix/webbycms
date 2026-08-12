<?php

declare(strict_types=1);

namespace WebbyCMS;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger as Monolog;
use Psr\Log\LoggerInterface;

/**
 * Application logger (PSR-3) writing to storage/logs/app.log.
 */
final class Logger
{
    private ?LoggerInterface $logger = null;

    public function __construct(
        private readonly string $logDir,
        private readonly string $name = 'webbycms',
        private readonly Level $level = Level::Debug,
    ) {
    }

    public function get(): LoggerInterface
    {
        if ($this->logger === null) {
            if (!is_dir($this->logDir) && !@mkdir($this->logDir, 0775, true) && !is_dir($this->logDir)) {
                throw new \RuntimeException("Log directory is not writable: {$this->logDir}");
            }

            $monolog = new Monolog($this->name);
            $handler = new StreamHandler($this->logDir . '/app.log', $this->level);
            $handler->setFormatter(new LineFormatter("[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n"));
            $monolog->pushHandler($handler);

            $this->logger = $monolog;
        }

        return $this->logger;
    }

    /**
     * @param array<string, mixed> $arguments
     */
    public function __call(string $method, array $arguments): mixed
    {
        return $this->get()->{$method}(...$arguments);
    }
}
