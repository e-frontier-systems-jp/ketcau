<?php

namespace Ketcau\Service;

use Doctrine\ORM\EntityManagerInterface;
use Ketcau\Util\StringUtil;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\DataCollector\MemoryDataCollector;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SystemService implements EventSubscriberInterface
{
    public const MAINTENANCE_TOKEN_KEY = 'maintenance_token';
    public const AUTO_MAINTENANCE = 'auto_maintenance';
    public const AUTO_MAINTENANCE_UPDATE = 'auto_maintenance_update';


    private $disableMaintenanceAfterResponse = false;

    private $maintenanceMode = null;

    protected $entityManager;

    protected $container;


    public function __construct(
        EntityManagerInterface $entityManager,
        ContainerInterface $container
    )
    {
        $this->entityManager = $entityManager;
        $this->container = $container;
    }


    public function getDbVersion()
    {
        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();
        $rsm->addScalarResult('v', 'v');

        $platform = $this->entityManager->getConnection()->getDatabasePlatform()->getName();
        switch($platform) {
            case 'sqlite':
                $prefix = 'SQLite version ';
                $func = 'sqlite_version()';
                break;

            case 'mysql':
                $prefix = 'MySQL ';
                $func = 'version()';

            case 'pgsql':
            default:
                $prefix = '';
                $func = 'version()';
        }

        $version = $this->entityManager
            ->createNativeQuery('SELECT '. $func. ' AS v', $rsm)
            ->getSingleScalarResult();

        return $prefix. $version;
    }


    public function canSetMemoryLimit($memory)
    {
        try {
            $ret = ini_set('memory_limit', $memory);
        } catch (\Exception $exception) {
            return false;
        }

        return !($ret === false);
    }


    public function getMemoryLimit()
    {
        $memoryLimit = (new MemoryDataCollector())->getMemoryLimit();
        if (-1 == $memoryLimit) {
            return -1;
        }

        return ($memoryLimit == 0) ? 0 : ($memoryLimit / 1024) / 1024;
    }


    public function switchMaintenance($isEnable = false, $mode = self::AUTO_MAINTENANCE, bool $force = false)
    {
        if ($isEnable) {
            $this->enableMaintenance($mode, $force);
        }
        else {
            $this->disableMaintenance($mode, $force);;
        }
    }


    public function getMaintenanceToken()
    {
        $path = $this->container->getParameter('ketcau_content_maintenance_file_path');
        if (!file_exists($path)) {
            return null;
        }

        $contents = file_get_contents($path);

        return explode(':', $contents)[1] ?? null;
    }


    public function disableMaintenanceEvent(TerminateEvent $event)
    {
        if ($this->disableMaintenanceAfterResponse) {
            $this->switchMaintenance(false, $this->maintenanceMode);
        }
    }

    public function enableMaintenance($mode = self::AUTO_MAINTENANCE, bool $force = false): void
    {
        if ($force || !$this->isMaintenanceMode()) {
            $path = $this->container->getParameter('ketcau_content_maintenance_file_path');
            $token = StringUtil::random(32);
            file_put_contents($path, "{$mode}:{$token}");
        }
    }


    public function disableMaintenance($mode = self::AUTO_MAINTENANCE): void
    {
        $this->disableMaintenanceAfterResponse = true;
        $this->maintenanceMode = $mode;
    }


    public function disableMaintenanceNow($mode = self::AUTO_MAINTENANCE, bool $force = false): void
    {
        if (!$this->isMaintenanceMode()) {
            return;
        }

        $path = $this->container->getParameter('ketcau_content_maintenance_file_path');
        $contents = file_get_contents($path);
        $currentMode = explode(':', $contents)[0] ?? null;

        if ($force || $currentMode === $mode) {
            unlink($path);
        }
    }


    public function isMaintenanceMode(): bool
    {
        return file_exists($this->container->getParameter('ketcau_content_maintenance_file_path'));
    }


    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::TERMINATE => 'disableMaintenanceEvent'];
    }
}