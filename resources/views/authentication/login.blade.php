<x-auth-template title="Masuk" description="Selamat Datang Di SICAKI">
    <x-partials.label.default for="user" title="Email atau nama pengguna" text="Email / Nama Pengguna" required />
    <x-partials.input.text name="user" title="Email atau nama pengguna" autofocus required />
    <x-partials.label.default for="password" title="Kata sandi" text="Kata Sandi" required />
    <x-partials.input.password name="password" title="Kata sandi" required />
    <a href="{{ url(route('forget-password')) }}" title="Halaman lupa kata sandi" class="text-right text-primary underline">Lupa Kata Sandi?</a>
    <x-partials.button.submit title="masuk" />
</x-auth-template>
