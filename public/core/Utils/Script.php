<?php

class Script{
    public string $handle;
    public string $fileName;
    public array $deps;
    public string|bool|null $version;
    public array|bool $args;

    public function __construct(string $handle, string $fileName, array $deps, string|bool|null $version, array|bool $args)
    {
        $this->handle = $handle;
        $this->fileName = $fileName;
        $this->deps = $deps;
        $this->version = $version;
        $this->args = $args;
    }

    public static function register(bool $createNonce, string $handle, string $fileName, array $deps = [], string|bool|null $version = false, array|bool $args = false): void
    {
        $scriptLoader = new ScriptLoader();
        $scriptLoader->register($createNonce, new self($handle, $fileName, $deps, $version, $args));
    }
}