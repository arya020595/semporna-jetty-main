<script setup>
import { watch, ref } from "vue";
import VueMultiselect from "vue-multiselect";

const props = defineProps({
    elId: {
        Type: String,
        default: "",
    },
    label: String,
    value: Array,
    options: Array,
    otherOptionLabel: String,
    otherOptionPlaceholder: {
        type: String,
        default: "Other Options ....",
    },
    error: String,
});

const selGroup = ref(
    props.options.filter((item) => props.value.includes(item.id))
);

const emits = defineEmits(["update:value"]);

const dataValue = ref([]);
const dataOther = ref("");
const isOther = ref(false);
const optionOther = { id: "other", description: props.otherOptionLabel };

const keyOptions = props.options.map((item) => {
    return item.id;
});

const emitSelection = () => {
    let data = selGroup.value.map((item) => item.id);
    isOther.value = data.includes("other");

    if (isOther.value && dataOther.value) {
        data.push(dataOther.value);
    }

    emits(
        "update:value",
        data.filter((item) => item != "other")
    );
};

watch(selGroup, (newValue) => {
    if (newValue.length > 0) {
        emitSelection();
    }
});

watch(dataOther, () => {
    emitSelection();
});

const getOptionValues = (values, options) => {
    return values.filter((item) => {
        return options.includes(item);
    });
};

const getOtherValue = (values, options) => {
    const others = values.filter((item) => {
        return !options.includes(item);
    });

    return others.length > 0 ? others[0] : "";
};

dataValue.value = getOptionValues(props.value, keyOptions);
dataOther.value = getOtherValue(props.value, keyOptions);

if (dataOther.value != "") {
    selGroup.value.push(optionOther);
}
</script>

<template>
    <div class="row align-items-sm-center">
        <label :for="elId" class="col-12 label-size fw-bold mb-2">
            {{ label }}
        </label>
        <div class="col-12">
            <VueMultiselect
                :id="elId"
                v-model="selGroup"
                label="description"
                track-by="id"
                open-direction="bottom"
                :multiple="true"
                :options="options.concat([optionOther])"
                :class="{ 'border-error': error }"
            >
            </VueMultiselect>
            <div class="mt-2">
                <input
                    class="form-control form-control-sm mt-1"
                    :class="{ 'd-none': !isOther }"
                    type="text"
                    v-model="dataOther"
                    @input="emitSelection"
                    :placeholder="otherOptionPlaceholder"
                />
            </div>
        </div>
    </div>

    <div v-if="error" class="row">
        <div class="col-12 text-danger font-error">
            {{ error }}
        </div>
    </div>
</template>
