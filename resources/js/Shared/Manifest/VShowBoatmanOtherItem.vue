<script setup>
import VInputWithLabel from "../Form/VInputWithLabel.vue";

const props = defineProps({
    title: String,
    field: String,
    type: String,
    value: Object,
    index: Number,
});

const boatman = props.value;

const labelWidth = 4;
</script>

<template>
    <div class="row">
        <div class="col-lg-6 mb-3">
            <VInputWithLabel
                :elId="type + '_boatman_id_' + index"
                :label="field + ' Name'"
                type="text"
                :value="boatman.name"
                :widthLabel="labelWidth"
                :widthInput="12 - labelWidth"
                :additionalAttr="{ disabled: !isFreeText }"
            />
        </div>
        <div class="col-lg-6 mb-3">
            <VInputWithLabel
                :elId="type + '_ic_no_' + index"
                :label="field + ' IC No.'"
                type="text"
                :value="boatman.ic_no"
                :widthLabel="labelWidth"
                :widthInput="12 - labelWidth"
                :additionalAttr="{ disabled: !isFreeText }"
            />
        </div>
    </div>
</template>
