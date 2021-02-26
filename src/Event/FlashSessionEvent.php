<?php
namespace NeutronStars\Neutrino\Event;

use NeutronStars\Events\Event;
use NeutronStars\FlashSession\FlashSession;

class FlashSessionEvent implements Event
{
    protected FlashSession $flashSession;

    public function __construct(FlashSession $flashSession)
    {
        $this->flashSession = $flashSession;
    }

    /**
     * @return FlashSession
     */
    public function getFlashSession(): FlashSession
    {
        return $this->flashSession;
    }

    /**
     * @param FlashSession $flashSession
     */
    public function setFlashSession(FlashSession $flashSession): void
    {
        $this->flashSession = $flashSession;
    }
}
