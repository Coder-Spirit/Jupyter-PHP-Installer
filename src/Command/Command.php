<?php


namespace Litipk\JupyterPhpInstaller\Command;


use Litipk\JupyterPhpInstaller\Console\Application;
use Litipk\JupyterPhpInstaller\IO\IOInterface;

use Litipk\JupyterPhpInstaller\IO\NullIO;
use Symfony\Component\Console\Command\Command as BaseCommand;


abstract class Command extends BaseCommand
{
    /** @var  IOInterface */
    private $io;

    public function getIO()
    {
        if (null === $this->io) {
            $application = $this->getApplication();

            if ($application instanceof Application) {
                $this->io = $application->getIO();
            } else {
                $this->io = new NullIO();
            }
        }

        return $this->io;
    }
}
