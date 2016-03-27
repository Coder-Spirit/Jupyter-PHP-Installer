<?php


namespace Litipk\JupyterPhpInstaller\Installer;


use Litipk\JupyterPhpInstaller\System\WindowsSystem;


final class WindowsInstaller extends Installer
{
    /**
     * LinuxInstaller constructor.
     * @param WindowsSystem $system
     * @param string $composerCmd
     */
    public function __construct(WindowsSystem $system, $composerCmd)
    {
        parent::__construct($system, $composerCmd);
    }

    /**
     * @return string
     */
    protected function getInstallPath()
    {
        // TODO: Implement getInstallPath() method.
    }

    /**
     *
     */
    protected function installKernel()
    {
        // TODO: Implement installKernel() method.
    }

    /**
     * @param $installPath
     * @return mixed
     */
    protected function executeSilentComposerCommand($installPath)
    {
        // TODO: Implement executeSilentComposerCommand() method.
    }
}
