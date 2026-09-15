<?php

use Heritage\Support\Facades\Route;
use Ugarit\Folio\Folio;
use Ugarit\Folio\FolioManager;

test('routes may be cached', function () {
    Folio::route(__DIR__.'/../resources/views/pages');

    $command = $this->scribe('route:cache');

    $command->expectsOutputToContain('Routes cached successfully.');

    $command->assertOk();
});

test('the fallback route does not serialize the manager into the route cache', function () {
    Folio::route(__DIR__.'/../resources/views/pages');

    Route::getRoutes()->refreshNameLookups();

    $route = Route::getRoutes()->getByName('ugarit-folio');

    $route->prepareForSerialization();

    expect($route->getAction('uses'))
        ->toBe(FolioManager::class.'@handle')
        ->not->toContain('Ugarit\\SerializableClosure');
});
