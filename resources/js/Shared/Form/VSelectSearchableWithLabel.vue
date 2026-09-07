<script setup>
import { watch, ref, computed } from "vue";
import VueMultiselect from "vue-multiselect";

const props = defineProps({
    elId: {
        type: String,
        default: "",
    },
    label: String,
    value: String | Number,
    options: Array,
    error: String,
    widthLabel: {
        type: Number,
        default: 3,
    },
    widthInput: {
        type: Number,
        default: 9,
    },
    placeholder: {
        type: String,
        default: "Select option",
    },
    allowOther: {
        type: Boolean,
        default: false,
    },
    additionalAttr: Object,
});

const emits = defineEmits([
    "update:value",
    "update:displayValue",
    "onSelectOther",
]);

const selGroup = ref(props.options.find((item) => item.id == props.value));

watch(
    () => props.value,
    (newValue) => {
        selGroup.value = props.options.find((item) => item.id == newValue);
    }
);

watch(
    () => props.options,
    (newValue) => {
        selGroup.value = newValue.find((item) => item.id == props.value);
    }
);

watch(selGroup, (newValue) => {
    if (!newValue) {
        emits("update:value", null);
        emits("update:displayValue", "");
        return;
    }

    // Check if "other" option is selected
    if (newValue.id === "other" && props.allowOther) {
        emits("onSelectOther");
        emits("update:value", "other");
        emits("update:displayValue", "");
    } else {
        emits("update:value", newValue.id);
        emits("update:displayValue", newValue.description);
    }
});

const getSelGroup = () => {
    return selGroup.value;
};

const clearAll = () => {
    selGroup.value = null;
};

defineExpose({ getSelGroup, clearAll });
</script>

<template>
    <div class="row align-items-center">
        <label
            v-if="label != ''"
            :for="elId"
            :class="'col-sm-' + widthLabel + ' label-size fw-bold mb-0'"
        >
            {{ label }}
        </label>
        <div :class="'col-sm-' + widthInput">
            <VueMultiselect
                :id="elId"
                v-model="selGroup"
                label="description"
                track-by="id"
                open-direction="bottom"
                :options="options"
                :placeholder="placeholder"
                :class="{ 'border-error': error }"
                :searchable="true"
                :close-on-select="true"
                :show-labels="false"
                v-bind="additionalAttr"
            >
                <template #noResult>
                    <span>No results found</span>
                </template>
                <template #noOptions>
                    <span>List is empty</span>
                </template>
            </VueMultiselect>
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

<style src="vue-multiselect/dist/vue-multiselect.css"></style>

<style scoped>
:deep(.multiselect__tags) {
    min-height: 38px;
    padding: 6px 40px 0 8px;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
}

:deep(.multiselect__placeholder) {
    color: #6c757d;
    margin-bottom: 0;
    padding-top: 2px;
    line-height: 1.5;
}
</style>
