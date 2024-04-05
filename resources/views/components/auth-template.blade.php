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

    <div class="{{ isset($reverse) ? 'flex-row-reverse' : 'flex-row' }} flex h-screen w-screen overflow-y-auto bg-primary">
        <div class="relative flex h-full w-full items-center justify-center">
            <div class="absolute z-0 h-full w-full bg-[url('{{ asset('storage/assets/authentication/line.svg') }}')] bg-contain bg-[left_-25px_top_-20px] bg-no-repeat">
            </div>
            <div class="absolute z-0 h-full w-full rotate-180 bg-[url('{{ asset('storage/assets/authentication/line.svg') }}')] bg-contain bg-[left_-25px_top_-20px] bg-no-repeat">
            </div>
            <div class="min-w-64 relative flex w-3/4 max-w-[750px] flex-col items-center rounded-lg bg-white p-10 max-2xl:w-[30rem] max-sm:w-10/12 max-sm:p-7 max-sm:text-sm max-[320px]:text-xs">
                <img src="{{ url(asset('storage/assets/fti-banner.svg')) }}" alt="fti-banner" class="mb-5 w-full">
                <h2 class="text-xl font-semibold max-sm:text-base" title="{{ $title }}">{{ $title }}</h2>
                <p class="text-slate-500" title="Selamat Datang Di Sistem Informasi Capaian Kinerja FTI ITERA">Selamat Datang Di SICAKI</p>

                <form action="" class="mt-4 flex w-full flex-col gap-2">
                    {{ $slot }}
                </form>

            </div>
        </div>
        <div class="relative flex h-full w-full items-center justify-center bg-white max-lg:hidden">
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
