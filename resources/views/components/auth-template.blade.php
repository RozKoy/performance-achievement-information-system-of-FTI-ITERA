<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Masuk | {{ env('APP_NAME') }}</title>

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

    <div class="{{ isset($reverse) ? 'flex-row-reverse' : 'flex-row' }} flex h-screen w-screen bg-primary">
        <div class="relative flex h-full w-full items-center justify-center">
            <div class="absolute z-0 h-full w-full bg-[url('{{ asset('storage/assets/authentication/line.svg') }}')] bg-contain bg-[left_-25px_top_-20px] bg-no-repeat">
            </div>
            <div class="absolute z-0 h-full w-full rotate-180 bg-[url('{{ asset('storage/assets/authentication/line.svg') }}')] bg-contain bg-[left_-25px_top_-20px] bg-no-repeat">
            </div>
            <div class="relative flex flex-col items-center rounded-lg bg-white p-10">
                <img src="{{ url(asset('storage/assets/fti-banner.svg')) }}" alt="fti-banner" class="mb-4 w-80">
                <h2 class="text-xl font-semibold">{{ $title }}</h2>
                <p class="text-slate-500">Selamat Datang Di SICAKI</p>

                {{ $slot }}

            </div>
        </div>
        <div class="relative flex h-full w-full items-center justify-center bg-white">
            <div class="flex flex-col gap-7">
                <img src="{{ url(asset('storage/assets/fti-logo.png')) }}" alt="fti-banner" class="w-96">
                <h1 class="rounded-xl bg-primary py-1 text-center text-xl font-semibold text-white"><span class="text-8xl">SICAKI</span><br> Sistem Informasi Capaian Kinerja</h1>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            let password = document.getElementById('password');
            password.type = password.type === 'password' ? 'text' : 'password';
            password.placeholder = password.type === 'password' ? '******' : 'Masukkan kata sandi';

            document.getElementById('eye-open').classList.toggle('hidden');
            document.getElementById('eye-closed').classList.toggle('hidden');
        }

        function inputCustomMessage(component) {
            const state = component.validity;
            const title = component.title;

            if (state.valueMissing) {
                component.setCustomValidity(title + ' wajib diisi');
            } else if (state.tooShort) {
                component.setCustomValidity(title + ' tidak boleh dibawah 6 karakter');
            } else {
                component.setCustomValidity('');
            }
        }
    </script>

</body>

</html>
