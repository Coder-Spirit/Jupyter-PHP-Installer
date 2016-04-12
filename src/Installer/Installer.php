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
     * @param bool $beVerbose
     */
    public function install($installPath = null, $beVerbose = false)
    {
        $installPath = (null !== $installPath) ? $installPath : $this->getInstallPath();
        if (null !== $installPath && !$this->system->validatePath($installPath)) {
            throw new \RuntimeException('Invalid install path.');
        }

        $this->system->ensurePath($installPath);
        $this->executeComposerCommand($installPath, $beVerbose);

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
     * @param $installPath
     * @return mixed
     */
    protected abstract function executeSilentComposerCommand($installPath);

    /**
     * @param string $installPath
     * @param bool $beVerbose
     */
    protected function executeComposerCommand($installPath, $beVerbose = false)
    {
        $composerStatus = 0;

        $pkgsDir = $installPath.DIRECTORY_SEPARATOR.'pkgs';
        if (file_exists($pkgsDir)) {
            foreach (
                new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($pkgsDir, \FilesystemIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::CHILD_FIRST
                ) as $path
            ) {
                $path->isDir() && !$path->isLink() ? rmdir($path->getPathname()) : unlink($path->getPathname());
            }
            rmdir($pkgsDir);
        }
        
        if ($beVerbose) {
            echo "\n";
            passthru(
                'PATH=' . getenv('PATH') . ' ' .
                $this->composerCmd . ' --prefer-dist --no-interaction --working-dir="' .
                $installPath .'" create-project litipk/jupyter-php=0.* pkgs',

                $composerStatus
            );
            echo "\n";
        } else {
            $composerStatus = $this->executeSilentComposerCommand($installPath);
        }

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
