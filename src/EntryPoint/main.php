#!/usr/bin/env php
<?php


if ('cli' !== PHP_SAPI) {
    echo 'Warning: This command should be invoked via the CLI version of PHP, not the '.PHP_SAPI.' SAPI'.PHP_EOL;
}

require_once __DIR__ . '/bootstrap.php';


use Litipk\JupyterPhpInstaller\Console\Application;


$application = new Application();
$application->run();
