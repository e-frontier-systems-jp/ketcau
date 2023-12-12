<?php

namespace Ketcau\Session\Storage\Handler;

use SessionHandlerInterface;
use Skorp\Dissua\SameSite;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\StrictSessionHandler;

class SameSiteNoneCompatSessionHandler extends StrictSessionHandler
{
    private $handler;

    private $doDestroy;

    private $sessionName;

    private $prefetchData;

    private $newSessionId;


    public function __construct(SessionHandlerInterface $handler)
    {

        parent::__construct($handler);
        $this->handler = $handler;

        ini_set('session.cookie_secure', $this->getCookieSecure());
        ini_set('session.cookie_samesite', $this->getCookieSameSite());
        ini_set('session.cookie_path', $this->getCookiePath());
    }

    /**
     * {@inheritDoc}
     */
    public function open($savePath, $sessionName): bool
    {
        $this->sessionName = $sessionName;
        if (!headers_sent() && !ini_get('session.cache_limiter') && '0' !== ini_get('session.cache_expire')) {
            header(sprintf('Cache-Control: max-age=%d, private, must-revalidate', 60 * (int) ini_get('session.cache_expire')));
        }
        return $this->handler->open($savePath, $sessionName);
    }

    public function doRead(#[\SensitiveParameter] string $sessionId): string
    {
        return $this->handler->read($sessionId);
    }


    public function updateTimestamp(#[\SensitiveParameter] string $sessionId, string $data): bool
    {
        return $this->write($sessionId, $data);
    }


    public function doWrite(#[\SensitiveParameter] string $sessionId, string $data): bool
    {
        return $this->handler->write($sessionId, $data);
    }


    public function destroy(#[\SensitiveParameter] string $sessionId): bool
    {
        if (\PHP_VERSION_ID < 70000) {
            $this->prefetchData = null;
        }
        if (!headers_sent() && filter_var(ini_get('session.use_cookies'), FILTER_VALIDATE_BOOLEAN)) {
            if (!$this->sessionName) {
                throw new \LogicException(sprintf('Session name cannot be empty, did you forget to call "parent::open" in "%s"', \get_class($this)));
            }
            $sessionCookie = sprintf(' %s=', urlencode($this->sessionName));
            $sessionCookieWithId = sprintf('%s%s;', $sessionCookie, urlencode($sessionId));
            $sessionCookieFound = false;
            $otherCookies = [];
            foreach (headers_list() as $h) {
                if (0 !== stripos($h, 'Set-Cookie:')) {
                    continue;
                }
                if (11 === strpos($h, $sessionCookie, 11)) {
                    $sessionCookieFound = true;

                    if (11 !== strpos($h, $sessionCookieWithId, 11)) {
                        $otherCookies[] = $h;
                    }
                }
                else {
                    $otherCookies[] = $h;
                }
            }
            if ($sessionCookieFound) {
                header_remove('Set-Cookie');
                foreach ($otherCookies as $h) {
                    header($h, false);
                }
            }
            else {
                if (\PHP_VERSION_ID < 70300) {
                    setcookie($this->sessionName, '', 0, ini_get('session.cookie_path'), ini_get('session.cookie_domain'), filter_var(ini_get('session.cookie_secure'), FILTER_VALIDATE_BOOLEAN), filter_var(ini_get('session.cookie_httponly'), FILTER_VALIDATE_BOOLEAN));
                }
                else {
                    setcookie($this->sessionName, '',
                    [
                        'expire' => 0,
                        'path' => $this->getCookiePath(),
                        'domain' => ini_get('session.cookie_domain'),
                        'secure' => filter_var(ini_get('session.cookie_secure'), FILTER_VALIDATE_BOOLEAN),
                        'httponly' => filter_var(ini_get('session.cookie_httponly'), FILTER_VALIDATE_BOOLEAN),
                        'samesite' => $this->getCookieSameSite(),
                    ]);
                }
            }
        }

        return $this->newSessionId === $sessionId || $this->doDestroy($sessionId);
    }


    public function doDestroy(#[\SensitiveParameter] string $sessionId): bool
    {
        $this->doDestroy = false;
        return $this->handler->destroy($sessionId);
    }


    public function close(): bool
    {
        return $this->handler->close();
    }


    public function gc(int $maxlifetime): int|false
    {
        return $this->handler->gc($maxlifetime);
    }


    public function getCookieSameSite()
    {
        if ($this->shouldSendSameSiteNone() && \PHP_VERSION_ID >= 70300 && $this->getCookieSecure()) {
            return Cookie::SAMESITE_NONE;
        }
        return '';
    }


    public function getCookiePath()
    {
        $cookiePath = env('KETCAU_COOKIE_PATH', '/');
        if ($this->shouldSendSameSiteNone() && \PHP_VERSION_ID < 70300 && $this->getCookieSecure()) {
            return $cookiePath. '; SameSite='. Cookie::SAMESITE_NONE;
        }
        return $cookiePath;
    }


    public function getCookieSecure()
    {
        $request = Request::createFromGlobals();
        return $request->isSecure() ? '1' : '0';
    }


    public function shouldSendSameSiteNone()
    {
        $userAgent = array_key_exists('HTTP_USER_AGENT', $_SERVER) ? $_SERVER['HTTP_USER_AGENT'] : null;
        return SameSite::handle($userAgent);
    }
}