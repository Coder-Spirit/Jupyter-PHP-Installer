<?php

/*
 * This file is part of Composer (and modified to fit in Jupyter-PHP-Installer)
 *
 * (c) Nils Adermann <naderman@naderman.de>
 *     Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
function includeIfExists($file)
{
    return file_exists($file) ? include $file : false;
}

if ((!$loader = includeIfExists(__DIR__ . '/../../vendor/autoload.php'))) {
    echo 'The dependencies are missing, you should use `composer install`.'.PHP_EOL;
    exit(1);
}

return $loader;
