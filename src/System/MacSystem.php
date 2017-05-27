<?php


namespace Litipk\JupyterPhpInstaller\System;


final class MacSystem extends UnixSystem
{
    public function getOperativeSystem(): int
    {
        return self::OS_OSX;
    }
}
