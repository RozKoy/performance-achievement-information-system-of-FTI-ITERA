<x-auth-template title="Masuk" description="Selamat Datang Di SICAKI">
    <x-partials.label.default for="user" title="Email atau nama pengguna" text="Email / Nama Pengguna" required />
    <x-partials.input.text name="user" title="Email atau nama pengguna" autofocus required />
    <x-partials.label.default for="password" title="Kata sandi" text="Kata Sandi" required />
    <x-partials.input.password name="password" title="Kata sandi" required />
    <x-partials.link.default route="forget-password" title="lupa kata sandi" name="Lupa Kata Sandi?" right />
    <x-partials.button.submit title="masuk" />
</x-auth-template>
