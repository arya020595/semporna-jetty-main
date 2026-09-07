<script setup>
import intus from "intus";
import { isRequired } from "intus/rules";
import { computed, ref, watch } from "vue";
import VModal from "@/Shared/VModal.vue";
import VInputWithLabel from "@/Shared/Form/VInputWithLabel.vue";
import { useForm } from "@inertiajs/vue3";
import VSelectDefaultWithLabel from "@/Shared/Form/VSelectDefaultWithLabel.vue";
import VButton from "@/Shared/Buttons/VButton.vue";
import VRadioWithLabel from "../../../Shared/Form/VRadioWithLabel.vue";
import VDevider from "../../../Shared/VDevider.vue";
import VSelectMultipleWithLabel from "../../../Shared/Form/VSelectMultipleWithLabel.vue";
import VSelectSearchableWithLabel from "../../../Shared/Form/VSelectSearchableWithLabel.vue";

const refModal = ref(null);

const props = defineProps({
    index: Number,
    value: Object,
    title: String,
    arrNationality: Array,
    arrActivity: Array,
    isStaffForm: {
        type: Boolean,
        default: false,
    },
    departureCode: {
        type: [Number, String],
        default: null,
    },
});

const emits = defineEmits(["onCancel", "onUpdate", "onSave"]);

const isNationalityFreeText = ref(
    props.value?.nationality_id || !props.value?.nationality_name
        ? false
        : true,
);

const form = useForm({
    id: props.value?.id ?? null,
    name: props.value?.name ?? "",
    ic_no: props.value?.ic_no ?? "",
    nationality_id: props.value?.nationality_id ?? "",
    nationality_name: props.value?.nationality_name ?? "",
    year_of_birth: props.value?.year_of_birth ?? "",
    age: props.value?.age ?? "",
    gender: props.value?.gender ?? "",
    next_of_kin: props.value?.next_of_kin ?? "",
    emergency_contact: props.value?.emergency_contact ?? "",
    activity_ids: props.value?.activity_ids ?? [],
    is_stay_resort: props.value?.is_stay_resort ?? 0,
    resort_name: props.value?.resort_name ?? "",
    ticket_code: props.value?.ticket_code ?? "",
});

const labelWidth = 4;

const optionOther = { id: "other", description: "Others (please specify)" };

const SEMPORNA_JETTY_CODE = "DPTR_00001";
const SEAFEST_JETTY_CODE = "DPTR_00002";
const NATIONALITY_LOCAL = [18, 19];

const isSempornaJetty = computed(() => {
    return (
        props.departureCode == SEMPORNA_JETTY_CODE ||
        props.departureCode == SEAFEST_JETTY_CODE
    );
});

watch(
    () => props.departureCode,
    (newValue) => {
        if (newValue == SEMPORNA_JETTY_CODE || newValue == SEAFEST_JETTY_CODE) {
            form.ticket_code = "";
        }
    },
);

watch(
    () => props.value,
    (newVal) => {
        if (!newVal) return;

        form.id = newVal.id ?? null;
        form.name = newVal.name ?? "";
        form.ic_no = newVal.ic_no ?? "";
        form.nationality_id = newVal.nationality_id ?? "";
        form.nationality_name = newVal.nationality_name ?? "";
        form.year_of_birth = newVal.year_of_birth ?? "";
        form.age = newVal.age ?? "";
        form.gender = newVal.gender ?? "";
        form.next_of_kin = newVal.next_of_kin ?? "";
        form.emergency_contact = newVal.emergency_contact ?? "";
        form.activity_ids = Array.isArray(newVal.activity_ids)
            ? [...newVal.activity_ids]
            : newVal.activity_ids
              ? [newVal.activity_ids]
              : [];
        form.is_stay_resort = newVal.is_stay_resort ?? 0;
        form.resort_name = newVal.resort_name ?? "";

        isNationalityFreeText.value =
            !newVal.nationality_id && newVal.nationality_name ? true : false;

        form.ticket_code = newVal.ticket_code;
    },
    { immediate: true },
);

const isAgeAutoFilled = ref(false);

const isLocal = computed(() => {
    return NATIONALITY_LOCAL.includes(form.nationality_id ?? false);
});

const autoCalculateAge = (icNo) => {
    const cleaned = icNo.replace(/[-\s]/g, "");

    // Check if it's a standard 12-digit MyKad
    if (/^\d{12}$/.test(cleaned)) {
        const yearPrefix = parseInt(cleaned.substring(0, 2));
        const currentYear = new Date().getFullYear();

        // MyKad logic (current year 2026):
        // We assume 00-26 (or dynamic based on current year) is 2000s
        // 27-99 is 1900s
        const currentYearShort = currentYear % 100;

        let birthYear = 0;
        if (yearPrefix <= currentYearShort) {
            birthYear = 2000 + yearPrefix;
        } else {
            birthYear = 1900 + yearPrefix;
        }

        const age = currentYear - birthYear;

        return age;

        // if (age >= 1 && age <= 120) {
        //     return age;
        // }
    }

    return null;
};

const autoCalculateBirthYear = (icNo) => {
    const cleaned = icNo.replace(/[-\s]/g, "");
    if (/^\d{12}$/.test(cleaned)) {
        const yearPrefix = parseInt(cleaned.substring(0, 2));
        const currentYearShort = new Date().getFullYear() % 100;

        if (yearPrefix <= currentYearShort) {
            return 2000 + yearPrefix;
        } else {
            return 1900 + yearPrefix;
        }
    }
    return null;
};

watch(
    () => form.ic_no,
    (newValue) => {
        if (!newValue) {
            isAgeAutoFilled.value = false;
            return;
        }
        const sanitized = newValue.replace(/[^a-zA-Z0-9]/g, "");

        if (sanitized !== newValue) {
            form.ic_no = sanitized;
            return;
        }

        const calculatedAge = autoCalculateAge(newValue);
        const calculatedYear = autoCalculateBirthYear(newValue);

        if (calculatedAge !== null && calculatedYear !== null) {
            form.age = calculatedAge;
            form.year_of_birth = calculatedYear;
            isAgeAutoFilled.value = true;
        } else {
            // If user manually changes IC to something invalid for calc, remove indicator
            isAgeAutoFilled.value = false;
        }
    },
);

watch(
    () => form.age,
    (newValue) => {
        // If user manually changes age, remove indicator
        // If value matches calculation from current IC, keep true, else false.
        const calculated = autoCalculateAge(form.ic_no);
        if (calculated !== null && parseInt(newValue) === calculated) {
            isAgeAutoFilled.value = true;
        } else {
            isAgeAutoFilled.value = false;
        }
    },
);

watch(
    () => form.year_of_birth,
    (newValue) => {
        if (!newValue || isNaN(parseInt(newValue))) return;
        const yob = parseInt(newValue);
        if (yob < 1900 || yob > new Date().getFullYear()) return;
        const calculatedAge = new Date().getFullYear() - yob;
        form.age = calculatedAge;
    },
);

watch(
    () => form.nationality_id,
    (newValue) => {
        if (newValue === "other") {
            form.nationality_name = "";
        } else if (newValue && newValue !== "other") {
            isNationalityFreeText.value = false;
            const selectedNationality = props.arrNationality.find(
                (n) => n.id == newValue,
            );
            form.nationality_name = selectedNationality?.title ?? "";
        }
    },
);

const resetForm = () => {
    form.id = "";
    form.name = "";
    form.ic_no = "";
    form.nationality_id = "";
    form.nationality_name = "";
    form.year_of_birth = "";
    form.age = "";
    form.gender = "";
    form.next_of_kin = "";
    form.emergency_contact = "";
    form.activity_ids = [];
    form.is_stay_resort = 0;
    form.resort_name = "";
    isNationalityFreeText.value = false;
};

const closeNationalityFreetext = () => {
    isNationalityFreeText.value = false;
    form.nationality_id = null;
    form.nationality_name = "";
};

const submit = () => {
    const baseRules = {
        name: [isRequired()],
        ic_no: [isRequired()],
        nationality_name: [isRequired()],
        year_of_birth: [], // Optional
        age: [isRequired()],
        gender: [isRequired()],
    };

    const staffExtra = props.isStaffForm
        ? {}
        : {
              next_of_kin: [isRequired()],
              emergency_contact: [isRequired()],
              activity_ids: [isRequired()],
              resort_name: form.is_stay_resort == 1 ? [isRequired()] : [],
          };

    const validation = intus.validate(form.data(), {
        ...baseRules,
        ...staffExtra,
    });

    form.clearErrors();

    if (form.age < 1 || form.age > 100) {
        form.setError({ age: "Age must be between 1 and 100" });
        return;
    }

    if (validation.passes()) {
        const formData = {
            ...form.data(),
            nationality_id: isNationalityFreeText.value
                ? null
                : form.nationality_id,
            activity_names: form.activity_ids
                .map((id) => props.arrActivity.find((a) => a.id == id)?.title)
                .filter(Boolean)
                .join(", "),
        };

        // If it is staff
        if (props.isStaffForm) {
            formData.next_of_kin = null;
            formData.emergency_contact = null;
            formData.activity_id = null;
            formData.activity_name = null;
            formData.is_stay_resort = null;
            formData.resort_name = null;
        }

        emits("onSave", formData);
        refModal.value.closeModal();
    } else {
        const errors = validation.errors();
        const customErrors = {
            name: "Passenger Name is required",
            ic_no: "IC/Passport Number is required",
            nationality_name: "Please select a Nationality",
            age: "Age must be between 1 and 100",
            gender: "Please select Gender",
            next_of_kin: "Next of Kin is required",
            emergency_contact: "Emergency Contact is required",
            activity_ids: "Please select at least one Activity",
            resort_name: "Resort Name is required",
        };

        const customizedErrors = {};
        for (const key in errors) {
            customizedErrors[key] = customErrors[key] || errors[key];
        }

        form.setError(customizedErrors);
    }
};

const onCloseModal = () => {
    resetForm();
    emits("onCancel");
};

const ARR_GENDER = [
    {
        id: "M",
        description: "Male",
    },
    {
        id: "F",
        description: "Female",
    },
];

const widthLabel = 4;
</script>
<template>
    <VModal
        ref="refModal"
        :title="title"
        size="modal-xl"
        @onClose="onCloseModal"
    >
        <template v-slot:body>
            <div class="row px-3">
                <div class="col-lg-6">
                    <div class="mb-3">
                        <VInputWithLabel
                            :elId="index + '_name'"
                            label="Name"
                            type="text"
                            v-model:value="form.name"
                            :error="form.errors.name"
                            :widthLabel="widthLabel"
                            :widthInput="12 - widthLabel"
                        />
                    </div>
                    <div class="mb-3 position-relative">
                        <template v-if="!isNationalityFreeText">
                            <VSelectSearchableWithLabel
                                :elId="index + '_nationality_id'"
                                label="Nationality"
                                :options="
                                    arrNationality
                                        .map((item) => ({
                                            id: item.id,
                                            description: item.title,
                                        }))
                                        .concat(optionOther)
                                "
                                placeholder="Search nationality"
                                v-model:value="form.nationality_id"
                                v-model:displayValue="form.nationality_name"
                                :error="form.errors.nationality_name"
                                :widthLabel="widthLabel"
                                :widthInput="12 - widthLabel"
                                :allowOther="true"
                                @onSelectOther="isNationalityFreeText = true"
                            />
                        </template>
                        <template v-else>
                            <VInputWithLabel
                                :elId="index + '_nationality_name'"
                                label="Nationality"
                                type="text"
                                v-model:value="form.nationality_name"
                                :error="form.errors.nationality_name"
                                :widthLabel="widthLabel"
                                :widthInput="12 - widthLabel"
                            />
                            <div
                                v-if="isNationalityFreeText"
                                class="position-absolute close-freetext"
                            >
                                <button
                                    class="btn btn-sm"
                                    @click="closeNationalityFreetext"
                                >
                                    <i class="fas fa-times-circle"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                    <div class="mb-3">
                        <VInputWithLabel
                            :elId="index + '_ic_no'"
                            :label="isLocal ? 'IC No.' : 'Passport No.'"
                            v-model:value="form.ic_no"
                            type="text"
                            :error="form.errors.ic_no"
                            :additionalAttr="{ maxlength: isLocal ? 12 : null }"
                            :widthLabel="widthLabel"
                            :widthInput="12 - widthLabel"
                        />
                    </div>
                    <div class="mb-3">
                        <VInputWithLabel
                            :elId="index + '_year_of_birth'"
                            label="Year of Birth"
                            type="number"
                            v-model:value="form.year_of_birth"
                            :error="form.errors.year_of_birth"
                            :widthLabel="widthLabel"
                            :widthInput="4"
                            :additionalAttr="{ min: 1900, max: 2030 }"
                        />
                    </div>
                    <div class="mb-3">
                        <VInputWithLabel
                            :elId="index + '_age'"
                            label="Age"
                            type="number"
                            v-model:value="form.age"
                            :error="form.errors.age"
                            :widthLabel="widthLabel"
                            :widthInput="4"
                            :additionalAttr="{ min: 1, max: 100 }"
                        />
                        <div v-if="isAgeAutoFilled" class="row mt-1">
                            <div class="col-sm-4 offset-sm-4">
                                <small class="text-success">
                                    <i class="fas fa-check-circle me-1"></i>
                                    Auto-calculated
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3">
                        <VSelectDefaultWithLabel
                            :elId="index + '_gender'"
                            label="Gender"
                            :options="ARR_GENDER"
                            v-model:value="form.gender"
                            :error="form.errors.gender"
                            :widthLabel="widthLabel"
                            :widthInput="6"
                        />
                    </div>
                    <div v-if="!props.isStaffForm" class="mb-3">
                        <VInputWithLabel
                            :elId="index + '_next_of_kin'"
                            label="Next of Kin"
                            type="text"
                            v-model:value="form.next_of_kin"
                            :error="form.errors.next_of_kin"
                            :widthLabel="widthLabel"
                            :widthInput="12 - widthLabel"
                        />
                    </div>
                    <div v-if="!props.isStaffForm" class="mb-3">
                        <VInputWithLabel
                            :elId="index + '_emergency_contact'"
                            label="Emergency Contact"
                            type="text"
                            v-model:value="form.emergency_contact"
                            :error="form.errors.emergency_contact"
                            :widthLabel="widthLabel"
                            :widthInput="12 - widthLabel"
                        />
                    </div>
                    <div v-if="!props.isStaffForm" class="mb-3">
                        <VSelectMultipleWithLabel
                            :elId="index + '_activity_ids'"
                            label="Activities"
                            :options="
                                arrActivity.map((item) => ({
                                    id: item.id,
                                    description: item.title,
                                }))
                            "
                            v-model:value="form.activity_ids"
                            :error="form.errors.activity_ids"
                            :widthLabel="widthLabel"
                            :widthInput="12 - widthLabel"
                        />
                    </div>
                </div>
            </div>
            <div v-if="!props.isStaffForm" class="px-3 mb-3">
                <VDevider />
            </div>
            <div class="row px-3">
                <div v-if="!props.isStaffForm" class="col-lg-6 mb-1 mb-lg-3">
                    <VRadioWithLabel
                        elId="is_stay_resort"
                        v-model:value="form.is_stay_resort"
                        label="This passenger staying at resort?"
                        :options="[
                            { id: 1, description: 'Yes' },
                            { id: 0, description: 'No' },
                        ]"
                        :error="form.errors?.is_stay_resort"
                        :widthLabel="widthLabel"
                        :widthInput="12 - widthLabel"
                    />
                </div>
                <div
                    v-if="!props.isStaffForm && form.is_stay_resort == 1"
                    class="col-lg-6 mb-3"
                >
                    <VInputWithLabel
                        elId="resort_name"
                        v-model:value="form.resort_name"
                        label="Resort Name:"
                        :error="form.errors?.resort_name"
                        :widthLabel="widthLabel"
                        :widthInput="12 - widthLabel"
                    />
                </div>
            </div>
            <div v-if="!props.isStaffForm" class="px-3 mb-3">
                <VDevider />
            </div>
            <div
                v-if="!props.isStaffForm && !isSempornaJetty"
                class="row px-3 mb-3"
            >
                <div class="col-lg-6">
                    <VInputWithLabel
                        elId="ticket_code"
                        v-model:value="form.ticket_code"
                        label="Ticket:"
                        :error="form.errors?.ticket_code"
                        :widthLabel="widthLabel"
                        :widthInput="12 - widthLabel"
                    />
                </div>
            </div>
        </template>
        <template v-slot:footer>
            <div class="text-end">
                <VButton btnStyle="btn-success" @onClick="submit">
                    {{ form.id ? "Edit" : "Add" }}
                    {{ isStaffForm ? "Staff" : "Passenger" }}</VButton
                >
            </div>
        </template>
    </VModal>
</template>

<style lang="css" scoped>
.close-freetext {
    top: 4px;
    right: 4px;
}
</style>
