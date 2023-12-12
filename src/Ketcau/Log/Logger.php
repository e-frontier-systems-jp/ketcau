<?php

namespace Ketcau\Log;

use Ketcau\Request\Context;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;

class Logger extends AbstractLogger
{
    protected $context;

    protected $logger;

    protected $frontLogger;

    protected $adminLogger;



    public function __construct(
        Context $context,
        LoggerInterface $logger,
        LoggerInterface $frontLogger,
        LoggerInterface $adminLogger,
    )
    {
        $this->context = $context;
        $this->logger = $logger;
        $this->frontLogger = $frontLogger;
        $this->adminLogger = $adminLogger;
    }


    public function log($level, \Stringable|string $message, array $context = []): void
    {
        if ($this->context->isAdmin()) {
            $this->adminLogger->log($level, $message, $context);
        }
        elseif ($this->context->isFront()) {
            $this->frontLogger->log($level, $message, $context);
        }
        else {
            $this->logger->log($level, $message, $context);
        }
    }
}