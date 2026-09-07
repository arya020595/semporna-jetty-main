<script setup>
import { watch, ref } from "vue";
import VueMultiselect from "vue-multiselect";

const props = defineProps({
    elId: {
        Type: String,
        default: "",
    },
    label: String,
    value: Array,
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
});

const selGroup = ref(
    props.options.filter((item) => props.value.includes(item.id))
);

const emits = defineEmits(["update:value"]);

watch(selGroup, (newValue) => {
    if (newValue.length > 0) {
        let data = newValue.map((item) => item.id);

        emits("update:value", data);
    } else {
        emits("update:value", []);
    }
});
</script>

<template>
    <div class="row align-items-sm-center">
        <label
            :for="elId"
            :class="'col-sm-' + widthLabel + ' label-size fw-bold mb-sm-0 mb-2'"
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
                :multiple="true"
                :options="options"
                :class="{ 'border-error': error }"
            >
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
