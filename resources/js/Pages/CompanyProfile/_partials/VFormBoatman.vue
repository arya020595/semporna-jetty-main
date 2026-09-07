<script setup>
import VInputWithLabel from "@/Shared/Form/VInputWithLabel.vue";
import { useForm } from "@inertiajs/vue3";
import VUploadButton from "../../../Shared/Form/VUploadButton.vue";
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

    ic_no_file: null,
    mate_card_file: null,
    seaman_card_file: null,

    ic_no_file_existing: props.value?.ic_no_file_existing,
    mate_card_file_existing: props.value?.mate_card_file_existing,
    seaman_card_file_existing: props.value?.seaman_card_file_existing,
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

        form.ic_no_file_existing = newValue?.ic_no_file_existing;
        form.mate_card_file_existing = newValue?.mate_card_file_existing;
        form.seaman_card_file_existing = newValue?.seaman_card_file_existing;
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
                label="Boatman Name"
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
                :elId="'mate_card' + index"
                label="Boatman Mate Card No."
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
        <div v-if="!isReadOnly" class="col-lg-4 mb-3 d-flex align-items-start">
            <VUploadButton
                :elId="'mate_card_file' + index"
                v-model:existing="form.mate_card_file_existing"
                v-model:value="form.mate_card_file"
                >Upload Mate Card</VUploadButton
            >
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8 mb-3">
            <VInputWithLabel
                :elId="'ic_no' + index"
                label="Boatman IC"
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
        <div v-if="!isReadOnly" class="col-lg-4 mb-3 d-flex align-items-start">
            <VUploadButton
                :elId="'ic_no_file' + index"
                v-model:existing="form.ic_no_file_existing"
                v-model:value="form.ic_no_file"
                >Upload Boatman IC</VUploadButton
            >
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
        <div v-if="!isReadOnly" class="col-lg-4 mb-3 d-flex align-items-start">
            <VUploadButton
                :elId="'seaman_card_no_file_' + index"
                v-model:existing="form.seaman_card_file_existing"
                v-model:value="form.seaman_card_file"
                >Upload Seaman Card</VUploadButton
            >
        </div>
    </div>

    <div v-if="isReadOnly" class="row mb-3">
        <div class="col-lg-8">
            <div class="row">
                <label class="col-sm-4 label-size fw-bold mb-sm-0 mb-2">
                    Documents
                </label>
                <div class="col-sm-8">
                    <a
                        v-if="form.ic_no_file_existing"
                        :href="form.ic_no_file_existing"
                        target="_blank"
                        class="btn btn-success mx-1 mb-2"
                        title="View IC File"
                    >
                        <span class="material-icons">visibility</span>
                    </a>
                    <a
                        v-if="form.mate_card_file_existing"
                        :href="form.mate_card_file_existing"
                        target="_blank"
                        class="btn btn-success mx-1 mb-2"
                        title="View Mate Card"
                    >
                        <span class="material-icons">visibility</span>
                    </a>
                    <a
                        v-if="form.seaman_card_file_existing"
                        :href="form.seaman_card_file_existing"
                        target="_blank"
                        class="btn btn-success mx-1 mb-2"
                        title="View Seaman Card"
                    >
                        <span class="material-icons">visibility</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div v-if="isShowDelete" class="row mb-3">
        <div class="col-lg-8">
            <div class="row">
                <div class="col-sm-8 offset-sm-4">
                    <VButton btnStyle="btn-danger" @onClick="emits('onDelete')">
                        <span class="material-icons me-1">delete</span>
                        Delete Boatman</VButton
                    >
                </div>
            </div>
        </div>
    </div>
</template>
