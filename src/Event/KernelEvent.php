<?php
namespace NeutronStars\Neutrino\Event;

use NeutronStars\Events\Event;
use NeutronStars\Neutrino\Core\Kernel;

class KernelEvent implements Event
{
    private Kernel $kernel;

    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
    }

    public function getKernel(): Kernel
    {
        return $this->kernel;
    }
}
