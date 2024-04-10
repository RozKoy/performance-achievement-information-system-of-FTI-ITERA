<x-auth-template title="Ubah Kata Sandi" reverse>
    <p class="text-center text-primary" title="Email anda">okkoy.1401@gmail.com</p>
    <x-partials.label.default for="password" title="Kata sandi baru" text="Kata Sandi Baru" required />
    <x-partials.input.password name="password" title="Kata sandi baru" autofocus required />
    <x-partials.button.submit title="ubah kata sandi" />
    <a href="{{ url(route('login')) }}" title="Halaman masuk" class="text-center text-primary underline">Masuk</a>
</x-auth-template>
