<script setup>
import { ref, watch } from "vue";

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

const emits = defineEmits(["update:value"]);

const month = ref("");
const year = ref("");

watch(
    () => props.value,
    (newValue) => {
        const arrStr = newValue.split("-");

        month.value = arrStr[0] ?? "";
        year.value = arrStr[1] ?? "";
    }
);

watch(month, (newValue) => {
    // Remove all non-digit characters
    let formatted = newValue.replace(/\D/g, "");
    formatted = formatted.substring(0, 2);
    month.value = formatted;
    emits("update:value", formatted + "-" + year.value);
});

watch(year, (newValue) => {
    let formatted = newValue.replace(/\D/g, "");
    formatted = formatted.substring(0, 4);
    year.value = formatted;
    emits("update:value", month.value + "-" + formatted);
});
</script>

<template>
    <div class="row align-items-sm-center">
        <label :for="elId" class="col-12 label-size fw-bold mb-2">
            {{ label }}
        </label>
        <div :class="'col-12'">
            <div class="row">
                <div class="col-4">
                    <input
                        :id="elId + '_month'"
                        type="text"
                        class="form-control"
                        v-model="month"
                        :class="{ 'is-invalid': error }"
                        placeholder="MM"
                    />
                </div>
                <div class="col-8">
                    <input
                        :id="elId + '_year'"
                        type="text"
                        class="form-control"
                        v-model="year"
                        :class="{ 'is-invalid': error }"
                        placeholder="YYYY"
                    />
                </div>
            </div>
        </div>
    </div>
    <div v-if="error" class="row">
        <div class="col-12 text-danger font-error">
            {{ error }}
        </div>
    </div>
</template>
