<?php


namespace Litipk\JupyterPhpInstaller\System;


final class LinuxSystem extends UnixSystem
{
    public function getOperativeSystem(): int
    {
        return self::OS_LINUX;
    }
}
