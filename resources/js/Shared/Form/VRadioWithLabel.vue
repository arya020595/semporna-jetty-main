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
    widthLabel: {
        default: 3,
        type: Number,
    },
    widthInput: {
        default: 9,
        type: Number,
    },
    error: String,
});

defineEmits(["update:value"]);

const labelClass =
    props.layout == "horizontal"
        ? "col-12 label-size fw-bold mb-2"
        : "col-sm-" +
          props.widthLabel +
          " label-size mb-sm-0 fw-bold mb-2 mt-1";
const inputClass =
    props.layout == "horizontal"
        ? "col-12"
        : "col-sm-" + props.widthInput + " custom-position-relative";

const errorClass =
    props.layout == "horizontal"
        ? "col-12"
        : "offset-sm-" + props.widthLabel + " col-sm-" + props.widthInput;
</script>

<template>
    <div class="row">
        <label :for="elId" :class="labelClass">
            {{ label }}
        </label>
        <div :class="inputClass" style="min-height: 2.5em">
            <div
                v-for="option in options"
                :key="option.id"
                class="form-check form-check-inline"
            >
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
        <div :class="errorClass + ' text-danger font-error'">
            {{ error }}
        </div>
    </div>
</template>
