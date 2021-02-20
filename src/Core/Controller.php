<?php

namespace NeutronStars\Neutrino\Core;

use NeutronStars\Neutrino\Core\View\View;
use NeutronStars\Neutrino\Core\View\ViewEngine;
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
        echo $object;
    }

    protected function renderText($text): void
    {
        $this->setContentType(ContentType::TEXT_PLAIN);
        echo $text;
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
        $this->setCode(HTTPCode::CODE_404);
        $this->render('app.404');
        die;
    }

    protected function redirect(string $route, $params = []): void
    {
        header('Location: ' . Kernel::get()->getRouter()->get($route, $params));
        die;
    }

    protected function createEmail(): Email
    {
        return new Email();
    }
}
