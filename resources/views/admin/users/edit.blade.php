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
                'id' => $user['id'],
            ],
        ],
    ];
@endphp

<x-admin-template title="Ubah Pengguna - {{ auth()->user()->unit->name }}">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="ubah pengguna" previous="admin-users" />

    <form action="" method="POST" class="flex flex-col gap-2">
        @csrf
        @method('PUT')
        <x-partials.label.default for="name" title="Nama pengguna" text="Nama Pengguna" required />
        <x-partials.input.text name="name" title="Nama pengguna" value="{{ $user['name'] }}" autofocus required />
        <x-partials.label.default for="email" title="Email" text="Email" required />
        <x-partials.input.text name="email" title="Email" value="{{ $user['email'] }}" required />

        <div id="selection" class="*:rounded-lg *:border *:border-slate-100 *:shadow *:p-1.5 *:gap-1 flex flex-wrap items-center justify-center gap-2 p-2.5 text-primary max-sm:text-sm max-[320px]:text-xs">
            <div class="flex items-center justify-center">

                @if ($user['access'] === 'editor')
                    <x-partials.input.radio title="Admin semua akses" name="access" id="editor" value="editor" checked required />
                @else
                    <x-partials.input.radio title="Admin semua akses" name="access" id="editor" value="editor" required />
                @endif

                <x-partials.label.default for="editor" title="Admin semua akses" text="Semua akses" />
            </div>
            <div class="flex items-center justify-center">

                @if ($user['access'] === 'viewer')
                    <x-partials.input.radio title="Admin akses hanya melihat" name="access" id="viewer-admin" value="viewer" checked required />
                @else
                    <x-partials.input.radio title="Admin akses hanya melihat" name="access" id="viewer-admin" value="viewer" required />
                @endif

                <x-partials.label.default for="viewer-admin" title="Admin akses hanya melihat" text="Hanya melihat" />
            </div>
        </div>

        @error('access')
            <p class="text-xs text-red-500 lg:text-sm">{{ $message }}</p>
        @enderror

        <x-partials.button.edit />
    </form>

    @pushIf($errors->any(), 'notification')
    <x-partials.toast.default id="user-edit-data-error" message="Gagal memperbarui data pengguna" withTimeout danger />
    @endPushIf

</x-admin-template>
