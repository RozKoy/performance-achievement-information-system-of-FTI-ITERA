<x-auth-template title="Ubah Kata Sandi" reverse>
    <p class="text-center text-primary" title="Email anda">okkoy.1401@gmail.com</p>
    <label for="password" title="Kata sandi baru">Kata Sandi Baru</label>
    <x-partials.input.password name="password" title="Kata sandi baru" autofocus required />
    <button type="submit" title="Tombol ubah kata sandi" class="rounded-lg bg-primary py-2 text-white">Ubah Kata Sandi</button>
    <a href="{{ url(route('login')) }}" title="Halaman masuk" class="text-center text-primary underline">Masuk</a>
</x-auth-template>
