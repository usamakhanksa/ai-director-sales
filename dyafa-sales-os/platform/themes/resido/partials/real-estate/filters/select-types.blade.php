@php
    use Botble\RealEstate\Enums\PropertyTypeEnum;
    $types = PropertyTypeEnum::labels();
@endphp

<select id="select-type" data-placeholder="{{ __('Type') }}" class="form-control" name="type">
    <option value="">{{ __('-- Select --') }}</option>
    @foreach ($types as $key => $type)
        <option value="{{ $key }}" @if (request()->input('type') == $key) selected @endif>{{ $type }}
        </option>
    @endforeach
</select>
