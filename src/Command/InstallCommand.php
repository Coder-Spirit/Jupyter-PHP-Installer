<?php


namespace Litipk\JupyterPhpInstaller\Command;


use Litipk\JupyterPhpInstaller\Installer\Installer;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


final class InstallCommand extends Command
{
    const RETURN_CODE_OK = 0;
    const RETURN_CODE_INSTALLER_INSTANTIATION_ERROR = 1;
    const RETURN_CODE_INSTALLER_INSTALL_ERROR = 2;

    protected function configure()
    {
        $this
            ->setName('install')
            ->setDescription('Installs a Jupyter-PHP kernel.')
            ->setDefinition([
                new InputOption('verbose', 'v|vv|vvv', InputOption::VALUE_NONE, 'Shows more details.'),
                new InputArgument(
                    'path',
                    InputArgument::OPTIONAL,
                    'This is the path where the Jupyter-PHP kernel will be installed.'
                ),
                new InputArgument(
                    'composer_cmd',
                    InputArgument::OPTIONAL,
                    'The installer will use this command to execute Composer.'
                )
            ])
            ->setHelp(
                "The <info>install</info> command installs a Jupyter-PHP kernel in your\n".
                "system and makes possible that Jupyter uses it to create \"PHP\n".
                "Notebooks\"\n\n".
                "<info>php jupyter-php-installer.phar install</info>\n\n"
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $installPath = ($input->hasArgument('path')) ? $input->getArgument('path') : null;
        $composerCmd = ($input->hasArgument('composer_cmd')) ? $input->getArgument('composer_cmd') : null;

        $io = $this->getIO();

        try {
            $installer = Installer::getInstaller($composerCmd);
        } catch (\RuntimeException $e) {
            $io->writeError('ERROR: '.$e->getMessage());
            // TODO : Use verbosity levels to enable showing the stacktrace
            return self::RETURN_CODE_INSTALLER_INSTANTIATION_ERROR;
        }

        try {
            $installer->install($installPath, (bool)$input->getOption('verbose'));
        } catch (\RuntimeException $e) {
            $io->writeError('ERROR: '.$e->getMessage());
            // TODO : Use verbosity levels to enable showing the stacktrace
            return self::RETURN_CODE_INSTALLER_INSTALL_ERROR;
        }

        $io->write('<info>The Jupyter-PHP kernel has been successfully installed.</info>');

        return self::RETURN_CODE_OK;
    }
}