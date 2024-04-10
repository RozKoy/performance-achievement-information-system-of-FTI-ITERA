<x-auth-template title="Lupa Kata Sandi" reverse>
    <x-partials.label.default for="user" title="Email atau nama pengguna" text="Email / Nama Pengguna" required />
    <x-partials.input.text name="user" title="Email atau nama pengguna" autofocus required />
    <x-partials.button.submit title="konfirmasi" />
    <a href="{{ url(route('login')) }}" title="Halaman masuk" class="text-center text-primary underline">Masuk</a>
</x-auth-template>
