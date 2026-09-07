<script setup>
const props = defineProps({
    elId: {
        Type: String,
        default: "",
    },
    label: String,
    value: String,
    options: Array,
    excludeOptions: {
        type: Array,
        default: [],
    },
    placeholder: {
        type: String,
        default: "Please Select Option",
    },
    error: String,

    widthLabel: {
        type: Number,
        default: 3,
    },
    widthInput: {
        type: Number,
        default: 9,
    },
});

defineEmits(["update:value"]);
</script>

<template>
    <div class="row" :class="{ 'question-error': error }">
        <label
            v-if="label != ''"
            :for="elId"
            :class="
                'col-sm-' + widthLabel + ' label-size fw-bold mb-lg-0 mb-2 mt-2'
            "
        >
            {{ label }}
        </label>
        <div :class="'col-sm-' + widthInput">
            <select
                :id="elId"
                class="form-select"
                :class="{ 'is-invalid': error }"
                @change="$emit('update:value', $event.target.value)"
            >
                <option value="" disabled selected>
                    {{ placeholder }}
                </option>
                <option
                    v-for="option in options"
                    :key="option.id"
                    :value="option.id"
                    :selected="option.id == value"
                    :disabled="excludeOptions.includes(option.id)"
                >
                    {{ option.description }}
                </option>
            </select>
        </div>
    </div>

    <div v-if="error" class="row">
        <div
            :class="
                'col-sm-' +
                widthInput +
                ' offset-sm-' +
                widthLabel +
                ' text-danger font-error'
            "
        >
            {{ error }}
        </div>
    </div>
</template>
