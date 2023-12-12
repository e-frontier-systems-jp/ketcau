<?php

namespace Ketcau\Service\Composer;

class OutputParser
{
    public static function parseRequire($output)
    {
        $rowArray = explode(PHP_EOL, str_replace('\r\n', PHP_EOL, $output));
        $installedLogs = array_filter(
            array_map(
                function ($line) {
                    $matches = [];
                    preg_match('/^  - Installing (.*?) \((.*?)\) .*/', $line, $matches);
                    return $matches;
                },
                $rowArray
            )
        );
        return ['installed' => array_column($installedLogs, 2, 1)];
    }


    public static function parseInfo($output)
    {
        $rowArray = explode(PHP_EOL, str_replace('\r\n', PHP_EOL, $output));
        $infoLogs = array_filter(array_map(function ($line) {
            $matches = [];
            preg_match('/^(name|descrip.|keywords|versions|type|license|source|dist|names)\s*:\s*(.*)$/', $line, $matches);
            return $matches;
        }, $rowArray));

        $result = array_column($infoLogs, 2, 1);
        $result['requires'] = static::parseArrayInfoOutput($rowArray, 'requires');
        $result['requires (dev)'] = static::parseArrayInfoOutput($rowArray, 'requires (dev)');

        return $result;
    }


    public static function parseConfig($output)
    {
        $rowArray = explode(PHP_EOL, str_replace('\r\n', PHP_EOL, $output));
        $rowArray = array_filter($rowArray, function ($line) {
            return !preg_match('/^<warning>.*/', $line);
        });
        return $rowArray ? json_decode(array_shift($rowArray), true) : [];
    }


    public static function parseList($output)
    {
        $rowArray = explode(PHP_EOL, str_replace('\r\n', PHP_EOL, $output));
        $rowConfig = array_map(function ($line) {
            $matches = [];
            preg_match('/^\[(.*?)\]\s?(.*)$', $line, $matches);
            return $matches;
        }, $rowArray);

        $rowConfig = array_column($rowConfig, 2, 1);

        $result = [];

        foreach ($rowConfig as $path => $value) {
            $arr = &$result;
            $keys = explode('.', $path);
            foreach ($keys as $key) {
                $arr = &$arr[$key];
            }
            $arr = $value;
        }

        return $result;
    }


    private static function parseArrayInfoOutput($rowArray, $key)
    {
        $result = [];
        $start = false;
        foreach ($rowArray as $line) {
            if ($line === $key) {
                $start = true;
                continue;
            }
            if ($start) {
                if (empty($line)) {
                    break;
                }
                $parts = explode(' ', $line);
                $result[$parts[0]] = $parts[1];
            }
        }
        return $result;
    }


    public function parseComposerVersion($output)
    {
        $rowArray = explode(PHP_EOL, str_replace('\r\n', PHP_EOL, $output));
        $rowArray = array_filter($rowArray, function ($line) {
            return preg_match('/^Composer */', $line);
        });
        return array_shift($rowArray);
    }
}