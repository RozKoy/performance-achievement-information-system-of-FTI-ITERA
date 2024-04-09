<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>{{ $title }} | {{ env('APP_NAME') }}</title>

    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />

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
            background: #7C6343;
            border-bottom-left-radius: 5px;
            border-top-left-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #c0935c;
        }
    </style>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                fontFamily: {
                    primary: ['Poppins']
                },
                extend: {
                    colors: {
                        primary: '#7C6343',
                    }
                }
            }
        }
    </script>

</head>

<body class="font-primary">

    @yield('content')

    @stack('script')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>

</body>

</html>
