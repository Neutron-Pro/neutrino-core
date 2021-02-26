<?php
namespace NeutronStars\Neutrino\Event;

use eftec\bladeone\BladeOne;
use NeutronStars\Events\Event;

class BladeEvent implements Event
{
    protected BladeOne $bladeOne;

    public function __construct(BladeOne $bladeOne)
    {
        $this->bladeOne = $bladeOne;
    }

    /**
     * @return BladeOne
     */
    public function getBladeOne(): BladeOne
    {
        return $this->bladeOne;
    }

    /**
     * @param BladeOne $bladeOne
     */
    public function setBladeOne(BladeOne $bladeOne): void
    {
        $this->bladeOne = $bladeOne;
    }
}
