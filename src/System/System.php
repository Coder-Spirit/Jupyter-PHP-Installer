<?php


namespace Litipk\JupyterPhpInstaller\System;


abstract class System
{
    const OS_UNKNOWN = 0;
    const OS_LINUX   = 1;
    const OS_OSX     = 2;
    const OS_BSD     = 3;
    const OS_WIN     = 4;


    /** @return System */
    public static function getSystem()
    {
        $phpOs = self::guessOperativeSystem();

        if (self::OS_LINUX === $phpOs) {
            return new LinuxSystem();
        } elseif (self::OS_BSD === $phpOs) {
            return new BsdSystem();
        } elseif (self::OS_OSX === $phpOs) {
            return new MacSystem();
        } elseif (self::OS_WIN === $phpOs) {
            return new WindowsSystem();
        } else {
            throw new \RuntimeException('This platform is unknown for the installer');
        }
    }

    /** @return integer */
    public abstract function getOperativeSystem();

    /** @return string */
    public abstract function getCurrentUser();

    /** @return string */
    public abstract function getCurrentUserHome();

    /**
     * @param string $cmdName
     * @return boolean
     */
    public abstract function checkIfCommandExists($cmdName);

    /** @return integer */
    private static function guessOperativeSystem()
    {
        $phpOS = strtolower(PHP_OS);

        if ('linux' === $phpOS) {
            return self::OS_LINUX;
        } elseif ('darwin' === $phpOS) {
            return self::OS_OSX;
        } elseif (in_array($phpOS, ['windows', 'winnt', 'win32'])) {
            return self::OS_WIN;
        } elseif (in_array($phpOS, ['freebsd', 'netbsd', 'openbsd'])) {
            return self::OS_BSD;
        } else {
            return self::OS_UNKNOWN;
        }
    }
}
