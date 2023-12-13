<?php

namespace Ketcau\Controller\Admin\Setting\System;

use Ketcau\Common\Constant;
use Ketcau\Service\SystemService;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SystemController
{
    public function __construct(
        protected SystemService $systemService
    )
    {}


    /**
     * @Route("/%ketcau_admin_route%/setting/system/system", name="admin_setting_system_info", methods={"GET"})
     *
     * @param Request $request
     * @return array[]
     */
    #[Template("@admin/Setting/Setting/system.twig")]
    public function index(Request $request): array
    {
        $info = [];

        // KETCAUバージョン
        $info[] = [
            'title' => trans('admin.setting.system.system.ketcau'),
            'value' => Constant::VERSION,
        ];
        // SERVER OS
        $info[] = [
            'title' => trans('admin.setting.system.system.server_os'),
            'value' => php_uname(),
        ];
        // DATABASE SERVER
        $info[] = [
            'title' => trans('admin.setting.system.system.database_server'),
            'value' => $this->systemService->getDbVersion(),
        ];
        // WEB SERVER
        $info[] = [
            'title' => trans('admin.setting.system.system.web_server'),
            'value' => $request->server->get('SERVER_SOFTWARE'),
        ];
        // USER AGENT
        $info[] = [
            'title' => trans('admin.setting.system.system.user_agent'),
            'value' => $request->headers->get('User-Agent'),
        ];
        // PHP VERSION (EXTENSIONS)
        $info[] = [
            'title' => trans('admin.setting.system.system.php'),
            'value' => phpversion(). ' ('. implode(', ', get_loaded_extensions()). ')',
        ];

        return [
            'info' => $info,
        ];
    }


    /**
     * @Route("/%ketcau_admin_route%/setting/system/system/phpinfo", name="admin_setting_system_system_phpinfo", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function phpinfo(Request $request): Response
    {
        ob_start();
        phpinfo();
        $phpinfo = ob_get_contents();
        ob_end_clean();

        return new Response($phpinfo);
    }
}