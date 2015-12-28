<?php


namespace Litipk\JupyterPhpInstaller\Installer;


use Litipk\JupyterPhpInstaller\System\System;

abstract class Installer
{
    /**
     * @param null|string $installPath
     * @param null|string $composerCmd
     * @return Installer
     */
    public static function getInstaller($installPath = null, $composerCmd = null)
    {
        $system = System::getSystem();
    }
}
