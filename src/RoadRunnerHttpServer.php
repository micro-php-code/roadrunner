<?php

declare(strict_types=1);

namespace MicroPHP\RoadRunner;

use JsonException;
use MicroPHP\Framework\Http\Contract\HttpServerInterface;
use MicroPHP\Framework\Http\ServerRequest;
use MicroPHP\Framework\Http\Traits\HttpServerTrait;
use MicroPHP\Framework\Router\Router;
use Nyholm\Psr7\Factory\Psr17Factory;
use Spiral\RoadRunner\Http\PSR7Worker;
use Spiral\RoadRunner\Worker;
use Throwable;

class RoadRunnerHttpServer implements HttpServerInterface
{
    use HttpServerTrait;

    /**
     * @throws JsonException
     */
    public function run(Router $router): void
    {
        $worker = Worker::create();

        $factory = new Psr17Factory();

        $psr7 = new PSR7Worker($worker, $factory, $factory, $factory);

        while ($req = $psr7->waitRequest()) {
            $request = ServerRequest::fromPsr7($req);
            try {
                $psr7->respond($this->routeDispatch($router, $request));
            } catch (Throwable $e) {
                $psr7->getWorker()->error((string) $e);
            }
        }
    }
}
