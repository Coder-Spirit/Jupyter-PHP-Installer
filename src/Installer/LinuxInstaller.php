<?php


namespace Litipk\JupyterPhpInstaller\Installer;


use Litipk\JupyterPhpInstaller\System\UnixSystem;


final class LinuxInstaller extends UnixInstaller
{
    /**
     * LinuxInstaller constructor.
     * @param UnixSystem $system
     * @param string $composerCmd
     */
    public function __construct(UnixSystem $system, $composerCmd)
    {
        parent::__construct($system, $composerCmd);
    }

    protected function getAdminInstallPath(): string
    {
        return '/opt/jupyter-php';
    }

    protected function getUserInstallPath(): string
    {
        return $this->system->getCurrentUserHome().'/.jupyter-php';
    }

    protected function getJupyterKernelsMetadatUserPath(): string
    {
        return $this->system->getCurrentUserHome().'/.local/share/jupyter/kernels/jupyter-php';
    }
}
