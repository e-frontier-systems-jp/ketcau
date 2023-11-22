<?php

namespace Ketcau\Util;

class StringUtil
{
    public static function convertLineFeed($value, $lf = "\n"): string
    {
        if (empty($value)) {
            return '';
        }

        return strtr($value, array_fill_keys(["\r\n", "\r", "\n"], $lf));
    }
}