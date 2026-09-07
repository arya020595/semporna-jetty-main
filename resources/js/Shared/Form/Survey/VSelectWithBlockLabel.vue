<script setup>
import { watch, ref } from "vue";
import VueMultiselect from "vue-multiselect";

const props = defineProps({
    elId: {
        Type: String,
        default: "",
    },
    label: String,
    value: String | Number,
    options: Array,
    error: String,
});

const selGroup = ref(props.options.find((item) => item.id == props.value));

const emits = defineEmits(["update:value"]);

watch(selGroup, () => {
    if (selGroup.value) {
        emits("update:value", selGroup.value.id);
    }
});

watch(
    () => props.options,
    (newValue) => {
        selGroup.value = newValue.find((item) => item.id == props.value);
    }
);
</script>

<template>
    <div class="row align-items-sm-center">
        <label
            v-if="label != ''"
            :for="elId"
            :class="'col-12 label-size fw-bold mb-2'"
        >
            {{ label }}
        </label>
        <div :class="'col-12'">
            <VueMultiselect
                :id="elId"
                v-model="selGroup"
                label="description"
                track-by="id"
                open-direction="bottom"
                :placeholder="$t('plugins.vue_multiselect.placeholder')"
                :options="options"
                :class="{ 'border-error': error }"
            >
            </VueMultiselect>
        </div>
    </div>

    <div v-if="error" class="row">
        <div class="col-12 text-danger font-error">
            {{ error }}
        </div>
    </div>
</template>
