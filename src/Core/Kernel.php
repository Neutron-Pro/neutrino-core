<?php
namespace NeutronStars\Neutrino\Core;

use eftec\bladeone\BladeOne;
use NeutronStars\Database\Database;
use NeutronStars\Events\Events;
use NeutronStars\FlashSession\FlashSession;
use NeutronStars\Neutrino\Core\View\BladeOneView;
use NeutronStars\Neutrino\Event\AddRouteEvent;
use NeutronStars\Neutrino\Event\BladeEvent;
use NeutronStars\Neutrino\Event\CallRouteEvent;
use NeutronStars\Neutrino\Event\DatabaseEvent;
use NeutronStars\Neutrino\Event\FlashSessionEvent;
use NeutronStars\Neutrino\Event\KernelEvent;
use NeutronStars\Neutrino\Exception\KernelException;
use NeutronStars\Router\Router;
use PDO;
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
    private Events $events;
    private Router $router;

    private ?BladeOne $bladeOne = null;
    private ?Database $database = null;
    private ?FlashSession $flashSession = null;

    private function __construct(Configuration $configuration, Router $router)
    {
        $this->configuration = $configuration;
        $this->events = new Events();
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

    public function getEvents(): Events
    {
        return $this->events;
    }

    public function getBlade(): BladeOne
    {
        if ($this->bladeOne === null) {
            $event = new BladeEvent(new BladeOneView($this->router, [
                $this->configuration->get('views', '../templates/views'),
                $this->configuration->get('layouts', '../templates/layouts')
            ], $this->configuration->get('bladeCache', '../var/cache')));
            $this->events->call('blade.init', $event);
            $this->bladeOne = $event->getBladeOne();
        }
        return $this->bladeOne;
    }

    /**
     * @return Database
     */
    public function getDatabase(): Database
    {
        if ($this->database === null) {
            $event = new DatabaseEvent(new Database(
                $this->configuration->get('database.dbname'),
                [
                    'url' => $this->configuration->get('database.host', '127.0.0.1'),
                    'port' => $this->configuration->get('database.port', 3306),
                    'user' => $this->configuration->get('database.user', ''),
                    'password' => $this->configuration->get('database.password', ''),
                    'charset' => $this->configuration->get('database.charset', 'utf8mb4'),
                    'fetchMode' => $this->configuration->get('database.mode.fetch', PDO::FETCH_OBJ),
                    'errorMode' => $this->configuration->get('database.mode.error', PDO::ERRMODE_WARNING),
                ]
            ));
            $this->events->call('database.init', $event);
            $this->database = $event->getDatabase();
        }
        return $this->database;
    }

    /**
     * @return FlashSession
     */
    public function getFlashSession(): FlashSession
    {
        if ($this->flashSession === null) {
            $event = new FlashSessionEvent(new FlashSession('_flashes_bag'));
            $this->events->call('flash_session.init', $event);
            $this->flashSession = $event->getFlashSession();
        }
        return $this->flashSession;
    }

    public function registerRoutes(Configuration $routes): self
    {
        $routes->forEach(function ($key, $value) {
            $event = new AddRouteEvent($key, $value);
            $this->events->call('route.add', $event);
            if (!$event->isCancelled()) {
                $this->router->add($key, $value);
            }
        });
        return $this;
    }

    public function registerListeners(Configuration $listeners): self
    {
        $listeners->forEach(function ($key, $value) {
            $this->events->registers(['name' => $key] + $value);
        });
        return $this;
    }

    /**
     * @throws ReflectionException
     */
    public function handle(): void
    {
        $route = $this->router->find($params);
        if ($route != null) {
            $event = new CallRouteEvent($route);
            $this->events->call('route.call', $event);
            if (!$event->isCancelled()) {
                $route = $event->getRoute();
                $route->setSelected(true);
                $controller = $route->getController();
                $controller = new $controller();
                $reflection = new ReflectionMethod($controller, $route->getCallMethod());
                $reflection->invoke($controller, ...$params);
                $this->die();
            }
        }
    }

    public function die($callback = null): void
    {
        $this->events->call('kernel.die', new KernelEvent($this));

        if ($this->flashSession !== null) {
            $this->flashSession->saveMessages();
        }
        if($callback !== null) {
            $callback();
        }
        die;
    }
}
