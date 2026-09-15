<?php

namespace Ugarit\Folio;

use Closure;
use Heritage\Contracts\Events\Dispatcher;
use Heritage\Http\Request;
use Heritage\Routing\Pipeline;
use Heritage\Support\Facades\Route;
use Heritage\Support\Facades\View;
use Ugarit\Folio\Pipeline\MatchedView;
use Symfony\Component\HttpFoundation\Response;

class RequestHandler
{
    /**
     * Create a new request handler instance.
     *
     * @param  array<int, MountPath>  $mountPaths
     */
    public function __construct(
        protected array $mountPaths,
        protected ?Closure $renderUsing = null,
        protected ?Closure $onViewMatch = null,
    ) {}

    /**
     * Handle the incoming request using Folio.
     */
    public function __invoke(Request $request): mixed
    {
        foreach ($this->mountPaths as $mountPath) {
            $requestPath = '/'.ltrim($request->path(), '/');

            $uri = '/'.ltrim(substr($requestPath, strlen($mountPath->baseUri)), '/');

            if ($matchedView = app()->make(Router::class, ['mountPath' => $mountPath])->match($request, $uri)) {
                break;
            }
        }

        abort_unless($matchedView ?? null, 404);

        if ($name = $matchedView->name()) {
            $request->route()->action['as'] = $name;
        }

        app(Dispatcher::class)->dispatch(new Events\ViewMatched($matchedView, $mountPath));

        $middleware = collect($this->middleware($mountPath, $matchedView));

        return (new Pipeline(app()))
            ->send($request)
            ->through($middleware->all())
            ->then(function (Request $request) use ($matchedView, $middleware) {
                if ($this->onViewMatch) {
                    ($this->onViewMatch)($matchedView);
                }

                $response = $this->renderUsing
                    ? ($this->renderUsing)($request, $matchedView)
                    : $this->toResponse($request, $matchedView);

                $app = app();

                $app->make(FolioManager::class)->terminateUsing(function () use ($middleware, $app, $request, $response) {
                    $middleware->filter(fn ($m) => is_string($m) && class_exists($m) && method_exists($m, 'terminate'))
                        ->map(fn (string $m) => $app->make($m))
                        ->each(fn (object $m) => $app->call([$m, 'terminate'], ['request' => $request, 'response' => $response]));

                    $request->route()->action['as'] = 'ugarit-folio';
                });

                return $response;
            });
    }

    /**
     * Get the middleware that should be applied to the matched view.
     */
    protected function middleware(MountPath $mountPath, MatchedView $matchedView): array
    {
        return Route::resolveMiddleware(
            $mountPath
                ->middleware
                ->match($matchedView)
                ->prepend('web')
                ->merge($matchedView->inlineMiddleware())
                ->unique()
                ->values()
                ->all()
        );
    }

    /**
     * Create a response instance for the given matched view.
     */
    protected function toResponse(Request $request, MatchedView $matchedView): Response
    {
        $view = View::file($matchedView->path, $matchedView->data);

        return Route::toResponse($request, app()->call(
            $matchedView->renderUsing(),
            ['view' => $view, ...$view->getData()]
        ) ?? $view);
    }
}
