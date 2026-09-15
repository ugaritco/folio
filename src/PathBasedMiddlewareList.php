<?php

namespace Ugarit\Folio;

use Heritage\Support\Arr;
use Heritage\Support\Collection;
use Heritage\Support\Str;
use Ugarit\Folio\Pipeline\MatchedView;

class PathBasedMiddlewareList
{
    /**
     * Create a new path based middleware list instance.
     */
    public function __construct(public array $middleware) {}

    /**
     * Find the middleware that match the given matched view's path.
     */
    public function match(MatchedView $view): Collection
    {
        $matched = [];

        $relativePath = trim($view->relativePath(), '/');

        foreach ($this->middleware as $pattern => $middleware) {
            if (Str::is(trim($pattern, '/'), $relativePath)) {
                $matched = array_merge($matched, Arr::wrap($middleware));
            }
        }

        return collect($matched);
    }
}
