<?php

namespace AllyTalks\WebApp;

use AllyTalks\WebApp\Controller\ControllerInterface;
use AllyTalks\WebApp\Controller\Render;
use AllyTalks\WebApp\Controller\Test;
use Silex\Application as SilexApp;
use Silex\Provider\TwigServiceProvider;

class Application extends SilexApp
{
    /** @var  \Twig_Environment */
    private $twig;

    private $model;

    /** @var ControllerInterface[] */
    private $controllers = [];

    public function __construct()
    {
        parent::__construct();

        $this->model = new Model();

        $this->registerComponents();

        $this->registerControllers();
        $this->attachControllers();
    }

    private function registerComponents()
    {
        $this->register(
            new TwigServiceProvider(),
            [
                'twig.path' => __DIR__.'/views',
            ]
        );

        $this->twig = $this['twig'];
    }

    private function registerControllers()
    {
        $this->controllers = [
            new Test($this),
            new Render($this),
        ];
    }

    private function attachControllers()
    {
        foreach ($this->controllers as $i => $controller) {

            foreach ($controller->getRoutes() as $methodName => $route) {
                $method = $route['method'];
                $route = $route['route'];

                $this->$method($route, [$this->controllers[$i], $methodName]);
            }
        }
    }

    public function getTwig() : \Twig_Environment
    {
        return $this->twig;
    }

    public function getModel() : Model
    {
        return $this->model;
    }
}
