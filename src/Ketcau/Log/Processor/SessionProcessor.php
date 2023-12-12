<?php

namespace Ketcau\Log\Processor;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionProcessor
{
    /**
     * @var SessionInterface
     */
    protected $session;


    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }


    public function __invoke(array $records)
    {
        $records['extra']['session_id'] = 'N/A';

        if(!$this->session->isStarted()) {
            return $records;
        }

        $records['extra']['session_id'] = substr(sha1($this->session->getId()), 0, 8);

        return $records;
    }
}