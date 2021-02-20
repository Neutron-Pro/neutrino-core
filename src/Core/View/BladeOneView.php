<?php
namespace NeutronStars\Neutrino\Core\View;

use eftec\bladeone\BladeOne;
use NeutronStars\Router\Router;

class BladeOneView extends BladeOne
{
    private Router $router;

    public function __construct(Router $router, $templatePath = null, $compiledPath = null, $mode = 0)
    {
        parent::__construct($templatePath, $compiledPath, $mode);
        $this->router = $router;
        $this->addDirectives();
    }

    private function addDirectives(): void
    {
        $this->directive('router', function (string $query): string {
            return '<?= $this->getRoute(' . $query . ') ?>';
        });
        $this->directive('isRoute', function (string $query):string {
            return '<?php if($this->isRoute('.$query.')): ?>';
        });
        $this->directive('classRoute', function (string $query): string {
            return '<?= $this->getClassRoute('.$query.') ?>';
        });
    }

    protected function getRoute(string $name, array $params = [], bool $fullPath = false): string
    {
        return $this->router->get($name, $params, $fullPath);
    }

    public function getClassRoute(string $name, string $class = 'selected', bool $strict = true): string
    {
        return $this->isRoute($name, $strict) ? $class : '';
    }

    protected function isRoute(string $name, bool $strict = true): bool
    {
        return $this->router->isRoute($name, $strict);
    }
}
