<?php


namespace Litipk\JupyterPhpInstaller\Installer;


use Litipk\JupyterPhpInstaller\System\UnixSystem;


final class LinuxInstaller extends UnixInstaller
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
    protected function getInstallPath()
    {
        $currentUser = $this->system->getCurrentUser();

        if ('root' === $currentUser) {
            return '/opt/jupyter-php';
        } else {
            return $this->system->getCurrentUserHome().'/.jupyter-php';
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
            $this->system->getCurrentUserHome().'/.local/share/jupyter/kernels/jupyter-php';

        $this->system->ensurePath($kernelSpecPath);
        file_put_contents($kernelSpecPath.'/kernel.json', $kernelDef);
    }
}
