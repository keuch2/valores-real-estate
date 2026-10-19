<?php

namespace App\Support;

use Livewire\Mechanisms\HandleRequests\HandleRequests;

/**
 * El sitio vive en una subcarpeta (codexpy.com/valores-real-estate). Livewire genera el endpoint
 * de actualización relativo a la raíz del dominio ("/livewire/update") y pierde la subcarpeta;
 * acá se devuelve la URL completa.
 */
class SubfolderLivewireRequests extends HandleRequests
{
    public function getUpdateUri()
    {
        return url(parent::getUpdateUri());
    }
}
