<script setup>
import { ref } from "vue";

const props = defineProps({
    elId: {
        type: String,
        default: "",
    },
    type: {
        type: String,
        default: "radio",
    },
    label: String,
    value: String,
    options: Array,
    error: String,
});

defineEmits(["update:value"]);
</script>

<template>
    <div class="row align-items-sm-center">
        <label :for="elId" :class="'col-12 label-size fw-bold mb-2'">
            {{ label }}
        </label>
        <div :class="'col-12'" style="min-height: 2.5em">
            <div v-for="option in options" :key="option.id" class="form-check">
                <input
                    :id="elId + option.id"
                    :name="elId"
                    :type="type"
                    class="form-check-input"
                    :class="{ 'is-invalid': error }"
                    @input="$emit('update:value', $event.target.value)"
                    :value="option.id"
                    :checked="option.id == value"
                />
                <label class="form-check-label" :for="elId + option.id">
                    {{ option.description }}
                </label>
            </div>
        </div>
    </div>
    <div v-if="error" class="row">
        <div class="col-12 text-danger font-error">
            {{ error }}
        </div>
    </div>
</template>
