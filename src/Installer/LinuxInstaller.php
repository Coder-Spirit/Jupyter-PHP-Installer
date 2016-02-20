<?php


namespace Litipk\JupyterPhpInstaller\Installer;


use Litipk\JupyterPhpInstaller\System\UnixSystem;


final class LinuxInstaller extends Installer
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

    /**
     * @return string
     */
    public function getInstallPath()
    {
        $currentUser = $this->system->getCurrentUser();

        if ('root' === $currentUser) {
            return '/opt/jupyter-php';
        } else {
            return $this->system->getCurrentUserHome().'/.jupyter-php';
        }
    }
}
