<?php

namespace Ugarit\Folio\Pipeline;

use Closure;
use Heritage\Support\Str;
use Ugarit\Folio\Exceptions\PossibleDirectoryTraversal;

class EnsureNoDirectoryTraversal
{
    /**
     * Invoke the routing pipeline handler.
     *
     * @throws PossibleDirectoryTraversal
     */
    public function __invoke(State $state, Closure $next): mixed
    {
        if (! ($view = $next($state)) instanceof MatchedView) {
            return $view;
        }

        if (! Str::of(realpath($view->path))->startsWith($state->mountPath.DIRECTORY_SEPARATOR)) {
            throw new PossibleDirectoryTraversal;
        }

        return $view;
    }
}
