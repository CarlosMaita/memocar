{{-- Sin Node ni CDN de scripts: CSS compilado con scripts/build-css.sh y Alpine servido desde public/js. --}}
<link rel="preconnect" href="https://fonts.bunny.net">
<link rel="stylesheet" href="https://fonts.bunny.net/css?family=archivo:600,700,800|figtree:400,500,600,700|jetbrains-mono:500,600&display=swap">
<link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
<script defer src="{{ asset('js/alpine.min.js') }}?v=3.14.9"></script>
