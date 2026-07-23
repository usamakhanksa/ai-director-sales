@foreach ($features as $feature)
    <x-core::form.checkbox
        :label="$feature->name"
        name="features[]"
        :value="$feature->id"
        :checked="in_array($feature->id, $selectedFeatures)"
        inline
    />
@endforeach
