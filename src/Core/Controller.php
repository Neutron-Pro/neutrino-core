<?php

namespace NeutronStars\Neutrino\Core;

use NeutronStars\FlashSession\FlashMessage;
use NeutronStars\Form\Form;
use NeutronStars\Neutrino\Core\View\View;
use NeutronStars\Neutrino\Core\View\ViewEngine;
use NeutronStars\Neutrino\Event\AddFlashMessageEvent;
use NeutronStars\Neutrino\Event\RenderEvent;
use NeutronStars\Neutrino\HTTP\ContentType;
use NeutronStars\Neutrino\HTTP\HTTPCode;

abstract class Controller
{
    protected function renderBlade(string $view, array $params = []): void
    {
        echo (new View(ViewEngine::BLADE, $view, $params));
    }

    protected function renderPHP(string $view, array $params = [], string $layout = 'index'): void
    {
        echo (new View(ViewEngine::DEFAULT, $view, $params))->run($layout);
    }

    protected function render(string $view, array $params = [], string $layout = 'index'): void
    {
        if (Kernel::get()->getConfiguration()->get('viewEngine', ViewEngine::DEFAULT) === ViewEngine::BLADE) {
            $this->renderBlade($view, $params);
        } else {
            $this->renderPHP($view, $params, $layout);
        }
    }

    /**
      * @param string|array|Object $object
      */
    protected function renderJSON($object): void
    {
        if (!is_string($object)) {
            $object = json_encode($object);
        }
        $this->setContentType(ContentType::APPLICATION_JSON);
        $this->sendResponse($object);
    }

    protected function renderText($text): void
    {
        $this->setContentType(ContentType::TEXT_PLAIN);
        $this->sendResponse($text);
    }

    protected function sendResponse(string $response)
    {
        $event = new RenderEvent($response);
        Kernel::get()->getEvents()->call('view.render', $event);
        if (!$event->isCancelled()) {
            echo $event->getRender();
        }
    }

    protected function setCode(string $code): void
    {
        header('HTTP/1.0 ' . $code);
    }

    protected function setContentType(string $contentType, string $charset = 'utf8'): void
    {
        header('Content-Type: '.$contentType.';charset='.$charset);
    }

    protected function page404(): void
    {
        Kernel::get()->die(function () {
            $this->setCode(HTTPCode::CODE_404);
            $this->render('app.404');
        });
    }

    protected function redirect(string $route, $params = []): void
    {
        Kernel::get()->die(function () use ($route, $params) {
            header('Location: ' . Kernel::get()->getRouter()->get($route, $params));
        });
    }

    protected function createEmail(): Email
    {
        return new Email();
    }

    protected function createForm(
        array $values = [], string $action = '',
        string $method = 'POST', $secureXSRF = true
    ): Form {
        return new Form($values, $action, $method, $secureXSRF);
    }

    protected function getFlash(string ...$keys): array
    {
        return Kernel::get()->getFlashSession()->flashes(...$keys);
    }

    protected function addFlash(string $type, string $message): void
    {
        $event = new AddFlashMessageEvent(new FlashMessage($type, $message));
        Kernel::get()->getEvents()->call('flash_session.add', $event);
        if (!$event->isCancelled()) {
            Kernel::get()->getFlashSession()->add($type, new FlashMessage($type, $message));
        }
    }
}
