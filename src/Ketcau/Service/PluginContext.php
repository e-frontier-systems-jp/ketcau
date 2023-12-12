<?php

namespace Ketcau\Service;

use Ketcau\Common\KetcauConfig;
use Ketcau\Exception\PluginException;

class PluginContext
{
    private const MODE_INSTALL = 'install';
    private const MODE_UNINSTALL = 'uninstall';


    private $mode;

    private $code;

    private $composerJson;

    private $ketcauConfig;


    public function __construct(KetcauConfig $ketcauConfig)
    {
        $this->ketcauConfig = $ketcauConfig;
    }


    public function isInstall()
    {
        return $this->mode === self::MODE_INSTALL;
    }

    public function isUninstall()
    {
        return $this->mode === self::MODE_UNINSTALL;
    }

    public function setInstall()
    {
        return $this->mode = self::MODE_INSTALL;
    }

    public function setUninstall()
    {
        return $this->mode = self::MODE_UNINSTALL;
    }

    public function setCode(string $code)
    {
        $this->code = $code;
    }

    public function getComposerJson(): array
    {
        if ($this->composerJson) {
            return $this->composerJson;
        }

        $projectRoot = $this->ketcauConfig->get('kernel.project_dir');
        $composerJsonPath = $projectRoot. '/app/Plugin/'. $this->code. '/composer.json';
        if (file_exists($composerJsonPath) === false) {
            throw new PluginException("${composerJsonPath} not found.");
        }
        $this->composerJson = json_decode(file_get_contents($composerJsonPath), true);
        if ($this->composerJson === null) {
            throw new PluginException("Invalid json format. [${composerJsonPath}]");
        }

        return $this->composerJson;
    }

    public function getExtraEntityNamespace(): array
    {
        $json = $this->getComposerJson();
        if (isset($json['extra'])) {
            if (array_key_exists('entity-namespace', $json['extra'])) {
                return $json['extra']['entity-namespace'];
            }
        }

        return [];
    }
}