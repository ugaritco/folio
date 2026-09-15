<?php

namespace Ugarit\Folio\Pipeline;

use Closure;
use Heritage\Support\Str;

class MatchWildcardViews
{
    use FindsWildcardViews;

    /**
     * Invoke the routing pipeline handler.
     */
    public function __invoke(State $state, Closure $next): mixed
    {
        if ($state->onLastUriSegment() &&
            $path = $this->findWildcardView($state->currentDirectory())) {
            return new MatchedView($state->currentDirectory().'/'.$path, $state->withData(
                Str::of($path)
                    ->before('.blade.php')
                    ->match('/\[(.*)\]/')->value(),
                $state->currentUriSegment(),
            )->data);
        }

        return $next($state);
    }
}
