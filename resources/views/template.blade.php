<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>{{ $title }} | {{ env('APP_NAME') }}</title>

    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>

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

</body>

</html>
