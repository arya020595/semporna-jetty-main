<script setup>
import { ref, watch } from "vue";

import debounce from "lodash/debounce";
import VButton from "../Buttons/VButton.vue";
import VInputWithLabel from "../Form/VInputWithLabel.vue";
import VOptionFreeTextInput from "./VOptionFreeTextInput.vue";
import { formatAlphaNumericOnly, formatNumberOnly } from "../../Helpers/number";

const props = defineProps({
    title: String,
    field: String,
    type: String,
    options: Array,
    value: Object,
    arrSelected: Array,
    isShowDelete: Boolean,
    index: Number,
});

const boatman = ref(
    props.value ?? {
        id: "",
        boatman_id: "",
        name: "",
        ic_no: "",
    }
);

const optionOther = { id: "other", description: "Others (please specify)" };

const isFreeText = ref(
    props.value?.boatman_id || !props.value.name ? false : true
);

const labelWidth = 4;

const emits = defineEmits(["update:value", "onDelete"]);

watch(
    () => props.value,
    (newValue) => {
        boatman.value.id = newValue?.id;
        boatman.value.boatman_id = newValue?.boatman_id;
        boatman.value.name = newValue?.name;
        boatman.value.ic_no = newValue?.ic_no;
    }
);

watch(
    () => boatman.value.boatman_id,
    (newValue) => {
        if (newValue == "other") {
            isFreeText.value = true;
            boatman.value = {
                boatman_id: "other",
                name: "",
                ic_no: "",
            };
            return;
        }

        const selBoatman = props.options.find((item) => item.id == newValue);

        if (selBoatman) {
            boatman.value = {
                boatman_id: selBoatman?.id,
                name: selBoatman?.name,
                ic_no: selBoatman?.ic_no,
            };
        }

        update(boatman.value);
    }
);

watch(
    () => boatman.value.ic_no,
    async (newValue) => {
        boatman.value.ic_no = await formatAlphaNumericOnly(newValue);
    }
);

const update = debounce((data) => {
    emits("update:value", data);
}, 500);

const closeFreetext = () => {
    isFreeText.value = false;
    boatman.value.boatman_id = null;
    boatman.value.name = "";
    boatman.value.ic_no = "";
};
</script>

<template>
    <div class="row">
        <div class="col-lg-11">
            <div class="row">
                <div class="col-lg-6 mb-3 position-relative">
                    <VOptionFreeTextInput
                        :attrId="type + '_boatman_id_' + index"
                        :options="
                            options
                                .map((item) => ({
                                    id: item.id,
                                    description: item.name,
                                }))
                                .concat(optionOther)
                        "
                        v-model:valueId="boatman.boatman_id"
                        v-model:valueName="boatman.name"
                        :labelId="field + ' Name'"
                        :labelName="field + ' Name'"
                        :error="''"
                        :isFreeText="isFreeText"
                        :widthLabel="labelWidth"
                        :arrSelected="
                            arrSelected.filter((item) => item != 'other')
                        "
                    />
                    <div
                        v-if="isFreeText"
                        class="position-absolute close-freetext"
                    >
                        <button class="btn btn-sm" @click="closeFreetext">
                            <i class="fas fa-times-circle"></i>
                        </button>
                    </div>
                </div>
                <div class="col-lg-6 mb-3">
                    <VInputWithLabel
                        :elId="type + '_ic_no_' + index"
                        :label="field + ' No./IC No./Passport No.'"
                        type="text"
                        v-model:value="boatman.ic_no"
                        :widthLabel="labelWidth"
                        :widthInput="12 - labelWidth"
                        :additionalAttr="{ disabled: !isFreeText }"
                    />
                </div>
            </div>
        </div>
        <div class="col-lg-1">
            <div class="row mb-3" v-if="isShowDelete">
                <div class="col-lg-6">
                    <div class="row">
                        <div class="col-sm-8 offset-sm-4">
                            <VButton
                                btnStyle="btn-danger btn-sm"
                                @onClick="emits('onDelete')"
                                ><span class="material-icons"
                                    >delete</span
                                ></VButton
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="css" scoped>
.close-freetext {
    top: 4px;
    right: 20px;
}
</style>
