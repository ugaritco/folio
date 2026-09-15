<?php

namespace Ugarit\Folio\Events;

use Ugarit\Folio\MountPath;
use Ugarit\Folio\Pipeline\MatchedView;

class ViewMatched
{
    /**
     * Create a new view matched event instance.
     */
    public function __construct(
        public MatchedView $matchedView,
        public MountPath $mountPath,
    ) {}
}
