<script setup>
import { ref } from "vue";

const props = defineProps({
    elId: {
        Type: String,
        default: "",
    },
    label: String,
    placeholder: String,
    value: String | Number,
    type: {
        type: String,
        default: "text",
    },
    unit: {
        type: String,
        default: "",
    },
    error: String,
});

defineEmits(["update:value"]);
</script>

<template>
    <div class="row align-items-sm-center">
        <label :for="elId" class="col-12 label-size fw-bold mb-2">
            {{ label }}
        </label>
        <div :class="'col-12 custom-position-relative'">
            <input
                :id="elId"
                :type="type"
                class="form-control text-left"
                @input="$emit('update:value', $event.target.value)"
                :value="value"
                :class="{ 'is-invalid': error }"
                :placeholder="placeholder"
            />

            <span v-if="unit != ''" class="custom-input-unit">{{ unit }}</span>
        </div>
    </div>
    <div v-if="error" class="row">
        <div class="col-12 text-danger font-error">
            {{ error }}
        </div>
    </div>
</template>
