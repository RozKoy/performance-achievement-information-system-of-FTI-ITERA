<x-auth-template title="Lupa Kata Sandi" reverse>
    <x-partials.label.default for="email" title="Email pengguna" text="Email Pengguna" required />
    <x-partials.input.text name="email" title="Email pengguna" autofocus required />
    <x-partials.button.submit title="konfirmasi" />
    <x-partials.link.default route="login" title="masuk" name="Masuk" center />

    @pushIf($errors->any(), 'notification')
    <x-partials.toast.default id="forget-password-error" message="Proses gagal" withTimeout danger />
    @endPushIf

</x-auth-template>
