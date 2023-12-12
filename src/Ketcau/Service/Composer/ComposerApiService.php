<?php

namespace Ketcau\Service\Composer;

use Composer\Console\Application;
use Ketcau\Common\KetcauConfig;
use Ketcau\Exception\PluginException;
use Ketcau\Service\PluginContext;
use Ketcau\Service\SchemaService;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class ComposerApiService implements ComposerServiceInterface
{
    protected $ketcauConfig;

    private $consoleApplication;

    private $workingDir;

    private $schemaService;

    private $pluginContext;


    public function __construct(
        KetcauConfig $ketcauConfig,
        SchemaService $schemaService,
        PluginContext $pluginContext
    )
    {
        $this->ketcauConfig = $ketcauConfig;
        $this->schemaService = $schemaService;
        $this->pluginContext = $pluginContext;
    }



    public function execInfo($pluginName, $version)
    {
        $output = $this->runCommand([
            'command' => 'info',
            'package' => $pluginName,
            'version' => $version,
            '--available' => true,
        ]);
        return OutputParser::parseInfo($output);
    }

    public function execRequire($packageName, $output = null, $from = null)
    {
        $packageName = explode(' ', trim($packageName));

        $this->init($packageName, $from);
        $this->execConfig('allow-plugins.symfony/flex', ['false']);

        try {
            return $this->runCommand([
                'command' => 'require',
                'packages' => $packageName,
                '--no-interaction' => true,
                '--profile' => true,
                '--prefer-dist' => true,
                '--update-with-dependencies' => true,
                '--no-scripts' => true,
                '--update-no-dev' => env('APP_ENV') === 'prod',
            ], $output, false);
        }
        finally {
            $this->execConfig('allow-plugins.symfony/flex', ['true']);
        }
    }

    public function execRemove($packageName, $output = null)
    {
        $this->dropTableToExtra($packageName);

        $packageName = explode(' ', trim($packageName));

        $this->init();
        $this->execConfig('allow-plugins.symfony/flex', ['false']);

        try {
            return $this->runCommand([
                'command' => 'remove',
                'packages' => $packageName,
                '--ignore-platform-reqs' => true,
                '--no-interaction' => true,
                '--profile' => true,
                '--no-scripts' => true,
                '--update-no-dev' => env('APP_ENV') === 'prod',
            ], $output, false);
        }
        finally {
            $this->execConfig('allow-plugins.symfony/flex', ['true']);
        }
    }

    public function execConfig($key, $value = null)
    {
        $commands = [
            'command' => 'config',
            'setting-key' => $key,
            'setting-value' => $value,
            '--no-interaction' => true,
        ];
        if ($value) {
            $commands['setting-value'] = $value;
        }
        $output = $this->runCommand($commands, null, false);
        return OutputParser::parseConfig($output);
    }

    public function configureRepository()
    {
        // TODO: Implement configureRepository() method.
    }

    public function foreachRequires($packageName, $version, $callback, $typeFilter = null, $level = 0)
    {
        if (!str_contains($packageName, '/')) {
            return;
        }

        $info = $this->execInfo($packageName, $version);
        if (isset($info['requires'])) {
            foreach ($info['requires'] as $name => $version) {
                if (isset($info['type']) && $info['type'] === $typeFilter) {
                    $this->foreachRequires($name, $version, $callback, $typeFilter, $level + 1);
                    if (isset($info['descrip.'])) {
                        $info['description'] = $info['descrip.'];
                    }
                    if ($level) {
                        $callback($info);
                    }
                }
            }
        }
    }


    public function getConfig()
    {
        $output = $this->runCommand([
            'command' => 'config',
            '--list' => true,
        ], null, false);

        return OutputParser::parseList($output);
    }


    public function setWorkingDir($workingDir)
    {
        $this->workingDir = $workingDir;
    }

    public function runCommand($commands, $output = null, $init = true)
    {
        if ($init) {
            $this->init();
        }
        $commands['--working-dir'] = $this->workingDir;
        $commands['--no-ansi'] = true;
        $input = new ArrayInput($commands);
        $useBufferedOutput = $output === null;

        if ($useBufferedOutput) {
            $output = new BufferedOutput();
            ob_start(function ($buffer) use ($output) {
                $output->write($buffer);
                return null;
            });
        }

        $exitCode = $this->consoleApplication->run($input, $output);

        if ($useBufferedOutput) {
            ob_end_clean();
            $log = $output->fetch();
            if ($exitCode) {
                log_error($log);
                throw new PluginException($log);
            }
            log_info($log, $commands);

            return $log;
        }
        elseif ($exitCode) {
            throw new PluginException();
        }

        return null;
    }


    private function init($packageName = [], $from = null)
    {
        set_time_limit(0);

        $composerMemory = $this->ketcauConfig['ketcau_composer_memory_limit'];
        ini_set('memory_limit', $composerMemory);

        putenv('COMPOSER_HOME='. $this->ketcauConfig['plugin_realdir']. '/.composer');
        $this->initConsole();
        $this->workingDir = $this->workingDir ?: $this->ketcauConfig['kernel.project_dir'];

        $this->initConsole();
    }

    private function initConsole()
    {
        $consoleApplication = new Application();
        $consoleApplication->resetComposer();
        $consoleApplication->setAutoExit(false);
        $this->consoleApplication = $consoleApplication;
    }

    private function dropTableToExtra($packageNames)
    {
        $projectRoot = $this->ketcauConfig->get('kernel.project_dir');

        foreach (explode(' ', trim($packageNames)) as $packageName) {
            $pluginCode = null;

            foreach (glob($projectRoot. '/app/Plugin/*', GLOB_ONLYDIR) as $dir) {
                if (strtolower(basename($dir)) === strtolower(basename($packageName))) {
                    $pluginCode = basename($dir);
                    break;
                }
            }
            if ($pluginCode === null) {
                throw new PluginException($packageName. ' not found');
            }

            $this->pluginContext->setCode($pluginCode);
            $this->pluginContext->setUninstall();

            foreach ($this->pluginContext->getExtraEntityNamespace() as $namespace) {
                $this->schemaService->dropTable($namespace);
            }
        }
    }
}