<?php


namespace Litipk\JupyterPhpInstaller\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


final class UninstallCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('uninstall')
            ->setDescription('Uninstalls a Jupyter-PHP kernel.')
            ->setDefinition([
                new InputOption('verbose', 'v|vv|vvv', InputOption::VALUE_NONE, 'Shows more details.'),
                new InputArgument(
                    'path',
                    InputArgument::OPTIONAL,
                    'This is the path where the Jupyter-PHP kernel is installed.'
                ),
                new InputArgument(
                    'composer_cmd',
                    InputArgument::OPTIONAL,
                    'The installer will use this command to execute Composer.'
                )
            ])
            ->setHelp(
                "The <info>uninstall</info> command uninstalls a Jupyter-PHP kernel from your\n".
                "system.\n\n".
                "<info>php jupyter-php-installer.phar uninstall</info>\n\n"
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }
}