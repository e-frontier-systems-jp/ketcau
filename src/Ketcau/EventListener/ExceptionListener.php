<?php

namespace Ketcau\EventListener;

use Ketcau\Common\KetcauConfig;
use Ketcau\Request\Context;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class ExceptionListener implements EventSubscriberInterface
{
    public function __construct(
        private Environment $twig,
        protected Context $requestContext,
        protected KetcauConfig $ketcauConfig
    )
    {}


    public function onKernelException(ExceptionEvent $event): void
    {
        if ($this->ketcauConfig->get('kernel.debug')) {
            return;
        }

        $title = trans('exception.error_title');
        $message = trans('exception.error_message');
        $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;

        $exception = $event->getThrowable();

        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
            switch ($statusCode) {
                case 400:
                case 401:
                case 403:
                case 405:
                case 406:
                    $infoMessage = 'アクセスできません。';
                    $title = trans('exception.error_title_can_not_access');
                    if ($exception->getMessage()) {
                        $message = $exception->getMessage();
                    } else {
                        $message = trans('exception.error_message_can_not_access');
                    }
                    break;
                case 429:
                    $infoMessage = '試行回数の制限を超過しました。';
                    $title = trans('exception.error_title_can_not_access');
                    $message = trans('exception.error_message_rate_limit');
                    break;
                case 404:
                    $infoMessage = 'ページがみつかりません。';
                    $title = trans('exception.error_title_not_found');
                    $message = trans('exception.error_message_not_found');
                    break;
                default:
                    break;
            }
        }

        if (isset($infoMessage)) {
            log_info($infoMessage, [
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine(),
            ]);
        }
        else {
            log_error('システムエラーが発生しました。', [
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine(),
                $exception->getTraceAsString(),
            ]);
        }

        try {
            $file = $this->requestContext->isAdmin() ? '@admin/error.twig' : 'error.twig';
            $content = $this->twig->render($file, [
                'error_title' => $title,
                'error_message' => $message,
            ]);
        } catch (\Exception $ex) {
            $content = $title;
        }

        $event->setResponse(new Response($content, $statusCode));
    }


    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException'],
        ];
    }
}