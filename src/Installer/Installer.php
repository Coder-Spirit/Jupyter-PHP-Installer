<?php


namespace Litipk\JupyterPhpInstaller\Installer;


use Litipk\JupyterPhpInstaller\System\MacSystem;
use Litipk\JupyterPhpInstaller\System\System;
use Litipk\JupyterPhpInstaller\System\UnixSystem;
use Litipk\JupyterPhpInstaller\System\WindowsSystem;

abstract class Installer
{
    /** @var  System */
    protected $system;

    /** @var  string */
    protected $composerCmd;

    /**
     * @param null|string $composerCmd
     * @return Installer
     */
    public static function getInstaller($composerCmd = null)
    {
        $system = System::getSystem();
        $composerCmd = (null !== $composerCmd) ? $composerCmd : $system->getComposerCommand();

        if (null === $composerCmd || !$system->checkIfCommandExists($composerCmd)) {
            throw new \RuntimeException('Unable to find the composer executable.');
        }

        if ($system instanceof UnixSystem) {
            return new LinuxInstaller($system, $composerCmd);
        } elseif ($system instanceof MacSystem) {
            return new MacInstaller($system, $composerCmd);
        } elseif ($system instanceof WindowsSystem) {
            return new WindowsInstaller($system, $composerCmd);
        } else {
            throw new \RuntimeException('This platform is unknown for the installer');
        }
    }

    /**
     * @param null|string $installPath
     */
    public function install($installPath = null)
    {
        $installPath = (null !== $installPath) ? $installPath : $this->getInstallPath();
        if (null !== $installPath && !$this->system->validatePath($installPath)) {
            throw new \RuntimeException('Invalid install path.');
        }

        $this->system->ensurePath($installPath);
        $this->executeComposerCommand($installPath);

        $this->installKernel();
    }

    /**
     * @return string
     */
    protected abstract function getInstallPath();

    /**
     *
     */
    protected abstract function installKernel();

    /**
     * @param string $installPath
     */
    protected function executeComposerCommand($installPath)
    {
        $composerStatus = 0;

        echo "\n";
        passthru(
            $this->composerCmd.' --working-dir="'.$installPath.'" create-project dawehner/jupyter-php pkgs',
            $composerStatus
        );
        echo "\n";

        if ($composerStatus !== 0) {
            throw new \RuntimeException('Error while trying to download Jupyter-PHP dependencies with Composer.');
        }
    }

    /**
     * Installer constructor.
     * @param System $system
     * @param string $composerCmd
     */
    protected function __construct(System $system, $composerCmd)
    {
        $this->system = $system;
        $this->composerCmd = $composerCmd;
    }
}
