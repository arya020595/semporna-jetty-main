<script setup>
import { onActivated, ref, computed } from "vue";
import VModal from "../../../Shared/VModal.vue";
import VInputWithLabel from "@/Shared/Form/VInputWithLabel.vue";
import { useForm } from "@inertiajs/vue3";
import VUploadButton from "../../../Shared/Form/VUploadButton.vue";
import VDevider from "@/Shared/VDevider.vue";
import VFormBoatman from "./VFormBoatman.vue";
import VFormBoatmanAssistance from "./VFormBoatmanAssistance.vue";
import VButton from "../../../Shared/Buttons/VButton.vue";
import VAlert from "@/Shared/VAlert.vue";
import intus from "intus";
import { isRequired } from "intus/rules";

const refModal = ref(null);

const props = defineProps({
    value: Object,
    urlSubmit: String,
    isReadOnly: {
        type: Boolean,
        default: false,
    },
});

const emits = defineEmits(["onCancel", "onSuccess"]);

const isShowAlert = ref(false);

onActivated(() => {
    isShowAlert.value = false;
});

const minDate = computed(() => {
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, "0");
    const day = String(today.getDate()).padStart(2, "0");
    return `${year}-${month}-${day}`;
});

const form = useForm({
    id: props.value.id,
    capacity: props.value.capacity,
    license_expiry_date: props.value.license_expiry_date,
    license: props.value.license,
    license_file_existing: props.value.license_file_existing ?? [
        null,
        null,
        null,
    ],
    license_file: [null, null, null],
    boatman:
        props.value.boatman?.length > 0
            ? props.value.boatman
            : [
                  {
                      boat_id: props.value.id,
                      name: "",
                      ic_no: "",
                      mate_card: "",
                      seaman_card_no: "",

                      ic_no_file: null,
                      mate_card_file: null,
                      seaman_card_no_file: null,

                      ic_no_file_existing: null,
                      mate_card_file_existing: null,
                      seaman_card_file_existing: null,
                  },
              ],
    asst:
        props.value.asst?.length > 0
            ? props.value.asst
            : [
                  {
                      boat_id: props.value.id,
                      name: "",
                      ic_no: "",
                      mate_card: "",
                      seaman_card_no: "",
                  },
              ],
});

const labelWidth = 4;

const resetForm = () => {
    form.id = null;
    form.number = "";
    form.license = "";
    form.license_file = [null, null, null];
    form.license_file_existing = [null, null, null];

    form.boatman = [
        {
            boat_id: props.value.id,
            name: "",
            ic_no: "",
            mate_card: "",
            seaman_card_no: "",

            ic_no_file: null,
            mate_card_file: null,
            seaman_card_no_file: null,

            ic_no_file_existing: null,
            mate_card_file_existing: null,
            seaman_card_file_existing: null,
        },
    ];

    form.asst = [
        {
            boat_id: props.value.id,
            name: "",
            ic_no: "",
            mate_card: "",
            seaman_card_no: "",
        },
    ];
};

const addBoatman = () => {
    form.boatman.push({
        boat_id: props.value.id,
        name: "",
        ic_no: "",
        mate_card: "",
        seaman_card_no: "",

        ic_no_file: null,
        mate_card_file: null,
        seaman_card_file: null,
    });
};

const deleteBoatman = (index) => {
    const formData = form.data();
    form.boatman = [];

    form.boatman = formData.boatman.filter((item, i) => {
        return i != index;
    });
};

const addAsst = () => {
    form.asst.push({
        boat_id: props.value.id,
        name: "",
        ic_no: "",
        mate_card: "",
    });
};

const deleteAsst = (index) => {
    const formData = form.data();
    form.asst = [];

    form.asst = formData.asst.filter((item, i) => {
        return i != index;
    });
};

const submitAndAddNewBoat = () => {
    form.clearErrors();
    const errors = {};

    // Validate basic fields
    if (!form.capacity) {
        errors.capacity = "Boat capacity is required";
    } else if (form.capacity < 1) {
        errors.capacity = "Boat capacity must be at least 1";
    }

    if (!form.license) {
        errors.license = "Boat license is required";
    }

    if (!form.license_expiry_date) {
        errors.license_expiry_date = "License expiry date is required";
    }

    // Validate License Page 1 - COMPULSORY
    if (!form.id && !form.license_file[0] && !form.license_file_existing[0]) {
        errors["license_file.0"] = "Boat License Page 1 is required";
    }

    // Validate Boatman - ALL FIELDS COMPULSORY
    form.boatman.forEach((boatman, index) => {
        if (!boatman.name || !boatman.name.trim()) {
            errors[`boatman.${index}.name`] = "Boatman name is required";
        }
        if (!boatman.ic_no || !boatman.ic_no.trim()) {
            errors[`boatman.${index}.ic_no`] = "Boatman IC is required";
        }
        if (!boatman.mate_card || !boatman.mate_card.trim()) {
            errors[`boatman.${index}.mate_card`] =
                "Boatman Mate Card No. is required";
        }
        if (!boatman.seaman_card_no || !boatman.seaman_card_no.trim()) {
            errors[`boatman.${index}.seaman_card_no`] =
                "Boatman Seaman Card No. is required";
        }
    });

    // Validate Assistant Boatman - NAME & IC Optional
    form.asst.forEach((asst, index) => {
        if (!asst.name || !asst.name.trim()) {
            errors[`asst.${index}.name`] = "Assistant Boatman name is required";
        }
        if (!asst.ic_no || !asst.ic_no.trim()) {
            errors[`asst.${index}.ic_no`] = "Assistant Boatman IC is required";
        }
    });

    // If there are errors, show them and stop
    if (Object.keys(errors).length > 0) {
        form.setError(errors);
        isShowAlert.value = true;
        return;
    }

    // Submit if validation passes
    form.post(props.urlSubmit, {
        onSuccess: () => {
            resetForm();
            refModal.value.closeModal();
        },
        onFinish: () => {
            isShowAlert.value = true;
        },
    });
};

const onCloseModal = () => {
    if (!props.isReadOnly) {
        resetForm();
    }
    emits("onCancel");
};

const maxLicensePage = 3;
</script>

<template>
    <VModal ref="refModal" size="modal-xl" @onClose="onCloseModal">
        <template v-slot:body>
            <div class="px-3">
                <div class="d-flex justify-content-between">
                    <h5 class="d-flex align-items-center mb-0">Boat Details</h5>
                </div>
                <VDevider />

                <VAlert v-if="isShowAlert && !isReadOnly" />

                <div class="row mt-4">
                    <div class="col-lg-8 mb-3">
                        <VInputWithLabel
                            elId="capacity"
                            label="Boat Capacity"
                            type="number"
                            v-model:value="form.capacity"
                            :error="form.errors.capacity"
                            :widthLabel="labelWidth"
                            :widthInput="3"
                            :additionalAttr="{
                                placeholder: 'Boat Capacity',
                                class: 'text-start',
                                disabled: isReadOnly,
                            }"
                        />
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 mb-3">
                        <VInputWithLabel
                            elId="license_expiry_date"
                            label="License Expiry Date"
                            type="date"
                            v-model:value="form.license_expiry_date"
                            :error="form.errors.license_expiry_date"
                            :widthLabel="labelWidth"
                            :widthInput="12 - labelWidth"
                            :additionalAttr="{
                                placeholder: 'License Expiry Date',
                                disabled: isReadOnly,
                                min: isReadOnly ? null : minDate,
                            }"
                        />
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 mb-3">
                        <VInputWithLabel
                            elId="license"
                            label="Boat License"
                            type="text"
                            v-model:value="form.license"
                            :error="form.errors.license"
                            :widthLabel="labelWidth"
                            :widthInput="12 - labelWidth"
                            :additionalAttr="{
                                placeholder: 'Boat License Number',
                                readonly: isReadOnly,
                            }"
                        />
                    </div>
                </div>

                <!-- Edit Mode: Upload buttons -->
                <div v-if="!isReadOnly" class="row">
                    <div class="col-lg-8 mb-3">
                        <div class="row" v-for="i in maxLicensePage" :key="i">
                            <div
                                class="offset-sm-4 offset-0 mb-2 d-flex align-items-center gap-2"
                            >
                                <VUploadButton
                                    :elId="`license_file_${i - 1}`"
                                    v-model:existing="
                                        form.license_file_existing[i - 1]
                                    "
                                    v-model:value="form.license_file[i - 1]"
                                    :error="
                                        form.errors[`license_file.${i - 1}`]
                                    "
                                    >Upload Boat License Page
                                    {{ i }}</VUploadButton
                                >
                                <span
                                    v-if="i === 1"
                                    class="text-danger"
                                    style="font-size: 0.9em; font-weight: 600"
                                >
                                    (Required)
                                </span>
                                <span
                                    v-else
                                    class="text-muted"
                                    style="font-size: 0.9em"
                                >
                                    (Optional)
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Read Only Mode: View buttons -->
                <div v-else class="row mb-3">
                    <div class="col-lg-8">
                        <div class="row">
                            <label
                                class="col-sm-4 label-size fw-bold mb-sm-0 mb-2"
                            >
                                Boat License Files
                            </label>
                            <div class="col-sm-8">
                                <a
                                    v-for="(
                                        fileUrl, index
                                    ) in form.license_file_existing"
                                    :key="index"
                                    v-show="fileUrl"
                                    class="btn btn-success mx-1 mb-2"
                                    target="_blank"
                                    :href="fileUrl"
                                    :title="`View License Page ${index + 1}`"
                                >
                                    <span class="material-icons"
                                        >visibility</span
                                    >
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <VDevider />

                <div class="my-4">
                    <VFormBoatman
                        v-for="(boatman, index) in form.boatman"
                        :key="'boatman-' + index"
                        v-model:value="form.boatman[index]"
                        :index="index"
                        :isReadOnly="isReadOnly"
                        @onDelete="deleteBoatman(index)"
                        :isShowDelete="form.boatman?.length > 1 && !isReadOnly"
                        :errors="{
                            name: form.errors[`boatman.${index}.name`],
                            ic_no: form.errors[`boatman.${index}.ic_no`],
                            mate_card:
                                form.errors[`boatman.${index}.mate_card`],
                            seaman_card_no:
                                form.errors[`boatman.${index}.seaman_card_no`],
                        }"
                    />
                    <div v-if="!isReadOnly" class="row">
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-sm-8 offset-sm-4">
                                    <VButton
                                        btnStyle="btn-success"
                                        @onClick="addBoatman"
                                    >
                                        <span class="material-icons me-1"
                                            >add_circle_outline</span
                                        >
                                        Additional Boatman</VButton
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <VDevider />

                <div class="my-4">
                    <VFormBoatmanAssistance
                        v-for="(boatman, index) in form.asst"
                        :key="'asst-' + index"
                        v-model:value="form.asst[index]"
                        :index="index"
                        :isReadOnly="isReadOnly"
                        @onDelete="deleteAsst(index)"
                        :isShowDelete="form.asst?.length > 1 && !isReadOnly"
                        :errors="{
                            name: form.errors[`asst.${index}.name`],
                            ic_no: form.errors[`asst.${index}.ic_no`],
                            mate_card: form.errors[`asst.${index}.mate_card`],
                            seaman_card_no:
                                form.errors[`asst.${index}.seaman_card_no`],
                        }"
                    />

                    <div v-if="!isReadOnly" class="row">
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-sm-8 offset-sm-4">
                                    <VButton
                                        btnStyle="btn-success"
                                        @onClick="addAsst"
                                    >
                                        <span class="material-icons me-1"
                                            >add_circle_outline</span
                                        >
                                        Additional Asst. Boatman</VButton
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <template v-slot:footer>
            <div class="text-end">
                <VButton
                    v-if="isReadOnly"
                    btnStyle="btn-secondary"
                    @onClick="onCloseModal"
                >
                    Close
                </VButton>
                <VButton
                    v-else
                    btnStyle="btn-success"
                    @onClick="submitAndAddNewBoat"
                >
                    Submit Boat Details
                </VButton>
            </div>
        </template>
    </VModal>
</template>
