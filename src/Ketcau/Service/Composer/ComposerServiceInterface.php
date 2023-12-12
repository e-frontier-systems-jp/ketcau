<?php

namespace Ketcau\Service\Composer;

interface ComposerServiceInterface
{
    public function execRequire($packageName, $output = null);

    public function execRemove($packageName, $output = null);


    public function execConfig($key, $value = null);

    public function configureRepository();

    public function foreachRequires($packageName, $version, $callback, $typeFilter = null, $level = 0);
}