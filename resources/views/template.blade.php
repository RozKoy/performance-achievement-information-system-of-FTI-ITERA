<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>{{ $title }} | {{ env('APP_NAME') }}</title>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />

        <script>
            tailwind.config = {
                theme: {
                    fontFamily: {
                        primary: ["Poppins"],
                    },
                    extend: {
                        colors: {
                            primary: "#B18E63",
                        },
                    },
                },
            }
        </script>

        <style>
            ::-webkit-scrollbar {
                width: 8px;
                height: 8px;
            }

            ::-webkit-scrollbar-track {
                background: #eeeeee;
                border-bottom-left-radius: 5px;
                border-top-left-radius: 5px;
            }

            ::-webkit-scrollbar-thumb {
                background: #7c6343;
                border-bottom-left-radius: 5px;
                border-top-left-radius: 5px;
            }

            ::-webkit-scrollbar-thumb:hover {
                background: #c0935c;
            }
        </style>
    @endif

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

    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

</body>

</html>
