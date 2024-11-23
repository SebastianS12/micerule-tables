<?php

class Logger
{
    private static ?Logger $instance = null;
    private array $logs = [];

    private function __construct() {}

    public static function getInstance(): Logger
    {
        if (self::$instance === null) {
            self::$instance = new Logger();
        }
        return self::$instance;
    }

    public function log(string $message): void
    {
        $this->logs[] = $message;
    }

    public function getLogs(): array
    {
        $logs = $this->logs;
        $this->reset();
        return $logs;
    }

    private function reset(): void
    {
        $this->logs = [];
    }
}