<?php


namespace Litipk\JupyterPhpInstaller\System;


abstract class UnixSystem extends System
{
    /** @return string */
    public function getCurrentUser()
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

    /** @return string */
    public function getCurrentUserHome()
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

    /**
     * @param string $cmdName
     * @return boolean
     */
    public function checkIfCommandExists($cmdName)
    {
        if (!function_exists('exec')) {
            return false;
        }

        $sysResponse = exec(
            "if command -v ".$cmdName." >/dev/null 2>&1; then echo \"true\"; else echo \"false\"; fi;"
        );

        return filter_var($sysResponse, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
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
     * Returns true if the path is a "valid" path and is writable (event if the complete path does not yet exist).
     * @param string $path
     * @return boolean
     */
    public function validatePath($path)
    {
        $absPath = $this->getAbsolutePath($path);
    }

    /**
     * @param string $path
     * @return string The "absolute path" version of $path.
     */
    public function ensurePath($path)
    {

    }

    /**
     * @param string $path
     * @return bool
     */
    protected function isAbsolutePath($path)
    {
        return (1 === preg_match('#^/#', $path));
    }

    /**
     * @param string $path
     * @return string
     */
    protected function getAbsolutePath($path)
    {
        return $this->isAbsolutePath($path) ? $path : (getcwd() . DIRECTORY_SEPARATOR . $path);
    }
}
