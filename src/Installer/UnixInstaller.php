<?php


namespace Litipk\JupyterPhpInstaller\Installer;


abstract class UnixInstaller extends Installer
{
    protected function getJupyterKernelsMetadataAdminPath(): string
    {
        return '/usr/local/share/jupyter/kernels/jupyter-php';
    }

    protected function getKernelEntryPointPath(): string
    {
        return $this->getInstallPath() . '/pkgs/vendor/litipk/jupyter-php/src/kernel.php';
    }
}
