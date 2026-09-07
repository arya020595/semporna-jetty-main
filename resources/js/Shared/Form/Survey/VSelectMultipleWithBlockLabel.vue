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
});

const selGroup = ref(
    props.options.filter((item) => props.value.includes(item.id))
);

const emits = defineEmits(["update:value"]);

watch(selGroup, (newValue) => {
    if (newValue.length > 0) {
        let data = newValue.map((item) => item.id);

        emits("update:value", data);
    }
});
</script>

<template>
    <div class="row align-items-sm-center">
        <label :for="elId" class="col-12 label-size fw-bold mb-2">
            {{ label }}
        </label>
        <div class="col-12">
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
        <div class="col-12 text-danger font-error">
            {{ error }}
        </div>
    </div>
</template>
