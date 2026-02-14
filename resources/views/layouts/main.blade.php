<!DOCTYPE html>
<html lang="en">

<head>
    <title>Pesona Bali | @yield('title')</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @stack('css')

    <style>
        body {
            background-color: #c7c5c57e;
        }
    </style>
</head>

<body>
    {{-- @include('partials.navbar') --}}
    @include('components.navbar')
    <div class="">
        @yield('content')
    </div>

    {{-- <script src="{{ asset('js/app.js') }}"></script> --}}
    @include('components.footer')
</body>
@stack('script')

</html>
