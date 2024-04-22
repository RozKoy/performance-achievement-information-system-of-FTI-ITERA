<textarea name="{{ $name }}" id="{{ $name }}" rows="1" placeholder="Masukkan {{ strtolower($title) }}" title="{{ ucfirst($title) }}" oninvalid="inputTextareaCustomMessage(this)" oninput="inputTextareaCustomMessage(this)" class="{{ isset($style) ? $style : '' }} rounded-lg border-2 !border-slate-100 !px-2 !py-1.5 text-primary focus:!border-primary focus:!outline-none focus:!ring-0 disabled:cursor-not-allowed disabled:bg-primary/10 max-sm:text-sm" @isset($value) value="{{ $value }}" @endisset @isset($autofocus) autofocus @endisset @disabled(isset($disabled)) @required(isset($required))></textarea>

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
