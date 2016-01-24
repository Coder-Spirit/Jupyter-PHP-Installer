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
     * @param null|string $installPath
     * @return Installer
     */
    public static function getInstaller($composerCmd = null, $installPath = null)
    {
        $system = System::getSystem();
        $composerCmd = (null !== $composerCmd) ? $composerCmd : $system->getComposerCommand();

        if (null === $composerCmd || !$system->checkIfCommandExists($composerCmd)) {
            throw new \RuntimeException('Unable to find the composer executable.');
        }

        if (null !== $installPath && !$system->validatePath($installPath)) {
            throw new \RuntimeException('Invalid install path.');
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
