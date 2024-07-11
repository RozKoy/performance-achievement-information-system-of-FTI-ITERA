<select name="{{ $name }}" id="{{ $name }}" title="{{ $title }}" oninvalid="inputSelectCustomMessage(this)" @isset($onchange) onchange="{{ $onchange }}" @endisset class="@isset($style) {{ $style }} @endisset rounded-lg border-2 !border-slate-100 !py-1.5 !pl-2 !pr-8 text-primary focus:!border-primary focus:!outline-none focus:!ring-0 disabled:cursor-not-allowed disabled:bg-primary/10 max-sm:text-sm" @isset($disabled) disabled @endisset @isset($autofocus) autofocus @endisset @isset($required) required @endisset>

    @foreach ($data as $item)
        <option value="{{ $item['value'] }}" @isset($item['selected']) selected @endif>{{ $item['text'] }}</option>
    @endforeach

</select>

@pushOnce('script')
    <script>
        function inputSelectCustomMessage(component) {
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
