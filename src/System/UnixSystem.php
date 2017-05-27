<?php


namespace Litipk\JupyterPhpInstaller\System;


abstract class UnixSystem extends System
{
    public function getCurrentUser(): string
    {
        if (function_exists('posix_getpwuid') && function_exists('posix_geteuid')) {
            $pwuData = posix_getpwuid(posix_geteuid());
            return $pwuData['name'];
        } elseif ($this->checkIfCommandExists('whoami')) {
            return exec('whoami');
        } else {
            throw new \RuntimeException('Unable to obtain the current username.');
        }
    }

    public function getAdminUser(): string
    {
        return 'root';
    }

    public function getCurrentUserHome(): string
    {
        if (function_exists('posix_getpwuid') && function_exists('posix_geteuid')) {
            $pwuData = posix_getpwuid(posix_geteuid());
            return $pwuData['dir'];
        } elseif (function_exists('getenv') && false !== getenv('HOME')) {
            return getenv('HOME');
        } else {
            throw new \RuntimeException('Unable to obtain the current user home directory.');
        }
    }

    public function checkIfCommandExists(string $cmdName): bool
    {
        if (!function_exists('exec')) {
            return false;
        }

        $sysResponse = exec(
            'PATH='.getenv('PATH').'; '.
            "if command -v ".$cmdName." >/dev/null 2>&1; then echo \"true\"; else echo \"false\"; fi;"
        );

        return filter_var($sysResponse, FILTER_VALIDATE_BOOLEAN);
    }

    /** @return string|null */
    public function getComposerCommand()
    {
        $potentialCommands = [
            'composer', 'composer.phar', './composer.phar', './composer', './bin/composer', './bin/composer.phar',
            './vendor/bin/composer', './vendor/bin/composer.phar', '../vendor/bin/composer',
            '../vendor/bin/composer.phar', '../../vendor/bin/composer', '../../vendor/bin/composer.phar'
        ];

        foreach ($potentialCommands as $potentialCommand) {
            if ($this->checkIfCommandExists($potentialCommand)) {
                return $potentialCommand;
            }
        }

        return null;
    }

    /**
     * Returns true if the path is a "valid" path and is writable (even if the complete path does not yet exist).
     */
    public function validatePath(string $path): bool
    {
        $absPath = $this->getAbsolutePath($path);
        $absPathParts = preg_split('/\//', preg_replace('/(^\/|\/$)/', '', $absPath));
        $nSteps = count($absPathParts);

        $tmpPath = '';
        $prevReadable = false;
        $prevWritable = false;

        for ($i=0; $i<$nSteps; $i++) {
            $tmpPath .= '/' . $absPathParts[$i];

            if (file_exists($tmpPath)) {
                if (!is_dir($tmpPath)) {
                    if (is_link($tmpPath)) {
                        $linkPath = readlink($tmpPath);
                        if (false === $linkPath || !is_dir($linkPath)) {
                            return false;
                        }
                        $tmpPath = $linkPath;
                    } else {
                        return false;
                    }
                }

                $prevReadable = is_readable($tmpPath);
                $prevWritable = is_writable($tmpPath);
            } else {
                return ($prevReadable && $prevWritable);
            }
        }

        return true;
    }

    /**
     * @param string $path
     * @return string The "absolute path" version of $path.
     */
    public function ensurePath(string $path): string
    {
        $absPath = $this->getAbsolutePath($path);

        if (!file_exists($absPath) && false === mkdir($absPath, 0755, true)) {
            throw new \RuntimeException('Unable to create the specified directory ('.$absPath.').');
        }

        return $absPath;
    }

    public function wrapCommandToNullifyItsOutput(string $command): string
    {
        return $command . ' > /dev/null 2>&1 ';
    }

    public function wrapCommandToAttachEnvironmentVariable(string $varName, string $varValue, string $command)
    {
        return ' ' . $varName . '=' . $varValue . ' && ' . $command;
    }

    protected function isAbsolutePath(string $path): bool
    {
        return (1 === preg_match('#^/#', $path));
    }

    protected function getAbsolutePath(string $path): string
    {
        return $this->isAbsolutePath($path) ? $path : (getcwd() . DIRECTORY_SEPARATOR . $path);
    }
}
