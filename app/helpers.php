<?php

if (! function_exists('money')) {
    function money(float|string|null $amount): string
    {
        return '$'.number_format((float) $amount, 2, ',', '.');
    }
}

if (! function_exists('km')) {
    function km(?int $km): string
    {
        return $km === null ? '—' : number_format($km, 0, ',', '.').' km';
    }
}
