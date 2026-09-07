<script setup>
import { watch, ref } from "vue";
import VueMultiselect from "vue-multiselect";

const props = defineProps({
    elId: {
        Type: String,
        default: "",
    },
    label: String,
    value: String | Number,
    options: Array,
    otherOptionLabel: String,
    otherOptionPlaceholder: {
        type: String,
        default: "Other Options ....",
    },
    error: String,

    widthLabel: {
        type: Number,
        default: 3,
    },
    widthInput: {
        type: Number,
        default: 9,
    },
});

const selGroup = ref(props.options.find((item) => item.id == props.value));
const dataOther = ref("");
const dataValue = ref("");
const isOther = ref(false);
const optionOther = { id: "other", description: props.otherOptionLabel };

const emits = defineEmits(["update:value"]);

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
    selGroup.value = optionOther;
    dataOther.value = props.value;
}

watch(selGroup, (value) => {
    emitSelection(value.id, dataOther.value);
});
</script>

<template>
    <div class="row">
        <label
            v-if="label != ''"
            :for="elId"
            :class="
                'col-sm-' +
                widthLabel +
                ' label-size text-sm-end fw-bold mb-sm-0 mb-2 mt-sm-2 mt-0'
            "
        >
            {{ label }}
        </label>
        <div :class="'col-sm-' + widthInput">
            <VueMultiselect
                :id="elId"
                v-model="selGroup"
                label="description"
                track-by="id"
                open-direction="bottom"
                :options="options.concat([optionOther])"
                :class="{ 'border-error': error }"
                :placeholder="$t('plugins.vue_multiselect.placeholder')"
            >
            </VueMultiselect>
            <div class="mt-2">
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
        <div class="col-sm-9 offset-sm-3 text-danger font-error">
            {{ error }}
        </div>
    </div>
</template>
