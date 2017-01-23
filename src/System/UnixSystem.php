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
            'PATH='.getenv('PATH').'; '.
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
     * Returns true if the path is a "valid" path and is writable (even if the complete path does not yet exist).
     * @param string $path
     * @return boolean
     */
    public function validatePath($path)
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
    public function ensurePath($path)
    {
        $absPath = $this->getAbsolutePath($path);

        if (!file_exists($absPath) && false === mkdir($absPath, 0755, true)) {
            throw new \RuntimeException('Unable to create the specified directory ('.$absPath.').');
        }

        return $absPath;
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
