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
        $currentUser = $this->system->getCurrentUser();

        if ('Administrator' === $currentUser) {
            return $this->getProgramDataPath() . '\jupyter-php';
        } else {
            return $this->system->getCurrentUserHome() . '\.jupyter-php';
        }
    }

    /**
     *
     */
    protected function installKernel()
    {
        $kernelDef = json_encode([
            'argv' => ['php', $this->getInstallPath() . '\pkgs\src\kernel.php', '{connection_file}'],
            'display_name' => 'PHP',
            'language' => 'php',
            'env' => new \stdClass
        ]);

        $currentUser = $this->system->getCurrentUser();

        $kernelSpecPath = ('Administrator' === $currentUser) ?
            $this->getProgramDataPath() . '\jupyter\kernels\jupyter-php' :
            $this->getAppDataPath() . '\jupyter\kernels\jupyter-php';

        $this->system->ensurePath($kernelSpecPath);
        file_put_contents($kernelSpecPath . '\kernel.json', $kernelDef);
    }

    /**
     * @param $installPath
     * @return mixed
     */
    protected function executeSilentComposerCommand($installPath)
    {
        $composerOutputLines = [];

        exec(
            $this->composerCmd . ' --prefer-dist --no-interaction --no-progress --working-dir="' .
            $installPath . '" create-project litipk/jupyter-php=dev-master pkgs > nul 2>&1 ',

            $composerOutputLines,
            $composerStatus
        );

        return $composerStatus;
    }

    private function getProgramDataPath()
    {
        if (function_exists('getenv') && false !== getenv('PROGRAMDATA')) {
            return getenv('PROGRAMDATA');
        } else {
            throw new \RuntimeException('Unable to obtain the program data directory.');
        }
    }

    private function getAppDataPath()
    {
        if (function_exists('getenv') && false !== getenv('APPDATA')) {
            return getenv('APPDATA');
        } else {
            throw new \RuntimeException('Unable to obtain the app data directory.');
        }
    }
}
