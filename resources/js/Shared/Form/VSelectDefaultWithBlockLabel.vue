<script setup>
const props = defineProps({
    elId: {
        Type: String,
        default: "",
    },
    label: String,
    value: String,
    options: Array,
    excludeOptions: {
        type: Array,
        default: [],
    },
    placeholder: {
        type: String,
        default: "Please Select Option",
    },
    error: String,
});

defineEmits(["update:value"]);
</script>

<template>
    <div class="row" :class="{ 'question-error': error }">
        <label
            v-if="label != ''"
            :for="elId"
            class="col-12 label-size fw-bold mb-2"
        >
            {{ label }}
        </label>
        <div class="col-12">
            <select
                :id="elId"
                class="form-select"
                :class="{ 'is-invalid': error }"
                @change="$emit('update:value', $event.target.value)"
            >
                <option value="" disabled selected>
                    {{ placeholder }}
                </option>
                <option
                    v-for="option in options"
                    :key="option.id"
                    :value="option.id"
                    :selected="option.id == value"
                    :disabled="excludeOptions.includes(option.id)"
                >
                    {{ option.description }}
                </option>
            </select>
        </div>
    </div>

    <div v-if="error" class="row">
        <div class="col-12 text-danger font-error">
            {{ error }}
        </div>
    </div>
</template>
