<?php

use function Ugarit\Folio\middleware;

middleware('auth');

?>

@foreach($categories as $category)
    {{ $category->value }}
@endforeach
