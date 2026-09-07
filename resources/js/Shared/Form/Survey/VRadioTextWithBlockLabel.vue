<script setup>
import { ref, watch } from "vue";

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
    otherOptionLabel: String,
    otherOptionPlaceholder: {
        type: String,
        default: "Other Options ....",
    },
    error: String,
    layout: {
        type: String,
        default: "horizontal",
    },
    widthLabel: {
        type: Number,
        default: 3,
    },
    widthInput: {
        type: Number,
        default: 9,
    },
});

const emits = defineEmits(["update:value"]);

const dataOther = ref("");
const dataValue = ref("");
const isOther = ref(false);

watch(dataValue, (value) => {
    emitSelection(value);
});

const emitSelection = (value, otherValue = "") => {
    let curValue = value;
    if (value == "other") {
        isOther.value = true;
        curValue = otherValue;
    } else {
        isOther.value = false;
        dataOther.value = "";
    }
    emits("update:value", curValue);
};

const checkOther = (value, options) => {
    let check = value ? true : false;
    const others = options.filter((item) => {
        if (item.id == value) {
            check = false;
        }
    });

    return check;
};

isOther.value = checkOther(props.value, props.options);
if (isOther.value) {
    dataOther.value = props.value;
}

const labelClass =
    props.layout == "horizontal"
        ? "col-12 label-size fw-bold mb-2"
        : "col-sm-" +
          props.widthLabel +
          " text-sm-end label-size mb-sm-0 fw-bold mb-2 mt-1";
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
            <div v-for="option in options" :key="option.id" class="form-check">
                <input
                    :id="elId + option.id"
                    :name="elId"
                    :type="type"
                    class="form-check-input"
                    :class="{ 'is-invalid': error }"
                    v-model="dataValue"
                    :value="option.id"
                    :checked="option.id == value"
                />
                <label class="form-check-label" :for="elId + option.id">
                    {{ option.description }}
                </label>
            </div>

            <div class="form-check">
                <input
                    :id="elId + 'other-checkbox'"
                    :name="elId"
                    :type="type"
                    class="form-check-input"
                    :class="{ 'is-invalid': error }"
                    v-model="dataValue"
                    value="other"
                    :checked="isOther"
                />
                <label class="form-check-label" :for="elId + 'other-checkbox'">
                    {{ otherOptionLabel }}
                </label>

                <input
                    class="form-control form-control-sm mt-1"
                    :class="{ 'd-none': !isOther }"
                    type="text"
                    v-model="dataOther"
                    @keyup="emitSelection('other', $event.target.value)"
                    :placeholder="otherOptionPlaceholder"
                />
            </div>
        </div>
    </div>
    <div v-if="error" class="row">
        <div :class="errorClass + ' text-danger font-error'">
            {{ error }}
        </div>
    </div>
</template>
