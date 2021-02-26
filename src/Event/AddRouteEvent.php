<?php
namespace NeutronStars\Neutrino\Event;

use NeutronStars\Events\Cancellable;
use NeutronStars\Events\Event;
use NeutronStars\Router\Route;

class AddRouteEvent implements Event, Cancellable
{
    protected string $name;
    protected array $routeInfo;
    protected bool $cancelled = false;

    public function __construct(string $name, array $routeInfo)
    {
        $this->name = $name;
        $this->routeInfo = $routeInfo;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getRouteInfo(): array
    {
        return $this->routeInfo;
    }

    public function setRouteInfo(array $routeInfo): void
    {
        $this->routeInfo = $routeInfo;
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
