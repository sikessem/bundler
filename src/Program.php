<?php

namespace SIKessEm\Setup;

class Program {
    public function __construct(protected string $wdir) {}

    public function execute(int $argc, array $argv): void {
        if ( $argc > 1) {
            switch ($argv[1]) {
                case '-c':
                    fprintf(STDOUT, 'We are going to compile file from %s' . PHP_EOL, $this->wdir);
                    break;
                
                default:
                    fprintf(STDERR, 'Unknow command: %s' . PHP_EOL, implode(' ', $argv));
                    exit(1);
            }

        } else {
            echo 'Setup your PHP App' . PHP_EOL;
        }
    }
}