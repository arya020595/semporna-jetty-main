<script setup>
import VInputReadonlyWithLabel from "../../../Shared/Form/VInputReadonlyWithLabel.vue";
import VInputWithLabel from "../../../Shared/Form/VInputWithLabel.vue";

const props = defineProps({
    field: String,
    type: String,
    value: Object,
});

const labelWidth = 4;
</script>

<template>
    <div class="row">
        <div class="col-lg-8 mb-3">
            <VInputReadonlyWithLabel
                :elId="type + '_name_' + value.id"
                :label="field + ' Name'"
                type="text"
                :value="value.name"
                :isPlainText="false"
                :widthLabel="labelWidth"
                :widthInput="12 - labelWidth"
            />
        </div>
        <div class="col-lg-8 mb-3">
            <VInputReadonlyWithLabel
                :elId="type + '_ic_no_' + index"
                :label="field + ' No./IC No.'"
                type="text"
                :value="value.ic_no"
                :isPlainText="false"
                :widthLabel="labelWidth"
                :widthInput="12 - labelWidth"
            />
        </div>
    </div>
</template>
