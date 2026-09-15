<?php

use Heritage\Support\Facades\Gate;

use function Ugarit\Folio\render;

render(function () {
    return Gate::authorize('viewAny');
}); ?>

<div>
    @php
        throw new \Exception('Should not be rendered');
    @endphp
</div>
