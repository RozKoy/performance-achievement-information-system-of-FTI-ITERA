<x-auth-template title="LUPA KATA SANDI" reverse>
    <form action="" class="mt-4 flex w-full flex-col gap-2">
        <label for="user" title="Email atau Nama Pengguna">Email / Nama Pengguna</label>
        <input type="text" name="user" id="user" placeholder="Masukkan email atau nama pengguna" title="Email atau nama pengguna" oninvalid="inputCustomMessage(this)" oninput="inputCustomMessage(this)" class="rounded-lg border-2 border-slate-100 px-3 py-1.5 focus:text-primary focus:outline-primary" autofocus required>
        <button type="submit" title="Tombol konfirmasi" class="rounded-lg bg-primary py-2 text-white">Konfirmasi</button>
        <a href="{{ url(route('login')) }}" title="Halaman masuk" class="text-center text-primary underline">Masuk</a>
    </form>
</x-auth-template>
