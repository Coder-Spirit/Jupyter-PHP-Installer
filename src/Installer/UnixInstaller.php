<?php


namespace Litipk\JupyterPhpInstaller\Installer;


abstract class UnixInstaller extends Installer
{
    /**
     * @param $installPath
     * @return mixed
     */
    protected function executeSilentComposerCommand($installPath)
    {
        $composerOutputLines = [];

        $composerOutput = exec(
            'PATH=' . getenv('PATH') . ' ' .
            $this->composerCmd . ' --prefer-dist --no-interaction --no-progress --working-dir="' .
            $installPath . '" create-project litipk/jupyter-php=dev-master pkgs > /dev/null 2>&1 ',

            $composerOutputLines,
            $composerStatus
        );

        return $composerStatus;
    }
}
