<?php


namespace Litipk\JupyterPhpInstaller\Console;


use Litipk\JupyterPhpInstaller\Command\InfoCommand;
use Litipk\JupyterPhpInstaller\Command\InstallCommand;
use Litipk\JupyterPhpInstaller\Command\UninstallCommand;
use Litipk\JupyterPhpInstaller\Command\UpdateCommand;
use Litipk\JupyterPhpInstaller\IO\ConsoleIO;
use Litipk\JupyterPhpInstaller\IO\IOInterface;
use Litipk\JupyterPhpInstaller\Util\ErrorHandler;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * The console application that handles the commands
 */
final class Application extends BaseApplication
{
    /** @var IOInterface */
    private $io;

    public function __construct()
    {
        static $shutdownRegistered = false;

        if (function_exists('ini_set') && extension_loaded('xdebug')) {
            ini_set('xdebug.show_exception_trace', false);
            ini_set('xdebug.scream', false);
        }

        if (function_exists('date_default_timezone_set') && function_exists('date_default_timezone_get')) {
            date_default_timezone_set(@date_default_timezone_get());
        }

        if (!$shutdownRegistered) {
            register_shutdown_function(function () { $this->handleShutdown(); });
            $shutdownRegistered = true;
        }

        parent::__construct('Jupyter-PHP Installer', '0.2');
    }

    /**
     * Starts the application, boilerplate code.
     * @param InputInterface|null $input
     * @param OutputInterface|null $output
     * @return int
     * @throws \Exception
     */
    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        if (null === $output) {
            $output = new ConsoleOutput(
                ConsoleOutput::VERBOSITY_NORMAL,
                null,
                new OutputFormatter(false, [
                    'highlight' => new OutputFormatterStyle('red'),
                    'warning' => new OutputFormatterStyle('black', 'yellow'),
                ])
            );
        }

        return parent::run($input, $output);
    }

    /**
     * Runs the application's business logic.
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Throwable
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $this->io = new ConsoleIO($input, $output, $this->getHelperSet());
        ErrorHandler::register($this->io);
        $io = $this->io;

        if (PHP_VERSION_ID < 70000) {
            $io->writeError(
                '<warning>'.
                'This installer only officially supports PHP 7.0 and above, you will most likely encounter problems with your PHP '.
                PHP_VERSION.', upgrading is strongly recommended'.
                '</warning>'
            );
        }

        if (extension_loaded('xdebug')) {
            $io->writeError(
                '<warning>'.
                'You are running PHP-CLI with xdebug enabled. This will have a major impact on the kernel\'s performance.'.
                '</warning>'
            );
        }

        return parent::doRun($input, $output);
    }

    /**
     * @return IOInterface
     */
    public function getIO()
    {
        return $this->io;
    }

    /**
     * Initializes all the composer commands.
     */
    protected function getDefaultCommands()
    {
        $commands = parent::getDefaultCommands();
        $commands[] = new InstallCommand();
        $commands[] = new UninstallCommand();
        $commands[] = new UpdateCommand();
        $commands[] = new InfoCommand();


        if ('phar:' === substr(__FILE__, 0, 5)) {
            // Nothing to do, yet
        }

        return $commands;
    }

    /**
     * Handles the program shutdown.
     */
    private function handleShutdown()
    {
        $lastError = error_get_last();

        if (!empty($lastError) && isset($lastError['message'])) {
            $this->handleShutdownLastError($lastError);
        }
    }

    /**
     * Handles the errors that caused the shutdown.
     * @param array $lastError
     */
    private function handleShutdownLastError(array $lastError)
    {
        if (
            strpos($lastError['message'], 'Allowed memory') !== false  /* Zend PHP out of memory error */ ||
            strpos($lastError['message'], 'exceeded memory') !== false /* HHVM out of memory errors */
        ) {
            $this->explainMemoryErrorSolution();
        }
    }

    /**
     * Explains to the user what can be done to solve the memory error that caused the shutdown.
     */
    private function explainMemoryErrorSolution()
    {
        if (extension_loaded('xdebug')) {
            echo "\n" . 'You should disable the Xdebug extension in your php.ini settings file.' . "\n";
        } else {
            echo "\n" . 'You should increase the `memory_limit` parameter in your php.ini settings file.' . "\n";
        }
    }
}
