<?php


namespace Litipk\JupyterPhpInstaller\System;


final class BsdSystem extends UnixSystem
{
    /** @return integer */
    public function getOperativeSystem()
    {
        return self::OS_BSD;
    }
}
