<?php


namespace Litipk\JupyterPhpInstaller\System;


final class MacSystem extends UnixSystem
{
    /** @return integer */
    public function getOperativeSystem()
    {
        return self::OS_OSX;
    }
}
