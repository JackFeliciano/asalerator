<?php

declare(strict_types=1);

namespace Asalerator\App;

use Asalerator\Home\Sample;

/**
 * The front controller. Routes, then renders. The health route answers from the
 * same sample the page renders, so the page and the probe cannot drift apart.
 */
final class Front
{
    public function __construct(private readonly string $base)
    {
    }

    public function run(): void
    {
        $router = new Router($this->base);
        $views = new Views($this->base);
        $sample = new Sample();

        $router->get('/', static fn (): array => [200, [
            'html' => $views->front($sample),
        ]]);
        $router->get('/health', static fn (): array => [200, ['json' => [
            'ok' => true,
            'sales' => count($sample->sales()),
            'bids' => $sample->bids(),
            'sold' => count(array_filter($sample->sales(), static fn ($s): bool => $s->standing() !== null)),
        ]]]);

        [$status, $body] = $router->handle(
            $_SERVER['REQUEST_METHOD'] ?? 'GET',
            $_SERVER['REQUEST_URI'] ?? '/',
        );

        http_response_code($status);
        if (isset($body['json'])) {
            header('Content-Type: application/json');
            echo json_encode($body['json'], JSON_UNESCAPED_SLASHES), "\n";

            return;
        }
        header('Content-Type: text/html; charset=utf-8');
        if (isset($body['html'])) {
            echo $body['html'];

            return;
        }
        echo $views->missing();
    }
}
