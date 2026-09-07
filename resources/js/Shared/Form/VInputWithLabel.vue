<script setup>
import { ref } from "vue";

const props = defineProps({
    elId: {
        Type: String,
        default: "",
    },
    label: String,
    value: String | Number,
    type: {
        type: String,
        default: "text",
    },
    unit: {
        type: String,
        default: "",
    },
    widthLabel: {
        type: Number,
        default: 3,
    },
    widthInput: {
        type: Number,
        default: 9,
    },
    additionalAttr: Object,
    additionalLabelAttr: Object,
    error: String,
});

defineEmits(["update:value"]);
</script>

<template>
    <div class="row align-items-sm-center">
        <label
            :for="elId"
            :class="'col-sm-' + widthLabel + ' label-size fw-bold mb-sm-0 mb-2'"
            v-bind="additionalLabelAttr"
        >
            {{ label }}
        </label>
        <div :class="'col-sm-' + widthInput + ' custom-position-relative'">
            <input
                :id="elId"
                :type="type"
                class="form-control"
                @input="
                    $emit(
                        'update:value',
                        type == 'number'
                            ? parseInt($event.target.value)
                            : $event.target.value
                    )
                "
                :value="value"
                :class="{ 'is-invalid': error }"
                v-bind="additionalAttr"
            />

            <span v-if="unit != ''" class="custom-input-unit">{{ unit }}</span>
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
