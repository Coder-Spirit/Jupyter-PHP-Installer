<?php


namespace Litipk\JupyterPhpInstaller\Installer;


use Litipk\JupyterPhpInstaller\System\MacSystem;


final class MacInstaller extends Installer
{
    /**
     * LinuxInstaller constructor.
     * @param MacSystem $system
     * @param string $composerCmd
     */
    public function __construct(MacSystem $system, $composerCmd)
    {
        parent::__construct($system, $composerCmd);
    }
}
