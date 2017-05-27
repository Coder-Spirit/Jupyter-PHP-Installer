<?php


namespace Litipk\JupyterPhpInstaller\System;


final class BsdSystem extends UnixSystem
{
    public function getOperativeSystem(): int
    {
        return self::OS_BSD;
    }
}
