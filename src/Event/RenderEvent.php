<?php
namespace NeutronStars\Neutrino\Event;

use NeutronStars\Events\Cancellable;
use NeutronStars\Events\Event;

class RenderEvent implements Event, Cancellable
{
    protected string $render;
    protected bool $cancelled = false;

    public function __construct(string $render)
    {
        $this->render = $render;
    }

    public function getRender(): string
    {
        return $this->render;
    }

    public function setRender(string $render): void
    {
        $this->render = $render;
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
