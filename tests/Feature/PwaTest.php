<?php

test('las páginas enlazan el manifest y el ícono de la app', function () {
    $this->get('/login')
        ->assertOk()
        ->assertSee('rel="manifest" href="/manifest.json"', false)
        ->assertSee('/icons/apple-touch-icon.png', false)
        ->assertSee('js/pwa.js', false);
});

test('el manifest es válido y sus íconos existen', function () {
    $manifest = json_decode(file_get_contents(public_path('manifest.json')), true, flags: JSON_THROW_ON_ERROR);

    expect($manifest['display'])->toBe('standalone')
        ->and($manifest['start_url'])->toStartWith('/dashboard');

    foreach ($manifest['icons'] as $icon) {
        expect(file_exists(public_path(ltrim($icon['src'], '/'))))->toBeTrue();
    }
    expect(file_exists(public_path('sw.js')))->toBeTrue()
        ->and(file_exists(public_path('offline.html')))->toBeTrue();
});
