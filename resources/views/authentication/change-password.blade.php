<x-auth-template title="UBAH KATA SANDI" reverse>
    <form action="" class="mt-4 flex w-full flex-col gap-2">
        <p class="text-center text-primary" title="Email anda">okkoy.1401@gmail.com</p>
        <label for="password" title="Kata sandi baru">Kata Sandi Baru</label>
        <div class="relative">
            <input type="password" name="password" id="password" placeholder="******" title="Kata sandi baru" minlength="6" oninvalid="inputCustomMessage(this)" oninput="inputCustomMessage(this)" class="w-full rounded-lg border-2 border-slate-100 px-3 py-1.5 focus:text-primary focus:outline-primary" required>
            <button type="button" id="eye-open" onclick="togglePassword()" title="Lihat kata sandi" class="absolute bottom-0 right-2 top-0 my-auto">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="18">
                    <g fill="none" fill-rule="evenodd" stroke-linecap="round" stroke-linejoin="round" stroke-width="1" transform="translate(1 1)" class="stroke-primary">
                        <path d="M0 8s4-8 11-8 11 8 11 8-4 8-11 8S0 8 0 8z"></path>
                        <circle cx="11" cy="8" r="3"></circle>
                    </g>
                </svg>
            </button>
            <button type="button" id="eye-closed" onclick="togglePassword()" title="Sembunyikan kata sandi" class="absolute bottom-0 right-2 top-0 my-auto hidden">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                    <g fill="none" fill-rule="evenodd" stroke-linecap="round" stroke-linejoin="round" stroke-width="1" class="stroke-primary">
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24M1 1l22 22"></path>
                    </g>
                </svg>
            </button>
        </div>
        <button type="submit" title="Tombol ubah kata sandi" class="rounded-lg bg-primary py-2 text-white">Ubah Kata Sandi</button>
        <a href="{{ url(route('login')) }}" title="Halaman masuk" class="text-center text-primary underline">Masuk</a>
    </form>
</x-auth-template>
