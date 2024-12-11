@php
    $breadCrumbs = [
        [
            'link' => 'admin-users',
            'name' => 'Pengguna',
        ],
        [
            'link' => 'admin-users-add',
            'name' => 'Tambah',
        ],
    ];
@endphp

<x-admin-template title="Tambah Pengguna - {{ auth()->user()->unit->name }}">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="tambah pengguna" previous="admin-users" />

    <form action="" method="POST" class="flex flex-col gap-2">
        @csrf
        <x-partials.label.default for="name" title="Nama pengguna" text="Nama Pengguna" required />
        <x-partials.input.text name="name" title="Nama pengguna" value="{{ old('name') }}" autofocus required />
        <x-partials.label.default for="email" title="Email" text="Email" required />
        <x-partials.input.text name="email" title="Email" value="{{ old('email') }}" required />
        <x-partials.label.default for="password" title="Kata sandi" text="Kata Sandi" required />
        <x-partials.input.text name="password" title="Kata sandi" disabled required />

        <div id="selection" class="*:rounded-lg *:border *:border-slate-100 *:shadow *:p-1.5 *:gap-1 flex flex-wrap items-center justify-center gap-2 p-2.5 text-primary max-sm:text-sm max-[320px]:text-xs">
            <div class="flex items-center justify-center">
                <x-partials.input.radio title="Admin semua akses" name="access" id="editor" value="editor" checked required />
                <x-partials.label.default for="editor" title="Admin semua akses" text="Semua akses" />
            </div>
            <div class="flex items-center justify-center">
                <x-partials.input.radio title="Admin akses hanya melihat" name="access" id="viewer-admin" value="viewer" required />
                <x-partials.label.default for="viewer-admin" title="Admin akses hanya melihat" text="Hanya melihat" />
            </div>
        </div>

        @error('access')
            <p class="text-xs text-red-500 lg:text-sm">{{ $message }}</p>
        @enderror

        <x-partials.button.add style="ml-auto" submit />
    </form>

    @pushIf($errors->any(), 'notification')
    <x-partials.toast.default id="user-add-data-error" message="Gagal menambahkan data pengguna" withTimeout danger />
    @endPushIf

    @pushOnce('script')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.getElementById('password').value = document.getElementById('name').value.replaceAll(' ', '_');
            });

            document.getElementById('name').addEventListener('input', function(event) {
                document.getElementById('password').value = event.target.value.replaceAll(' ', '_');
            });
        </script>
    @endPushOnce

</x-admin-template>
