<?php
namespace NeutronStars\Neutrino\Event;

use NeutronStars\Events\Cancellable;
use NeutronStars\Events\Event;
use NeutronStars\Router\Route;

class CallRouteEvent implements Event, Cancellable
{
    protected Route $route;
    protected bool $cancelled = false;

    public function __construct(Route $route)
    {
        $this->route = $route;
    }

    public function getRoute(): Route
    {
        return $this->route;
    }

    public function setRoute(Route $route): void
    {
        $this->route = $route;
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
