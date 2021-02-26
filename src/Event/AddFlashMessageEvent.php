<?php
namespace NeutronStars\Neutrino\Event;

use NeutronStars\Events\Cancellable;
use NeutronStars\Events\Event;
use NeutronStars\FlashSession\FlashMessage;

class AddFlashMessageEvent implements Event, Cancellable
{
    protected FlashMessage $flashMessage;
    protected bool $cancelled = false;

    public function __construct(FlashMessage $flashMessage)
    {
        $this->flashMessage = $flashMessage;
    }

    public function getFlashMessage(): FlashMessage
    {
        return $this->flashMessage;
    }

    public function setFlashMessage(FlashMessage $flashMessage): void
    {
        $this->flashMessage = $flashMessage;
    }

    public function isCancelled(): bool
    {
        return $this->cancelled;
    }

    public function setCancelled(bool $cancelled): void
    {
        $this->cancelled = $cancelled;
    }
}
