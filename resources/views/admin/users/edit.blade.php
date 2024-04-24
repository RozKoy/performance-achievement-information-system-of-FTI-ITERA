@php
    $breadCrumbs = [
        [
            'link' => 'admin-users',
            'name' => 'Pengguna',
        ],
        [
            'link' => 'admin-users-edit',
            'name' => 'Ubah',
            'params' => [
                'id' => 'hahaha',
            ],
        ],
    ];
@endphp
<x-admin-template title="Ubah Pengguna - Teknik Informatika">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="ubah pengguna" previous="admin-users" />
    <form action="" class="flex flex-col gap-2">
        <x-partials.label.default for="name" title="Nama pengguna" text="Nama Pengguna" required />
        <x-partials.input.text name="name" title="Nama pengguna" autofocus required />
        <x-partials.label.default for="email" title="Email" text="Email" required />
        <x-partials.input.text name="email" title="Email" required />
        <x-partials.label.default for="password" title="Kata sandi" text="Kata Sandi" required />
        <x-partials.input.text name="password" title="Kata sandi" disabled required />
        <div id="selection" class="*:rounded-lg *:border *:border-slate-100 *:shadow *:p-1.5 *:gap-1 flex flex-wrap items-center justify-center gap-2 p-2.5 text-primary max-sm:text-sm max-[320px]:text-xs">
            <div class="flex items-center justify-center">
                <x-partials.input.radio title="Admin semua akses" name="access" id="editor" value="admin-editor" checked required />
                <label for="editor" title="Admin semua akses">Semua akses</label>
            </div>
            <div class="flex items-center justify-center">
                <x-partials.input.radio title="Admin akses hanya melihat" name="access" id="viewer-admin" value="admin-viewer" required />
                <label for="viewer-admin" title="Admin akses hanya melihat">Hanya melihat</label>
            </div>
        </div>
        <x-partials.button.edit />
    </form>

    @pushOnce('script')
        <script>
            document.getElementById('name').addEventListener('input', function(event) {
                document.getElementById('password').value = event.target.value.replaceAll(' ', '_');
            });
        </script>
    @endPushOnce
</x-admin-template>
