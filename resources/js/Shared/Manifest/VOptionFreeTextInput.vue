<script setup>
import { reactive, ref, watch } from "vue";
import VSelectDefaultWithLabel from "../Form/VSelectDefaultWithLabel.vue";
import VInputWithLabel from "../Form/VInputWithLabel.vue";

const props = defineProps({
    attrId: String,
    options: Array,
    valueId: String | Number,
    valueName: String,
    labelId: String,
    labelName: String,
    error: String,
    isFreeText: Boolean,
    arrSelected: Array,
    widthLabel: {
        type: Number,
        default: 3,
    },
});

const form = reactive({
    id: props.valueId,
    name: props.valueName,
});

const emits = defineEmits(["update:valueId", "update:valueName"]);

watch(
    () => props.valueId,
    (newValue) => {
        form.id = props.valueId;
        form.name = props.name;
    }
);

watch(
    () => props.name,
    (newValue) => {
        form.id = props.valueId;
        form.name = props.name;
    }
);

watch(
    () => form,
    (newVal) => {
        if (props.isFreeText) {
            emits("update:valueId", null);
            emits("update:valueName", newVal.name);
        } else {
            emits("update:valueId", newVal.id);
        }
    },
    { deep: true }
);
</script>

<template>
    <VInputWithLabel
        v-if="isFreeText"
        :elId="attrId + '_name'"
        :label="labelName"
        type="text"
        v-model:value="form.name"
        :error="error"
        :widthLabel="widthLabel"
        :widthInput="12 - widthLabel"
    />
    <VSelectDefaultWithLabel
        v-else
        :elId="attrId + '_id'"
        :label="labelId"
        :options="options"
        :excludeOptions="arrSelected"
        v-model:value="form.id"
        :error="error"
        :widthLabel="widthLabel"
        :widthInput="12 - widthLabel"
    />
</template>
