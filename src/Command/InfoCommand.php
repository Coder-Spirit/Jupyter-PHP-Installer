<?php


namespace Litipk\JupyterPhpInstaller\Command;


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


final class InfoCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('info')
            ->setDescription('Shows info about installed Jupyter-PHP kernels.')
            ->setDefinition([
                new InputOption('verbose', 'v|vv|vvv', InputOption::VALUE_NONE, 'Shows more details.'),
                new InputArgument(
                    'path',
                    InputArgument::OPTIONAL,
                    'If is provided, then instead of looking for install paths, uses the given one.'
                )
            ])
            ->setHelp(
                "The <info>info</info> command looks for installed Jupyter-PHP kernels\n".
                "and shows info about them.\n\n".
                "<info>php jupyter-php-installer.phar info</info>\n\n"
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }
}