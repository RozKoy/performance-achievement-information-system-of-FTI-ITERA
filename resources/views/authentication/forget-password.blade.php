<x-auth-template title="Lupa Kata Sandi" reverse>
    <label for="user" title="Email atau nama pengguna">Email / Nama Pengguna</label>
    <x-partials.input.text name="user" title="Email atau nama pengguna" autofocus required />
    <button type="submit" title="Tombol konfirmasi" class="rounded-lg bg-primary py-2 text-white">Konfirmasi</button>
    <a href="{{ url(route('login')) }}" title="Halaman masuk" class="text-center text-primary underline">Masuk</a>
</x-auth-template>
