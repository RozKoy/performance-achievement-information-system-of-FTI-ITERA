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

<textarea name="{{ $name }}" id="{{ $name }}" rows="1" placeholder="Masukkan {{ strtolower($title) }}" title="{{ ucfirst($title) }}" oninvalid="inputTextareaCustomMessage(this)" oninput="inputTextareaCustomMessage(this)" class="{{ $style ?? '' }} rounded-lg border-2 !border-slate-100 !px-2 !py-1.5 text-primary focus:!border-primary focus:!outline-none focus:!ring-0 disabled:cursor-not-allowed disabled:bg-primary/10 max-sm:text-sm" @isset($autofocus) autofocus @endisset @disabled(isset($disabled)) @required(isset($required))>
    {{ $value ?? '' }}
</textarea>

@pushOnce('script')
    <script>
        function inputTextareaCustomMessage(component) {
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
