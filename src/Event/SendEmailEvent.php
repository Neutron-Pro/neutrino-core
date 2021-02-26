<?php
namespace NeutronStars\Neutrino\Event;

use NeutronStars\Events\Cancellable;
use NeutronStars\Events\Event;
use NeutronStars\Neutrino\Core\Email;

class SendEmailEvent implements Event, Cancellable
{
    protected bool $cancelled = false;
    protected Email $email;

    public function __construct(Email $email)
    {
        $this->email = $email;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function setEmail(Email $email): void
    {
        $this->email = $email;
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
