<?php

namespace NFePHP\DUA\Factories;


class Header
{
    /**
     * Return header
     * @param string $namespace
     * @param int $cUF
     * @param string $version
     * @return string
     */
    public static function get($namespace,  $version)
    {
        return "<DuaServiceHeader xmlns=\"$namespace\">"
            . "<versao>$version</versao>"
            . "</DuaServiceHeader>";
    }
}
