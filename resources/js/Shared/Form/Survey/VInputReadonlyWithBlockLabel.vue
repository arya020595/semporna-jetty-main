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
    isPlainText: {
        type: Boolean,
        default: true,
    },
    error: String,
});

defineEmits(["update:value"]);
</script>

<template>
    <div class="row align-items-sm-center">
        <label :for="elId" :class="'col-12 label-size fw-bold mb-2'">
            {{ label }}
        </label>
        <div :class="'col-12 custom-position-relative'">
            <input
                :id="elId"
                :type="type"
                :class="{
                    'form-control-plaintext': isPlainText,
                    'form-control': !isPlainText,
                }"
                :value="value"
                readonly
            />

            <span v-if="unit != ''" class="custom-input-unit">{{ unit }}</span>
        </div>
    </div>
</template>
