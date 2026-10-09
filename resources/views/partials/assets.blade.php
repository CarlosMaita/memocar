{{-- Sin Node ni CDN en runtime: CSS compilado con scripts/build-css.sh y Alpine servido desde public/js. --}}
<link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
<script defer src="{{ asset('js/alpine.min.js') }}?v=3.14.9"></script>
