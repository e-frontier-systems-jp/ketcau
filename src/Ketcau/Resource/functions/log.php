<?php

use Ketcau\DependencyInjection\Facade\LoggerFacade;

function log_emergency($message, array $context = [])
{
    $logger = LoggerFacade::create();
    $logger->emergency($message, $context);
}

function log_alert($message, array $context = [])
{
    $logger = LoggerFacade::create();
    $logger->alert($message, $context);
}

function log_critical($message, array $context = [])
{
    $logger = LoggerFacade::create();
    $logger->critical($message, $context);
}

function log_error($message, array $context = [])
{
    $logger = LoggerFacade::create();
    $logger->error($message, $context);
}

function log_warning($message, array $context = [])
{
    $logger = LoggerFacade::create();
    $logger->warning($message, $context);
}

function log_notice($message, array $context = [])
{
    $logger = LoggerFacade::create();
    $logger->notice($message, $context);
}

function log_info($message, array $context = [])
{
    $logger = LoggerFacade::create();
    $logger->info($message, $context);
}

function log_debug($message, array $context = [])
{
    $logger = LoggerFacade::create();
    $logger->debug($message, $context);
}

function logs($channel)
{
    return LoggerFacade::getLoggerBy($channel);
}
