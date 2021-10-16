<?php

namespace SIKessEm\Setup;

class Program {
    public function __construct(protected string $wdir) {}

    public function execute(int $argc, array $argv): void {
        echo 'Setup your PHP App' . PHP_EOL;
    }
}