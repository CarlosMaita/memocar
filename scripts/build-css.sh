#!/usr/bin/env bash
# Compila resources/css/app.css → public/css/app.css con el binario standalone de Tailwind (sin Node).
# Correr después de cambiar clases en vistas Blade y commitear el CSS generado.
set -euo pipefail
cd "$(dirname "$0")/.."

VERSION=4.1.11
BIN="${TAILWIND_BIN:-$HOME/.cache/tailwindcss-$VERSION}"
if [ ! -x "$BIN" ]; then
    mkdir -p "$(dirname "$BIN")"
    curl -sSL -o "$BIN" "https://github.com/tailwindlabs/tailwindcss/releases/download/v$VERSION/tailwindcss-linux-x64"
    chmod +x "$BIN"
fi

"$BIN" -i resources/css/app.css -o public/css/app.css --minify
