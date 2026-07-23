<div class="row">
    <div class="col-md-6">
        <facilities-component
            :selected_facilities="{{ json_encode($selectedFacilities) }}"
            :facilities="{{ json_encode($facilities) }}"
            v-slot="{ items, facilities, addRow, deleteRow, removeSelectedItem }"
        >
            <div class="mb-3">
                <div class="row g-2 mb-2" v-for="(item, index) in items">
                    <div class="col">
                        <select
                            :name="`facilities[${index}1][id]`"
                            class="select-search-full"
                            @change="removeSelectedItem"
                            data-placeholder="{{ trans('plugins/real-estate::dashboard.select_facility') }}"
                        >
                            <option value="0">{{ trans('plugins/real-estate::dashboard.select_facility') }}</option>
                            <option
                                v-for="(facility, index) in facilities"
                                :key="index"
                                :value="facility.id"
                                :selected="facility.id === item.id"
                            >
                                @{{ facility.name }}
                            </option>
                        </select>
                    </div>
                    <div class="col">
                        <input
                            type="text"
                            :name="`facilities[${index}1][distance]`"
                            v-model="item.distance"
                            class="form-control"
                            placeholder="{{ trans('plugins/real-estate::dashboard.distance') }}"
                        />
                    </div>
                    <div class="col-auto">
                        <x-core::button
                            @click="deleteRow(index)"
                            icon="ti ti-trash"
                            :icon-only="true"
                        />
                    </div>
                </div>
            </div>

            <a href="javascript:void(0)" role="button" @click="addRow">{{ trans('plugins/real-estate::dashboard.add_new') }}</a>
        </facilities-component>
    </div>
</div>
