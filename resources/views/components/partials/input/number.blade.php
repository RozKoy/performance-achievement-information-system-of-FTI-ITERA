@php
    /**
     * @required @param title: string
     * @required @param name: string
     * @optional @param autofocus: mixed
     * @optional @param disabled: mixed
     * @optional @param required: mixed
     * @optional @param style: string
     * @optional @param value: string
     */
@endphp

<div class="flex flex-col gap-0.5">
    <input type="number" name="{{ $name }}" id="{{ $name }}" placeholder="Masukkan {{ strtolower($title) }}" title="{{ ucfirst($title) }}" oninvalid="inputNumberCustomMessage(this)" oninput="inputNumberCustomMessage(this)" class="{{ isset($style) ? $style : '' }} rounded-lg border-2 !border-slate-100 !px-2 !py-1.5 text-primary focus:!border-primary focus:!outline-none focus:!ring-0 disabled:cursor-not-allowed disabled:bg-primary/10 max-sm:text-sm" @isset($value) value="{{ $value }}" @endisset @isset($autofocus) autofocus @endisset @disabled(isset($disabled)) @required(isset($required))>

    @error($name)
        <p class="text-xs text-red-500 lg:text-sm">{{ $message }}</p>
    @enderror
</div>

@pushOnce('script')
    <script>
        function inputNumberCustomMessage(component) {
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
