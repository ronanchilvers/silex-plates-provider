<?php

namespace Rych\Silex\Provider;

use Rych\Plates\Extension\RoutingExtension;
use Rych\Plates\Extension\SecurityExtension;
use Silex\Application;
use Silex\ServiceProviderInterface;

class PlatesServiceProvider implements ServiceProviderInterface
{

    public function register(Application $app)
    {
        $app['plates.path']      = null;
        $app['plates.extension'] = 'php';
        $app['plates.folders']   = array();

        $app['plates.engine'] = $app->share(function ($app) {
            $engine = new \League\Plates\Engine(
                $app['plates.path'],
                $app['plates.extension']
            );
            foreach ($app['plates.folders'] as $name => $path) {
                $engine->addFolder($name, $path);
            }

            if (isset($app['url_generator'])) {
                $engine->loadExtension(new RoutingExtension($app['url_generator']));
            }

            if (isset($app['security'])) {
                $engine->loadExtension(new SecurityExtension($app['security']));
            }

            return $engine;
        });

        $app['plates'] = function ($app) {
            $plates      = $app['plates.engine'];
            $plates->addData([
                'app' => $app
            ]);

            return $plates;
        };
    }

    public function boot(Application $app)
    {
    }
}

