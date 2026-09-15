<?php

namespace Ugarit\Folio;

use Heritage\Support\Facades\Facade;

/**
 * @method static array middlewareFor(string $uri)
 * @method static mixed|null data(string|null $key = null, mixed|null $default = null)
 * @method static \Ugarit\Folio\FolioManager renderUsing(\Closure|null $callback = null)
 * @method static array mountPaths()
 * @method static array paths()
 * @method static \Ugarit\Folio\PendingRoute route(string|null $path = null, string|null $uri = '/', array $middleware = [])
 * @method static \Ugarit\Folio\PendingRoute path(string $path)
 * @method static \Ugarit\Folio\PendingRoute uri(string $uri)
 * @method static \Ugarit\Folio\PendingRoute domain(string $domain)
 * @method static \Ugarit\Folio\PendingRoute middleware(array $middleware)
 *
 * @see FolioManager
 */
class Folio extends Facade
{
    /**
     * {@inheritDoc}
     */
    public static function getFacadeAccessor(): string
    {
        return FolioManager::class;
    }
}
