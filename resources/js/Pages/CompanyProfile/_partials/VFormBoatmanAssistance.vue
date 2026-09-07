<script setup>
import VInputWithLabel from "@/Shared/Form/VInputWithLabel.vue";
import { useForm } from "@inertiajs/vue3";
import VButton from "../../../Shared/Buttons/VButton.vue";
import { watch } from "vue";

import debounce from "lodash/debounce";
import { formatAlphaNumericOnly } from "../../../Helpers/number";

const props = defineProps({
    errors: Object,
    value: Object,
    isShowDelete: Boolean,
    index: Number,
    isReadOnly: {
        type: Boolean,
        default: false,
    },
});

const form = useForm({
    id: props.value?.id,
    boat_id: props.value?.boat_id,
    company_id: props.value?.company_id,
    name: props.value?.name,
    ic_no: props.value?.ic_no,
    mate_card: props.value?.mate_card,
    seaman_card_no: props.value?.seaman_card_no,
});

const labelWidth = 4;

const emits = defineEmits(["update:value", "onDelete"]);

watch(
    () => props.value,
    (newValue) => {
        form.id = newValue?.id;
        form.boat_id = newValue?.boat_id;
        form.company_id = newValue?.company_id;
        form.name = newValue?.name;
        form.ic_no = newValue?.ic_no;
        form.mate_card = newValue?.mate_card;
        form.seaman_card_no = newValue?.seaman_card_no;
    },
);

watch(form, (newValue) => {
    if (!props.isReadOnly) {
        update(newValue.data());
    }
});

watch(
    () => form.ic_no,
    async (newValue) => {
        if (!props.isReadOnly) {
            form.ic_no = await formatAlphaNumericOnly(newValue);
        }
    },
);

const update = debounce((data) => {
    emits("update:value", data);
}, 100);
</script>

<template>
    <div class="row">
        <div class="col-lg-8 mb-3">
            <VInputWithLabel
                :elId="'name' + index"
                label="Assistant Boatman Name"
                type="text"
                v-model:value="form.name"
                :error="errors.name"
                :widthLabel="labelWidth"
                :widthInput="12 - labelWidth"
                :additionalAttr="{
                    readonly: isReadOnly,
                }"
            />
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8 mb-3">
            <VInputWithLabel
                :elId="'ic_no' + index"
                label="Assistant Boatman IC No. / Passport No."
                type="text"
                v-model:value="form.ic_no"
                :error="errors.ic_no"
                :widthLabel="labelWidth"
                :widthInput="12 - labelWidth"
                :additionalAttr="{
                    readonly: isReadOnly,
                }"
            />
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8 mb-3">
            <VInputWithLabel
                :elId="'mate_card' + index"
                label="Assistant Boatman Mate No."
                type="text"
                v-model:value="form.mate_card"
                :error="errors.mate_card"
                :widthLabel="labelWidth"
                :widthInput="12 - labelWidth"
                :additionalAttr="{
                    readonly: isReadOnly,
                }"
            />
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8 mb-3">
            <VInputWithLabel
                :elId="'seaman_card_no' + index"
                label="Seaman Card No."
                type="text"
                v-model:value="form.seaman_card_no"
                :error="errors.seaman_card_no"
                :widthLabel="labelWidth"
                :widthInput="12 - labelWidth"
                :additionalAttr="{
                    readonly: isReadOnly,
                }"
            />
        </div>
    </div>

    <div class="row mb-3" v-if="isShowDelete">
        <div class="col-lg-8">
            <div class="row">
                <div class="col-sm-8 offset-sm-4">
                    <VButton btnStyle="btn-danger" @onClick="emits('onDelete')">
                        <span class="material-icons me-1">delete</span>
                        Delete Asst. Boatman</VButton
                    >
                </div>
            </div>
        </div>
    </div>
</template>
