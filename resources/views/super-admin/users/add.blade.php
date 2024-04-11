@php
    $breadCrumbs = [
        [
            'link' => 'super-admin-users',
            'name' => 'Pengguna',
        ],
        [
            'link' => 'super-admin-users-add',
            'name' => 'Tambah',
        ],
    ];
@endphp
<x-super-admin-template title="Tambah Pengguna - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="tambah pengguna" previous="super-admin-users" />
    <form action="" class="flex flex-col gap-2">
        <x-partials.label.default for="name" title="Nama pengguna" text="Nama Pengguna" required />
        <x-partials.input.text name="name" title="Nama pengguna" autofocus required />
        <x-partials.label.default for="email" title="Email" text="Email" required />
        <x-partials.input.text name="email" title="Email" required />
        <x-partials.label.default for="password" title="Kata sandi" text="Kata Sandi" required />
        <x-partials.input.text name="password" title="Kata sandi" disabled required />
        <div class="*:p-2.5 max-sm:text-sm max-[320px]:text-xs">
            <div class="*:flex-1 *:rounded-lg *:p-1 *:bg-primary/80 flex gap-2.5 text-white">
                <button id="super-admin-button" type="button" title="Tombol akses super admin" class="outline outline-2 outline-offset-1 outline-primary hover:bg-primary/70">Super Admin</button>
                <button id="admin-button" type="button" title="Tombol akses admin" onclick="switchSelection('admin-button', 'super-admin-button')" class="hover:bg-primary/70">Admin</button>
            </div>
            <div id="selection" class="*:rounded-lg *:border *:border-primary *:shadow *:p-1.5 *:gap-1 flex flex-wrap items-center justify-center gap-2">
                <div class="flex items-center justify-center">
                    <input type="radio" title="Super admin semua akses" name="access" id="editor" value="super-admin-editor" class="rounded-md border-0 bg-primary/25 checked:bg-primary/80 focus:ring-primary/90" checked required>
                    <label for="editor" title="Super admin semua akses">Semua akses</label>
                </div>
                <div class="flex items-center justify-center">
                    <input type="radio" title="Super admin akses hanya melihat" name="access" id="viewer-super-admin" value="super-admin-viewer" class="rounded-md border-0 bg-primary/25 checked:bg-primary/80 focus:ring-primary/90" required>
                    <label for="viewer-super-admin" title="Super admin akses hanya melihat">Hanya melihat</label>
                </div>
            </div>
        </div>
        <x-partials.button.add submit />
    </form>

    <div class="hidden">
        <div id="super-admin-selection">
            <div class="flex items-center justify-center">
                <input type="radio" title="Super admin semua akses" name="access" id="editor" value="super-admin-editor" class="rounded-md border-0 bg-primary/25 checked:bg-primary/80 focus:ring-primary/90" checked required>
                <label for="editor" title="Super admin semua akses">Semua akses</label>
            </div>
            <div class="flex items-center justify-center">
                <input type="radio" title="Super admin akses hanya melihat" name="access" id="viewer-super-admin" value="super-admin-viewer" class="rounded-md border-0 bg-primary/25 checked:bg-primary/80 focus:ring-primary/90" required>
                <label for="viewer-super-admin" title="Super admin akses hanya melihat">Hanya melihat</label>
            </div>
        </div>
        <div id="admin-selection">
            <select title="Pilih organisasi" name="organization" oninvalid="inputTextCustomMessage(this)" class="focus:border-primary focus:outline-none focus:ring-0" required>
                <option value="">Pilih Program Studi</option>
                <option value="243rei9rr339">Teknik Informatika</option>
            </select>
            <div class="flex items-center justify-center">
                <input type="checkbox" title="Admin akses hanya melihat" name="access" id="viewer-admin" value="admin-viewer" class="rounded-md border-0 bg-primary/25 checked:bg-primary/80 focus:ring-primary/90">
                <label for="viewer-admin" title="Admin akses hanya melihat">Hanya melihat</label>
            </div>
        </div>
    </div>

    @pushOnce('script')
        <script>
            document.getElementById('name').addEventListener('input', function(event) {
                document.getElementById('password').value = event.target.value.replaceAll(' ', '_');
            });

            function classToggle(id, arr) {
                arr.forEach(element => {
                    document.getElementById(id).classList.toggle(element);
                });
            }

            function switchSelection(first, second) {
                document.getElementById(first).removeAttribute('onclick');
                document.getElementById(second).setAttribute('onclick', `switchSelection('${ second }', '${ first }')`);

                classToggle('super-admin-button', ['outline', 'outline-2', 'outline-offset-1', 'outline-primary']);
                classToggle('admin-button', ['outline', 'outline-2', 'outline-offset-1', 'outline-primary']);

                let newSelection = document.getElementById(first === 'super-admin-button' ? 'super-admin-selection' : 'admin-selection');
                document.getElementById('selection').innerHTML = newSelection.innerHTML;

                console.log(newSelection);
            }
        </script>
    @endPushOnce
</x-super-admin-template>
