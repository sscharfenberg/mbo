<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="color-scheme" content="light dark">
        @if (file_exists(public_path('hot')))
            <meta name="hot" content="true" />
            @vite(['resources/app/main.ts'])
        @else
            @php
                $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
            @endphp
            <link rel="stylesheet" href="/build/{{$manifest['resources/app/main.ts']['css'][0]}}">
            <script type="module" src="/build/{{$manifest['resources/app/main.ts']['file']}}" defer></script>
        @endif
        @inertiaHead
    </head>
    <body>
        @php
            if (Storage::disk('public')->exists('sprite.svg')) {
                echo Storage::disk('public')->get('sprite.svg');
            }
        @endphp
        @inertia
    </body>
</html>
