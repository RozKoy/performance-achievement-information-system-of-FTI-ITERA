@php
    $breadCrumbs = [
        [
            'link' => 'super-admin-unit',
            'name' => 'Unit',
        ],
        [
            'link' => 'super-admin-unit-add',
            'name' => 'Tambah',
        ],
    ];
@endphp
<x-super-admin-template title="Tambah Unit - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="tambah unit" previous="super-admin-unit" />
    <form action="" method="POST" class="flex flex-col gap-2">
        @csrf
        <x-partials.label.default for="name" title="Nama unit" text="Nama Unit" required />
        <x-partials.input.text name="name" title="Nama unit" value="{{ old('name') }}" autofocus required />
        <p class="text-sm sm:text-base">Pilih Pengguna</p>
        <div class="*:border *:rounded-lg flex flex-wrap gap-1">
            @foreach ($users as $user)
                <div class="min-w-40 relative flex flex-1 items-center gap-1.5 px-2 py-1">
                    <input type="checkbox" name="users[]" id="user-{{ $loop->iteration }}" value="{{ $user['id'] }}" class="rounded-md border-0 bg-primary/25 checked:bg-primary/80 focus:ring-primary/90">
                    <label for="user-{{ $loop->iteration }}" class="*:truncate flex-1 overflow-hidden text-xs text-primary sm:text-sm">
                        <div class="flex items-center gap-1 font-semibold">
                            <p title="{{ $user['access'] === 'editor' ? 'Semua akses' : 'Hanya melihat' }}">
                                @if ($user['access'] === 'viewer')
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="aspect-square w-4">
                                        <g>
                                            <path d="M23.821,11.181v0C22.943,9.261,19.5,3,12,3S1.057,9.261.179,11.181a1.969,1.969,0,0,0,0,1.64C1.057,14.739,4.5,21,12,21s10.943-6.261,11.821-8.181A1.968,1.968,0,0,0,23.821,11.181ZM12,19c-6.307,0-9.25-5.366-10-6.989C2.75,10.366,5.693,5,12,5c6.292,0,9.236,5.343,10,7C21.236,13.657,18.292,19,12,19Z" />
                                            <path d="M12,7a5,5,0,1,0,5,5A5.006,5.006,0,0,0,12,7Zm0,8a3,3,0,1,1,3-3A3,3,0,0,1,12,15Z" />
                                        </g>
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="aspect-square w-4">
                                        <path d="m12,0C5.383,0,0,5.383,0,12s5.383,12,12,12,12-5.383,12-12S18.617,0,12,0Zm0,22c-5.514,0-10-4.486-10-10S6.486,2,12,2s10,4.486,10,10-4.486,10-10,10Zm1.379-15.621l-7.379,7.379v4.242h4.242l7.379-7.379c1.17-1.17,1.17-3.072,0-4.242s-3.072-1.17-4.242,0Zm-3.965,9.621h-1.414v-1.414l4.336-4.336,1.414,1.414-4.336,4.336Zm6.793-6.793l-1.043,1.043-1.414-1.414,1.043-1.043c.391-.391,1.023-.391,1.414,0s.39,1.024,0,1.414Z" />
                                    </svg>
                                @endif
                            </p>
                            <p title="{{ $user['username'] }}" class="truncate">
                                {{ $user['username'] }}
                            </p>
                        </div>
                        <p title="{{ $user['email'] }}" class="text-primary">{{ $user['email'] }}</p>
                    </label>
                </div>
            @endforeach
        </div>
        @if (!count($users))
            <p class="text-center text-red-500">Tidak ada pengguna yang tersedia</p>
        @endif
        @error('users')
            <p class="text-xs text-red-500 lg:text-sm">{{ $message }}</p>
        @enderror
        <x-partials.button.add submit />
    </form>
</x-super-admin-template>
