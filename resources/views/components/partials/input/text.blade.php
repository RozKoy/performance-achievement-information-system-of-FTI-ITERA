<div class="flex flex-col gap-0.5">
    <input type="text" name="{{ $name }}" id="{{ $name }}" placeholder="Masukkan {{ strtolower($title) }}" title="{{ ucfirst($title) }}" oninvalid="inputTextCustomMessage(this)" oninput="inputTextCustomMessage(this)" class="{{ isset($style) ? $style : '' }} rounded-lg border-2 !border-slate-100 !px-2 !py-1.5 text-primary focus:!border-primary focus:!outline-none focus:!ring-0 disabled:cursor-not-allowed disabled:bg-primary/10 max-sm:text-sm" @isset($value) value="{{ $value }}" @endisset @isset($oldvalue) oldvalue="{{ $oldvalue }}" @endisset @isset($onblur) onblur="{{ $onblur }}" @endisset @isset($autofocus) autofocus @endisset @disabled(isset($disabled)) @required(isset($required))>

    @error($name)
        <p class="text-xs text-red-500 lg:text-sm">{{ $message }}</p>
    @enderror
</div>


@pushOnce('script')
    <script>
        function inputTextCustomMessage(component) {
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
