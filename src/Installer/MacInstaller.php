<?php


namespace Litipk\JupyterPhpInstaller\Installer;


use Litipk\JupyterPhpInstaller\System\MacSystem;


final class MacInstaller extends UnixInstaller
{
    public function __construct(MacSystem $system, string $composerCmd)
    {
        parent::__construct($system, $composerCmd);
    }

    protected function getAdminInstallPath(): string
    {
        return '/Applications/jupyter-php';
    }

    protected function getUserInstallPath(): string
    {
        return $this->system->getCurrentUserHome().'/Library/jupyter-php';
    }

    protected function getJupyterKernelsMetadatUserPath(): string
    {
        return $this->system->getCurrentUserHome().'/Library/Jupyter/kernels/jupyter-php';
    }
}
