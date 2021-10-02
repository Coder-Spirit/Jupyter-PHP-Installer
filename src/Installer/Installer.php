<?php


namespace Litipk\JupyterPhpInstaller\Installer;


use Litipk\JupyterPhpInstaller\System\LinuxSystem;
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

    public static function getInstaller(string $composerCmd = null): Installer
    {
        $system = System::getSystem();
        $composerCmd = (null !== $composerCmd) ? $composerCmd : $system->getComposerCommand();

        if (null === $composerCmd || !$system->checkIfCommandExists($composerCmd)) {
            throw new \RuntimeException('Unable to find the composer executable.');
        }

        if ($system instanceof LinuxSystem) {
            return new LinuxInstaller($system, $composerCmd);
        } elseif ($system instanceof MacSystem) {
            return new MacInstaller($system, $composerCmd);
        } elseif ($system instanceof UnixSystem) {
            // Fallback for BSD systems
            return new LinuxInstaller($system, $composerCmd);
        } elseif ($system instanceof WindowsSystem) {
            return new WindowsInstaller($system, $composerCmd);
        } else {
            throw new \RuntimeException('This platform is unknown for the installer');
        }
    }

    public function install(string $installPath = null, bool $beVerbose = false)
    {
        $installPath = (null !== $installPath) ? $installPath : $this->getInstallPath();
        if (null !== $installPath && !$this->system->validatePath($installPath)) {
            throw new \RuntimeException('Invalid install path.');
        }

        $this->system->ensurePath($installPath);
        $this->executeComposerCommand($installPath, $beVerbose);

        $this->installKernel();
    }

    protected function getInstallPath(): string
    {
        return ($this->system->isRunningAsAdmin())
            ? $this->getAdminInstallPath()
            : $this->getUserInstallPath();
    }

    protected abstract function getAdminInstallPath(): string;

    protected abstract function getUserInstallPath(): string;

    protected function installKernel()
    {
        $kernelDef = json_encode([
            'argv' => [
                'php',
                $this->getKernelEntrypointPath(),
                '{connection_file}'
            ],
            'display_name' => 'PHP',
            'language' => 'php',
            'env' => new \stdClass
        ]);

        $kernelSpecPath = ($this->system->isRunningAsAdmin())
            ? $this->getJupyterKernelsMetadataAdminPath()
            : $this->getJupyterKernelsMetadatUserPath();

        $this->system->ensurePath($kernelSpecPath);
        file_put_contents($kernelSpecPath.'/kernel.json', $kernelDef);
    }

    protected abstract function getKernelEntryPointPath(): string;

    protected abstract function getJupyterKernelsMetadataAdminPath(): string;

    protected abstract function getJupyterKernelsMetadatUserPath(): string;

    protected function executeComposerCommand(string $installPath, bool $beVerbose = false)
    {
        $composerStatus = 0;

        $pkgsDir = $installPath.DIRECTORY_SEPARATOR.'pkgs';
        $this->preparePackagesDir($pkgsDir);
        
        if ($beVerbose) {
            echo "\n";
            passthru(
                $this->system->wrapCommandToAttachEnvironmentVariable(
                    'PATH', getenv('PATH'),
                    $this->getComposerInitCommand($pkgsDir) . ' && ' .
                    $this->getComposerInstallCommand($pkgsDir)
                ),

                $composerStatus
            );
            echo "\n";
        } else {
            $composerStatus = $this->executeSilentComposerCommand($pkgsDir);
        }

        if (0 !== $composerStatus) {
            throw new \RuntimeException('Error while trying to download Jupyter-PHP dependencies with Composer.');
        }
    }

    protected function executeSilentComposerCommand(string $pkgsDir)
    {
        $composerOutputLines = [];

        exec(
            $this->system->wrapCommandToAttachEnvironmentVariable(
                'PATH', getenv('PATH'),
                $this->getComposerInitCommand($pkgsDir, true) . ' && ' .
                $this->getComposerInstallCommand($pkgsDir, true)
            ),

            $composerOutputLines,
            $composerStatus
        );

        return $composerStatus;
    }

    private function getComposerInitCommand(string $pkgsDir, bool $silent = false): string
    {
        $cmd = (
            $this->composerCmd . ' init ' .
            ' --no-interaction ' .
            ' --name=jupyter/php_instance ' .
            ' --type=project ' .
            ' --working-dir="' . $pkgsDir . '" ' .
            ' --require=litipk/jupyter-php=0.* '
        );

        return ($silent)
            ? $this->system->wrapCommandToNullifyItsOutput($cmd)
            : $cmd;
    }

    private function getComposerInstallCommand(string $pkgsDir, bool $silent = false): string
    {
        $cmd = (
            $this->composerCmd . ' install ' .
            ' --no-interaction ' .
            ' --no-progress ' .
            ' --prefer-dist ' .
            ' --optimize-autoloader ' .
            ' --working-dir="' . $pkgsDir . '" '
        );

        return ($silent)
            ? $this->system->wrapCommandToNullifyItsOutput($cmd . ' --no-progress ')
            : $cmd;
    }

    protected function __construct(System $system, string $composerCmd)
    {
        $this->system = $system;
        $this->composerCmd = $composerCmd;
    }

    protected function preparePackagesDir(string $pkgsDir)
    {
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

        if (!mkdir($pkgsDir) && !is_dir($pkgsDir)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $pkgsDir));
        }
    }
}
