<script setup>
import { ref } from "vue";

const props = defineProps({
    elId: {
        Type: String,
        default: "",
    },
    label: String,
    value: String | Number,
    error: String,
});

defineEmits(["update:value"]);
</script>

<template>
    <div>
        <div class="row align-items-sm-center">
            <label :for="elId" :class="'col-12 label-size fw-bold mb-2'">
                {{ label }}
            </label>
            <div :class="'col-12 custom-position-relative'">
                <textarea
                    :id="elId"
                    rows="7"
                    class="form-control"
                    @input="$emit('update:value', $event.target.value)"
                    :class="{ 'is-invalid': error }"
                    :value="value"
                    readonly
                ></textarea>
            </div>
        </div>
        <div v-if="error" class="row">
            <div class="col-12 text-danger font-error">
                {{ error }}
            </div>
        </div>
    </div>
</template>
