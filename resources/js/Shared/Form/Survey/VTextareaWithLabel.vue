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
    widthLabel: {
        type: Number,
        default: 3,
    },
    widthInput: {
        type: Number,
        default: 9,
    },
    rows: {
        type: Number,
        default: 7,
    },
});

defineEmits(["update:value"]);
</script>

<template>
    <div>
        <div class="row align-items-sm-start">
            <label
                :for="elId"
                :class="
                    'col-sm-' + widthLabel + ' label-size fw-bold mb-sm-0 mb-2'
                "
            >
                {{ label }}
            </label>
            <div :class="'col-sm-' + widthInput + ' custom-position-relative'">
                <textarea
                    :id="elId"
                    :rows="rows"
                    class="form-control"
                    @input="$emit('update:value', $event.target.value)"
                    :class="{ 'is-invalid': error }"
                    :value="value"
                ></textarea>
            </div>
        </div>
        <div v-if="error" class="row">
            <div class="col-sm-9 offset-sm-3 text-danger font-error">
                {{ error }}
            </div>
        </div>
    </div>
</template>
