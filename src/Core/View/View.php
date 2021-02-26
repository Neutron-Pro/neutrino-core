<?php

namespace NeutronStars\Neutrino\Core\View;

use NeutronStars\Neutrino\Core\Kernel;
use Exception;
use NeutronStars\Neutrino\Event\RenderEvent;

class View
{
    private int $engine;
    private string $viewPath;
    private array $params;

    public function __construct(int $engine, string $viewPath, array $params)
    {
        $this->engine = $engine;
        $this->viewPath = $viewPath;
        $this->params = $params;
    }

    /**
     * @param ?string $layout
     * @return string
     * @throws Exception
     */
    public function run(?string $layout = null): string
    {
        $event = null;
        switch ($this->engine) {
            case ViewEngine::BLADE:
                $event = new RenderEvent($this->renderBlade());
                break;
            case ViewEngine::DEFAULT:
                $event = new RenderEvent($this->renderDefault($layout));
                break;
        }
        if ($event != null) {
            Kernel::get()->getEvents()->call('view.render', $event);
            if (!$event->isCancelled()) {
                return $event->getRender();
            }
        }
    }

    /**
     * @return string
     * @throws Exception
     */
    private function renderBlade(): string
    {
        return Kernel::get()->getBlade()->run($this->viewPath, $this->params);
    }

    private function renderDefault(?string $layout = null): string
    {
        $this->params['router'] = Kernel::get()->getRouter();
        ob_start();
        extract($this->params);
        include Kernel::get()->getConfiguration()->get('views', '../templates/views') . '/' . str_replace('.', '/', $this->viewPath) . '.php';
        $view = ob_get_clean();
        if ($layout !== null) {
            ob_start();
            include Kernel::get()->getConfiguration()->get('layouts', '../templates/layouts')  . '/' . str_replace('.', '/', $layout) . '.php';
            $view = ob_get_clean();
        }
        return $view;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function __toString(): string
    {
        return $this->run();
    }
}
