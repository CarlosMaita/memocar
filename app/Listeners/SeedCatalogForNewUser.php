<?php

namespace App\Listeners;

use App\Actions\SeedMaintenanceCatalog;
use Illuminate\Auth\Events\Registered;

class SeedCatalogForNewUser
{
    public function handle(Registered $event): void
    {
        (new SeedMaintenanceCatalog)($event->user);
    }
}
