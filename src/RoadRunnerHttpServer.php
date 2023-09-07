<?php

declare(strict_types=1);

namespace MicroPHP\RoadRunner;

use JsonException;
use League\Route\Http\Exception\MethodNotAllowedException;
use League\Route\Http\Exception\NotFoundException;
use League\Route\Router;
use MicroPHP\Framework\Http\Contract\ServerInterface;
use MicroPHP\Framework\Http\Response;
use MicroPHP\Framework\Http\ServerRequest;
use Nyholm\Psr7\Factory\Psr17Factory;
use Spiral\RoadRunner\Http\PSR7Worker;
use Spiral\RoadRunner\Worker;
use Throwable;

class RoadRunnerHttpServer implements ServerInterface
{
    /**
     * @throws JsonException
     *
     * @noinspection PhpRedundantCatchClauseInspection
     */
    public function run(Router $router): void
    {
        $worker = Worker::create();

        $factory = new Psr17Factory();

        $psr7 = new PSR7Worker($worker, $factory, $factory, $factory);

        while ($req = $psr7->waitRequest()) {
            $request = ServerRequest::fromPsr7($req);
            try {
                $psr7->respond($router->dispatch($request));
            } catch (NotFoundException $exception) {
                $psr7->respond(new Response(404, [], $exception->getMessage()));
            } catch (MethodNotAllowedException $exception) {
                $psr7->respond(new Response(405, [], $exception->getMessage()));
            } catch (Throwable $e) {
                $psr7->getWorker()->error((string) $e);
            }
        }
    }
}
