<?php


namespace Litipk\JupyterPhpInstaller\Installer;


use Litipk\JupyterPhpInstaller\System\MacSystem;


final class MacInstaller extends UnixInstaller
{
    /**
     * LinuxInstaller constructor.
     * @param MacSystem $system
     * @param string $composerCmd
     */
    public function __construct(MacSystem $system, $composerCmd)
    {
        parent::__construct($system, $composerCmd);
    }

    /**
     * @return string
     */
    protected function getInstallPath()
    {
        $currentUser = $this->system->getCurrentUser();

        if ('root' === $currentUser) {
            return '/Applications/jupyter-php';
        } else {
            return $this->system->getCurrentUserHome().'/Library/jupyter-php';
        }
    }

    /**
     *
     */
    protected function installKernel()
    {
        $kernelDef = json_encode([
            'argv' => ['php', $this->getInstallPath().'/pkgs/src/kernel.php', '{connection_file}'],
            'display_name' => 'PHP',
            'language' => 'php',
            'env' => new \stdClass
        ]);

        $currentUser = $this->system->getCurrentUser();

        $kernelSpecPath = ('root' === $currentUser) ?
            '/usr/local/share/jupyter/kernels/jupyter-php' :
            $this->system->getCurrentUserHome().'/Library/Jupyter/kernels/jupyter-php';

        $this->system->ensurePath($kernelSpecPath);
        file_put_contents($kernelSpecPath.'/kernel.json', $kernelDef);
    }
}
