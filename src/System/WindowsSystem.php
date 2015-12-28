<?php


namespace Litipk\JupyterPhpInstaller\System;


final class WindowsSystem extends System
{

    /** @return integer */
    public function getOperativeSystem()
    {
        return self::OS_WIN;
    }

    /** @return string */
    public function getCurrentUser()
    {
        if (function_exists('getenv') && false !== getenv('username')) {
            return getenv('username');
        } else {
            throw new \RuntimeException('Unable to obtain the current username.');
        }
    }

    /** @return string */
    public function getCurrentUserHome()
    {
        if (function_exists('getenv') && false !== getenv('HOMEDRIVE') && false !== getenv('HOMEPATH')) {
            return getenv("HOMEDRIVE") . getenv("HOMEPATH");
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
        return true;
    }
}
