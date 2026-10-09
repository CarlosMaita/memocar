<?php

namespace App\Http\Controllers;

use App\Models\Car;

abstract class Controller
{
    /** Carro seleccionado (lo resuelve el middleware EnsureCarSelected). */
    protected function car(): Car
    {
        return request()->attributes->get('car');
    }
}
