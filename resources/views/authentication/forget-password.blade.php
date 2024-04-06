<x-auth-template title="Lupa Kata Sandi" reverse>
    <label for="user" title="Email atau nama pengguna">Email / Nama Pengguna</label>
    <input type="text" name="user" id="user" placeholder="Masukkan email atau nama pengguna" title="Email atau nama pengguna" oninvalid="inputCustomMessage(this)" oninput="inputCustomMessage(this)" class="rounded-lg border-2 border-slate-100 px-3 py-1.5 text-primary focus:border-transparent focus:outline-primary focus:ring-primary" autofocus required>
    <button type="submit" title="Tombol konfirmasi" class="rounded-lg bg-primary py-2 text-white">Konfirmasi</button>
    <a href="{{ url(route('login')) }}" title="Halaman masuk" class="text-center text-primary underline">Masuk</a>
</x-auth-template>
