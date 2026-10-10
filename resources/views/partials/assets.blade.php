{{-- Sin Node ni CDN de scripts: CSS compilado con scripts/build-css.sh y Alpine servido desde public/js. --}}
<link rel="manifest" href="/manifest.json">
<link rel="icon" href="/favicon.ico" sizes="32x32">
<link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="MemoCar">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<link rel="preconnect" href="https://fonts.bunny.net">
<link rel="stylesheet" href="https://fonts.bunny.net/css?family=archivo:600,700,800|figtree:400,500,600,700|jetbrains-mono:500,600&display=swap">
<link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
<script defer src="{{ asset('js/pwa.js') }}?v={{ filemtime(public_path('js/pwa.js')) }}"></script>
<script defer src="{{ asset('js/alpine.min.js') }}?v=3.14.9"></script>
