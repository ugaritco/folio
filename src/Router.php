<?php

namespace Ugarit\Folio;

use Heritage\Http\Request;
use Heritage\Pipeline\Pipeline;
use Ugarit\Folio\Pipeline\ContinueIterating;
use Ugarit\Folio\Pipeline\EnsureMatchesDomain;
use Ugarit\Folio\Pipeline\EnsureNoDirectoryTraversal;
use Ugarit\Folio\Pipeline\MatchDirectoryIndexViews;
use Ugarit\Folio\Pipeline\MatchedView;
use Ugarit\Folio\Pipeline\MatchLiteralDirectories;
use Ugarit\Folio\Pipeline\MatchLiteralViews;
use Ugarit\Folio\Pipeline\MatchRootIndex;
use Ugarit\Folio\Pipeline\MatchWildcardDirectories;
use Ugarit\Folio\Pipeline\MatchWildcardViews;
use Ugarit\Folio\Pipeline\MatchWildcardViewsThatCaptureMultipleSegments;
use Ugarit\Folio\Pipeline\SetMountPathOnMatchedView;
use Ugarit\Folio\Pipeline\State;
use Ugarit\Folio\Pipeline\StopIterating;
use Ugarit\Folio\Pipeline\TransformModelBindings;

class Router
{
    /**
     * Create a new router instance.
     */
    public function __construct(protected MountPath $mountPath) {}

    /**
     * Match the given URI to a view via page based routing.
     */
    public function match(Request $request, string $uri): ?MatchedView
    {
        $uri = strlen($uri) > 1 ? trim($uri, '/') : $uri;

        if ($view = $this->matchAtPath($request, $uri)) {
            return $view;
        }

        return null;
    }

    /**
     * Resolve the given URI via page based routing at the given mount path.
     */
    protected function matchAtPath(Request $request, string $uri): ?MatchedView
    {
        $state = new State(
            uri: $uri,
            mountPath: $this->mountPath->path,
            segments: explode('/', $uri)
        );

        for ($i = 0; $i < $state->uriSegmentCount(); $i++) {
            $value = (new Pipeline)
                ->send($state->forIteration($i))
                ->through([
                    new EnsureMatchesDomain($request, $this->mountPath),
                    new EnsureNoDirectoryTraversal,
                    new TransformModelBindings($request),
                    new SetMountPathOnMatchedView,
                    new MatchRootIndex,
                    new MatchDirectoryIndexViews,
                    new MatchWildcardViewsThatCaptureMultipleSegments,
                    new MatchLiteralDirectories,
                    new MatchWildcardDirectories,
                    new MatchLiteralViews,
                    new MatchWildcardViews,
                ])->then(fn () => new StopIterating);

            if ($value instanceof MatchedView) {
                return $value;
            } elseif ($value instanceof ContinueIterating) {
                $state = $value->state;

                continue;
            } elseif ($value instanceof StopIterating) {
                break;
            }
        }

        return null;
    }
}
