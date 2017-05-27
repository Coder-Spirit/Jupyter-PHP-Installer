<?php


namespace Litipk\JupyterPhpInstaller\Installer;


use Litipk\JupyterPhpInstaller\System\WindowsSystem;


final class WindowsInstaller extends Installer
{
    /**
     * Also defined in the base class, only here to make more explicit its type.
     * @var  WindowsSystem
     */
    protected $system;

    public function __construct(WindowsSystem $system, string $composerCmd)
    {
        parent::__construct($system, $composerCmd);
    }

    protected function getAdminInstallPath(): string
    {
        return $this->system->getProgramDataPath() . '\jupyter-php';
    }

    protected function getUserInstallPath(): string
    {
        return $this->system->getCurrentUserHome() . '\.jupyter-php';
    }

    protected function getKernelEntryPointPath(): string
    {
        return $this->getInstallPath() . '\pkgs\vendor\litipk\jupyter-php\src\kernel.php';
    }

    protected function getJupyterKernelsMetadataAdminPath(): string
    {
        return $this->system->getProgramDataPath() . '\jupyter\kernels\jupyter-php';
    }

    protected function getJupyterKernelsMetadatUserPath(): string
    {
        return $this->system->getAppDataPath() . '\jupyter\kernels\jupyter-php';
    }
}
