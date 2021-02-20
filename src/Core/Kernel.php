<?php
namespace NeutronStars\Neutrino\Core;

use eftec\bladeone\BladeOne;
use NeutronStars\Neutrino\Core\View\BladeOneView;
use NeutronStars\Neutrino\Exception\KernelException;
use NeutronStars\Router\Router;
use ReflectionException;
use ReflectionMethod;

class Kernel
{
    private static ?self $instance = null;

    /**
     * @param Configuration $configuration
     * @param Router $router
     * @throws KernelException
     */
    public static function create(Configuration $configuration, Router $router)
    {
        if (self::$instance !== null) {
            throw new KernelException('The kernel is already initialized !');
        }
        self::$instance = new self($configuration, $router);
    }

    public static function get(): self
    {
        return self::$instance;
    }

    private Configuration $configuration;
    private Router $router;
    private ?BladeOne $bladeOne = null;

    private function __construct(Configuration $configuration, Router $router)
    {
        $this->configuration = $configuration;
        $this->router = $router;
    }

    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }

    public function getRouter(): Router
    {
        return $this->router;
    }

    public function getBlade(): BladeOne
    {
        if ($this->bladeOne === null) {
            $this->bladeOne = new BladeOneView($this->router, [
                $this->configuration->get('views', '../templates/views'),
                $this->configuration->get('layouts', '../templates/layouts')
            ], $this->configuration->get('bladeCache', '../var/cache'));
        }
        return $this->bladeOne;
    }

    public function registerRoutes(Configuration $routes): void
    {
        $routes->forEach(function ($key, $value) {
            $this->router->add($key, $value);
        });
    }

    /**
     * @throws ReflectionException
     */
    public function handle(): void
    {
        $route = $this->router->find($params);
        if ($route != null) {
            $route->setSelected(true);
            $controller = $route->getController();
            $controller = new $controller();
            $reflection = new ReflectionMethod($controller, $route->getCallMethod());
            $reflection->invoke($controller, ...$params);
        }
    }
}