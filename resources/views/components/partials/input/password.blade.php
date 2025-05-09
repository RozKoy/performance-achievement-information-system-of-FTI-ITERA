@php
    /**
     * @required @param title: string
     * @required @param name: string
     * @optional @param autofocus: mixed
     * @optional @param required: mixed
     * @optional @param style: string
     */
@endphp

<div class="relative">
    <input type="password" name="{{ $name }}" id="{{ $name }}" code="&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;" placeholder="&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;" title="{{ ucfirst($title) }}" oninvalid="inputPasswordCustomMessage(this)" oninput="inputPasswordCustomMessage(this)" class="{{ isset($style) ?? $style }} w-full rounded-lg border-2 border-slate-100 px-2 py-1.5 text-primary focus:border-primary focus:outline-none focus:ring-0 max-sm:text-sm" @isset($autofocus) autofocus @endisset @required(isset($required))>
    <button type="button" id="eye-open-{{ $name }}" target="{{ $name }}" onclick="togglePassword(this)" title="Lihat kata sandi" class="absolute bottom-0 right-2 top-0 my-auto">
        <svg itemref="" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-4 fill-primary sm:w-6">
            <g>
                <path d="M23.821,11.181v0C22.943,9.261,19.5,3,12,3S1.057,9.261.179,11.181a1.969,1.969,0,0,0,0,1.64C1.057,14.739,4.5,21,12,21s10.943-6.261,11.821-8.181A1.968,1.968,0,0,0,23.821,11.181ZM12,19c-6.307,0-9.25-5.366-10-6.989C2.75,10.366,5.693,5,12,5c6.292,0,9.236,5.343,10,7C21.236,13.657,18.292,19,12,19Z" />
                <path d="M12,7a5,5,0,1,0,5,5A5.006,5.006,0,0,0,12,7Zm0,8a3,3,0,1,1,3-3A3,3,0,0,1,12,15Z" />
            </g>
        </svg>
    </button>
    <button type="button" id="eye-closed-{{ $name }}" target="{{ $name }}" onclick="togglePassword(this)" title="Sembunyikan kata sandi" class="absolute bottom-0 right-2 top-0 my-auto hidden">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-4 fill-primary sm:w-6">
            <g>
                <path d="M23.821,11.181v0a15.736,15.736,0,0,0-4.145-5.44l3.032-3.032L21.293,1.293,18,4.583A11.783,11.783,0,0,0,12,3C4.5,3,1.057,9.261.179,11.181a1.969,1.969,0,0,0,0,1.64,15.736,15.736,0,0,0,4.145,5.44L1.293,21.293l1.414,1.414L6,19.417A11.783,11.783,0,0,0,12,21c7.5,0,10.943-6.261,11.821-8.181A1.968,1.968,0,0,0,23.821,11.181ZM2,12.011C2.75,10.366,5.693,5,12,5a9.847,9.847,0,0,1,4.518,1.068L14.753,7.833a4.992,4.992,0,0,0-6.92,6.92L5.754,16.832A13.647,13.647,0,0,1,2,12.011ZM15,12a3,3,0,0,1-3,3,2.951,2.951,0,0,1-1.285-.3L14.7,10.715A2.951,2.951,0,0,1,15,12ZM9,12a3,3,0,0,1,3-3,2.951,2.951,0,0,1,1.285.3L9.3,13.285A2.951,2.951,0,0,1,9,12Zm3,7a9.847,9.847,0,0,1-4.518-1.068l1.765-1.765a4.992,4.992,0,0,0,6.92-6.92l2.078-2.078A13.584,13.584,0,0,1,22,12C21.236,13.657,18.292,19,12,19Z" />
            </g>
        </svg>
    </button>
</div>

@pushOnce('script')
    <script>
        function togglePassword(component) {
            let target = component.getAttribute('target');
            let password = document.getElementById(target);
            let defaultPlaceHolder = password.getAttribute('code');
            password.type = password.type === 'password' ? 'text' : 'password';
            password.placeholder = password.type === 'password' ? defaultPlaceHolder : 'Masukkan ' + password.title.toLowerCase();

            document.getElementById('eye-open-' + target).classList.toggle('hidden');
            document.getElementById('eye-closed-' + target).classList.toggle('hidden');
        }

        function inputPasswordCustomMessage(component) {
            const state = component.validity;
            const title = component.title;

            if (state.valueMissing) {
                component.setCustomValidity(title + ' wajib diisi');
            } else {
                component.setCustomValidity('');
            }
        }
    </script>
@endPushOnce
