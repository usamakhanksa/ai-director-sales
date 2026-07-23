@php
    use Botble\RealEstate\Enums\PropertyTypeEnum;
    $types = PropertyTypeEnum::labels();
@endphp

@if (count($types))
    @php
        $type_choice = request()->input('type', PropertyTypeEnum::RENT());
    @endphp
    <ul>
        @foreach ($types as $key => $type)
            <li>
                <input id="cp-{{ $key }}" value="{{ $key }}" class="checkbox-custom" name="type"
                    type="radio" @if ($type_choice == $key) checked @endif>
                <label for="cp-{{ $key }}" class="checkbox-custom-label">{{ $type }}</label>
            </li>
        @endforeach
    </ul>
@endif
