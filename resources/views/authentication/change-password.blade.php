<x-auth-template title="Ubah Kata Sandi" reverse>
    <p class="text-center text-primary" title="Email anda">{{ $user['email'] }}</p>
    <x-partials.label.default for="password" title="Kata sandi baru" text="Kata Sandi Baru" required />
    <x-partials.input.password name="password" title="Kata sandi baru" autofocus required />
    <x-partials.button.submit title="ubah kata sandi" />
    <x-partials.link.default route="login" title="masuk" name="Masuk" center />
</x-auth-template>
