<x-auth-template title="Masuk" description="Selamat Datang Di SICAKI">
    <x-partials.label.default for="email" title="Email" text="Email" required />
    <x-partials.input.text name="email" title="Email" value="{{ old('email') }}" autofocus required />
    <x-partials.label.default for="password" title="Kata sandi" text="Kata Sandi" required />
    <x-partials.input.password name="password" title="Kata sandi" required />
    <x-partials.link.default route="forget-password" title="lupa kata sandi" name="Lupa Kata Sandi?" right />
    <x-partials.button.submit title="masuk" />

    @pushIf($errors->any(), 'notification')
    <x-partials.toast.default id="error-login" message="Gagal masuk" withTimeout danger />
    @endPushIf

</x-auth-template>
