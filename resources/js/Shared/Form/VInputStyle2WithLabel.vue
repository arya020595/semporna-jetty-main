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
    error: String,
});

defineEmits(["update:value"]);
</script>

<template>
    <div class="">
        <label :for="elId" class="form-label label-size fw-bold">
            {{ label }}
        </label>
        <input
            :id="elId"
            :type="type"
            class="form-control"
            @input="$emit('update:value', $event.target.value)"
            :value="value"
            :class="{ 'is-invalid': error }"
        />
    </div>
    <div v-if="error">
        <div class="text-danger font-error">
            {{ error }}
        </div>
    </div>
</template>
