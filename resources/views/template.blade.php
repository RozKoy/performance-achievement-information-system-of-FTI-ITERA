<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>{{ $title }} | {{ env('APP_NAME') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>

</head>

<body class="font-primary">

    @yield('content')

    <div class="fixed bottom-0 left-0 z-40 flex w-fit flex-col gap-2 p-6">

        @if ($message = Session::get('success'))
            <x-partials.toast.default id="{{ str_replace(' ', '-', $message) }}" message="{{ $message }}" />
        @endif

        @stack('notification')

    </div>

    @stack('script')

</body>

</html>
