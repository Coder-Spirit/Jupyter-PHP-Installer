<?php


namespace Litipk\JupyterPhpInstaller\System;


final class WindowsSystem extends System
{
    public function getOperativeSystem(): int
    {
        return self::OS_WIN;
    }

    public function getCurrentUser(): string
    {
        if (function_exists('getenv') && false !== getenv('username')) {
            return getenv('username');
        } else {
            throw new \RuntimeException('Unable to obtain the current username.');
        }
    }

    public function getAdminUser(): string
    {
        return 'Administrator';
    }

    public function getCurrentUserHome(): string
    {
        if (function_exists('getenv') && false !== getenv('HOMEDRIVE') && false !== getenv('HOMEPATH')) {
            return getenv("HOMEDRIVE") . getenv("HOMEPATH");
        } else {
            throw new \RuntimeException('Unable to obtain the current user home directory.');
        }
    }

    public function checkIfCommandExists(string $cmdName): bool
    {
        if (!function_exists('exec')) {
            return false;
        }

        $sysResponse = exec("where $cmdName > nul 2>&1 && echo true");

        return filter_var($sysResponse, FILTER_VALIDATE_BOOLEAN);
    }

    public function getComposerCommand(): string
    {
        return 'composer';
    }

    /**
     * Returns true if the path is a "valid" path and is writable (event if the complete path does not yet exist).
     */
    public function validatePath(string $path): bool
    {
        $absPath = $this->getAbsolutePath($path);
        $absPathParts = explode(DIRECTORY_SEPARATOR, $absPath);
        $nSteps = count($absPathParts);

        $tmpPath = $absPathParts[0];
        $prevReadable = false;
        $prevWritable = false;

        for ($i = 1; $i < $nSteps; $i++) {
            $tmpPath .= DIRECTORY_SEPARATOR . $absPathParts[$i];

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
            throw new \RuntimeException('Unable to create the specified directory (' . $absPath . ').');
        }

        return $absPath;
    }

    public function wrapCommandToNullifyItsOutput(string $command): string
    {
        return $command . ' > nul 2>&1 ';
    }

    public function wrapCommandToAttachEnvironmentVariable(string $varName, string $varValue, string $command)
    {
        return ' set ' . $varName . '=' . $varValue . ' && ' . $command;
    }

    public function getProgramDataPath(): string
    {
        if (function_exists('getenv') && false !== getenv('PROGRAMDATA')) {
            return getenv('PROGRAMDATA');
        } else {
            throw new \RuntimeException('Unable to obtain the program data directory.');
        }
    }

    public function getAppDataPath(): string
    {
        if (function_exists('getenv') && false !== getenv('APPDATA')) {
            return getenv('APPDATA');
        } else {
            throw new \RuntimeException('Unable to obtain the app data directory.');
        }
    }

    protected function isAbsolutePath(string $path): bool
    {
        return preg_match('/^[a-z]\:/i', $path) === 1;
    }

    protected function getAbsolutePath(string $path): string
    {
        $path = $this->isAbsolutePath($path) ? $path : (getcwd() . DIRECTORY_SEPARATOR . $path);

        // Normalise directory separators
        $path = preg_replace('/[\/\\\\]/u', DIRECTORY_SEPARATOR, $path);

        return $path;
    }
}
