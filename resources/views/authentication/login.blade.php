<x-auth-template title="Masuk" description="Selamat Datang Di SICAKI">
    <label for="user" title="Email atau nama pengguna">Email / Nama Pengguna</label>
    <x-partials.input.text name="user" title="Email atau nama pengguna" autofocus required />
    <label for="password" title="Kata sandi">Kata Sandi</label>
    <x-partials.input.password name="password" title="Kata sandi" required />
    <a href="{{ url(route('forget-password')) }}" title="Halaman lupa kata sandi" class="text-right text-primary underline">Lupa Kata Sandi?</a>
    <button type="submit" title="Tombol masuk" class="rounded-lg bg-primary py-2 text-white">Masuk</button>
</x-auth-template>
