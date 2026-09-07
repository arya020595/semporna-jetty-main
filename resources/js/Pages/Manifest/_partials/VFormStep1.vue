<script setup>
import intus from "intus";
import { isRequired } from "intus/rules";

import VInputWithLabel from "@/Shared/Form/VInputWithLabel.vue";
import VSelectDefaultWithLabel from "@/Shared/Form/VSelectDefaultWithLabel.vue";
import VDevider from "@/Shared/VDevider.vue";
import { useForm } from "@inertiajs/vue3";
import VButtonSubmit from "@/Shared/Buttons/VButtonSubmit";
import { computed, ref, watch, onMounted } from "vue";
import VOptionFreeTextInput from "../../../Shared/Manifest/VOptionFreeTextInput.vue";
import { formatAlphaNumericOnly } from "../../../Helpers/number";
import VRadioWithLabel from "../../../Shared/Form/VRadioWithLabel.vue";
import { values } from "lodash";
import { useFormDataStore } from "../../../Store/formData";

const formdataStore = useFormDataStore();

const props = defineProps({
    additional: Object,
});

const { boats, company } = props.additional;

const isAssistantFreeText = ref(
    props.additional.manifest?.assistant_id ||
        !props.additional.manifest.assistant_name
        ? false
        : true
);

onMounted(() => {
    const savedIsRent = localStorage.getItem("manifest_is_rent");
    const savedIsAssistantFreeText = localStorage.getItem(
        "manifest_is_assistant_freetext"
    );

    if (savedIsRent !== null) {
        form.is_rent = JSON.parse(savedIsRent);
    }

    if (savedIsAssistantFreeText !== null) {
        isAssistantFreeText.value = JSON.parse(savedIsAssistantFreeText);
    }
});

const emits = defineEmits("onNext");

const MANIFEST_TYPE_RENTAL = 2;

const form = useForm({
    is_rent:
        props.additional.manifest?.is_rent ??
        props.additional.manifest?.type == MANIFEST_TYPE_RENTAL,
    departure_date: props.additional.manifest?.departure_date ?? "",
    departure_time: props.additional.manifest?.departure_time ?? "",
    company_id: props.additional.manifest?.company_id ?? company.id,
    company_name: props.additional.manifest?.company_name ?? company.name,

    boat_id: props.additional.manifest?.boat_id ?? null,
    boat_number: props.additional.manifest?.boat_number ?? null,

    boatman_id: props.additional.manifest?.boatman_id ?? null,
    boatman_name: props.additional.manifest?.boatman_name ?? null,
    boatman_mate_no: props.additional.manifest?.boatman_mate_no ?? null,
    seaman_no: props.additional.manifest?.seaman_no ?? null,
    boatman_ic_no: props.additional.manifest?.boatman_ic_no ?? null,

    assistant_id: props.additional.manifest?.assistant_id ?? null,
    assistant_name: props.additional.manifest?.assistant_name ?? null,
    assistant_mate_no: props.additional.manifest?.assistant_mate_no ?? null,
    assistant_ic_no: props.additional.manifest?.assistant_ic_no ?? null,
    assistant_seaman_no: props.additional.manifest?.assistant_seaman_no ?? null,

    is_dive_activity: props.additional.manifest?.is_dive_activity ?? null,
});

const selBoat = computed(() => {
    return boats.find((item) => item.id == form.boat_id);
});

const curentDate = computed(() => {
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, "0"); // Months are zero-based
    const dd = String(today.getDate()).padStart(2, "0");

    const formattedDate = `${yyyy}-${mm}-${dd}`;
    return formattedDate;
});

watch(
    () => form.boat_id,
    (newValue) => {
        if (!selBoat.value) return {};
        form.boat_number = selBoat.value.number;
    }
);
watch(
    () => form.boatman_id,
    (newValue) => {
        if (!selBoat.value) return {};

        const boatman = selBoat.value.boatman_main.find(
            (item) => item.id == newValue
        );

        form.boatman_name = boatman?.name;
        form.boatman_ic_no = boatman?.ic_no;
        form.boatman_mate_no = boatman?.mate_card;
        form.seaman_no = boatman?.seaman_card_no;

        return boatman;
    }
);

watch(
    () => form.assistant_id,
    (newValue) => {
        if (!selBoat.value) return {};

        const boatman = selBoat.value.boatman_asst.find(
            (item) => item.id == newValue
        );

        form.assistant_name = boatman?.name ?? "";
        form.assistant_ic_no = boatman?.ic_no ?? "";
        form.assistant_mate_no = boatman?.mate_card ?? "";
        form.assistant_seaman_no = boatman?.seaman_card_no ?? "";

        if (newValue == "other") {
            isAssistantFreeText.value = true;
        }
    }
);

const handleClickNext = () => {
    const validation = intus.validate(form.data(), {
        departure_date: [isRequired()],
        departure_time: [isRequired()],
        company_id: [isRequired()],
        company_name: [isRequired()],
        boat_id: form.is_rent ? [] : [isRequired()],
        boat_number: [isRequired()],

        boatman_id: form.is_rent ? [] : [isRequired()],
        boatman_name: [isRequired()],
        boatman_mate_no: [isRequired()],
        seaman_no: [isRequired()],
        boatman_ic_no: [isRequired()],

        assistant_id: [],
        assistant_name: [isRequired()],
        assistant_mate_no: [],
        assistant_ic_no: [isRequired()],
        assistant_seaman_no: [],
    });

    form.clearErrors();
    if (validation.passes()) {
        clearLocalStorage();
        emits("onNext", form.data());
    } else {
        form.setError(validation.errors());
    }
};

const isFreeText = computed(() => {
    return form.is_rent == 1;
});

watch(
    () => form.is_rent,
    (newValue) => {
        localStorage.setItem("manifest_is_rent", JSON.stringify(newValue));

        if (newValue == 1) {
            isAssistantFreeText.value = true;
            resetForm();
        } else {
            isAssistantFreeText.value = false;
            resetForm();
        }

        const currentData = formdataStore.getForm();
        formdataStore.setForm({
            ...currentData,
            is_rent: newValue,
        });
    }
);

watch(
    () => isAssistantFreeText.value,
    (newValue) => {
        localStorage.setItem(
            "manifest_is_assistant_freetext",
            JSON.stringify(newValue)
        );
    }
);

const clearLocalStorage = () => {
    localStorage.removeItem("manifest_is_rent");
    localStorage.removeItem("manifest_is_assistant_freetext");
};

const resetForm = () => {
    form.boat_id = "";
    form.boat_number = "";

    form.boatman_id = "";
    form.boatman_name = "";
    form.boatman_mate_no = "";
    form.seaman_no = "";
    form.boatman_ic_no = "";

    form.assistant_id = "";
    form.assistant_name = "";
    form.assistant_mate_no = "";
    form.assistant_ic_no = "";
    form.assistant_seaman_no = "";
};

watch(
    () => form.boatman_ic_no,
    async (newValue) => {
        form.boatman_ic_no = await formatAlphaNumericOnly(newValue);
    }
);

const closeFreetext = () => {
    form.assistant_id = null;
    form.assistant_name = "";
    form.assistant_ic_no = "";
    form.assistant_mate_no = "";
    form.assistant_seaman_no = "";
    isAssistantFreeText.value = false;
};

const optionOther = { id: "other", description: "Others (please specify)" };

const labelWidth = 4;
</script>

<template>
    <div class="d-flex justify-content-between">
        <h5 class="d-flex align-items-center mb-0">Departure Date/Time</h5>
    </div>

    <VDevider />
    <div class="row mt-4">
        <div class="col-md-6 mb-3">
            <VInputWithLabel
                elId="departure_date"
                label="Departure Date"
                type="date"
                v-model:value="form.departure_date"
                :error="form.errors.departure_date"
                :additionalAttr="{ min: curentDate }"
            />
        </div>
        <div class="col-md-6 mb-3">
            <VInputWithLabel
                elId="departure_time"
                label="Departure Time"
                type="time"
                v-model:value="form.departure_time"
                :error="form.errors.departure_time"
            />
        </div>
    </div>

    <div class="d-flex justify-content-between mt-5">
        <h5 class="d-flex align-items-center mb-0">
            Boat & Company Information &nbsp;
            <span v-if="isFreeText" class="text-primary">(Rental Boat)</span>
        </h5>
    </div>
    <VDevider />
    <div class="row mt-4">
        <div class="col-md-6 mb-3">
            <VRadioWithLabel
                elId="is_dive_activity"
                v-model:value="form.is_dive_activity"
                label="Any Diving/Snorkeling Activity Involved?"
                :options="[
                    { id: 1, description: 'Yes' },
                    { id: 0, description: 'No' },
                ]"
                :error="form.errors?.is_dive_activity"
                :widthLabel="labelWidth"
                :widthInput="12 - labelWidth"
            />
        </div>
        <div class="col-md-6 mb-3">
            <VRadioWithLabel
                elId="is_rent"
                v-model:value="form.is_rent"
                label="Rent Boat:"
                :options="[
                    { id: 1, description: 'Yes' },
                    { id: 0, description: 'No' },
                ]"
                :error="form.errors?.is_rent"
                :widthLabel="labelWidth"
                :widthInput="12 - labelWidth"
            />
            <label for="type">(If boat is not available on the departure date)</label>
        </div>
    </div>
    <VDevider class="mb-4" />
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="mb-3">
                <VInputWithLabel
                    elId="company_name"
                    label="Company Name"
                    type="text"
                    v-model:value="form.company_name"
                    :error="form.errors.company_name"
                    :additionalAttr="{ disabled: true }"
                />
            </div>
            <div class="mb-3">
                <VOptionFreeTextInput
                    elId="boat"
                    :options="
                        boats.map((item) => ({
                            id: item.id.toString(),
                            description: item.number,
                        }))
                    "
                    v-model:valueId="form.boat_id"
                    v-model:valueName="form.boat_number"
                    labelId="Boat Number"
                    labelName="Boat Number"
                    :error="form.errors.boat_id || form.errors.boat_number"
                    :isFreeText="isFreeText"
                />
            </div>
            <div class="mb-3">
                <VOptionFreeTextInput
                    elId="boatman"
                    :options="
                        selBoat?.boatman_main?.map((item) => ({
                            id: item.id.toString(),
                            description: item.name,
                        }))
                    "
                    v-model:valueId="form.boatman_id"
                    v-model:valueName="form.boatman_name"
                    labelId="Boatman"
                    labelName="Boatman"
                    :error="form.errors.boatman_id || form.errors.boatman_name"
                    :isFreeText="isFreeText"
                />
            </div>
            <div class="mb-3">
                <VInputWithLabel
                    elId="boatman_mate_no"
                    label="Mate No."
                    type="text"
                    v-model:value="form.boatman_mate_no"
                    :error="form.errors.boatman_mate_no"
                    :additionalAttr="{ disabled: !isFreeText }"
                />
            </div>
            <div class="mb-3">
                <VInputWithLabel
                    elId="seaman_no"
                    label="Seaman No."
                    type="text"
                    v-model:value="form.seaman_no"
                    :error="form.errors.seaman_no"
                    :additionalAttr="{ disabled: !isFreeText }"
                />
            </div>
            <div class="mb-3">
                <VInputWithLabel
                    elId="boatman_ic_no"
                    label="IC No."
                    type="text"
                    v-model:value="form.boatman_ic_no"
                    :error="form.errors.boatman_ic_no"
                    :additionalAttr="{ disabled: !isFreeText }"
                />
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3 position-relative">
                <VOptionFreeTextInput
                    elId="assistant"
                    :options="
                        selBoat?.boatman_asst
                            ?.map((item) => ({
                                id: item.id.toString(),
                                description: item.name,
                            }))
                            .concat(optionOther)
                    "
                    v-model:valueId="form.assistant_id"
                    v-model:valueName="form.assistant_name"
                    labelId="Assistant Name"
                    labelName="Assistant Name"
                    :error="
                        form.errors.assistant_id || form.errors.assistant_name
                    "
                    :isFreeText="isAssistantFreeText"
                    :widthLabel="labelWidth"
                    :widthInput="12 - labelWidth"
                />
                <div
                    v-if="form.is_rent == 0 && isAssistantFreeText"
                    class="position-absolute close-freetext"
                >
                    <button class="btn btn-sm" @click="closeFreetext">
                        <i class="fas fa-times-circle"></i>
                    </button>
                </div>
            </div>
            <div class="mb-3">
                <VInputWithLabel
                    elId="assistant_ic_no"
                    label="IC No. / Passport No."
                    type="text"
                    v-model:value="form.assistant_ic_no"
                    :error="form.errors.assistant_ic_no"
                    :additionalAttr="{ disabled: !isAssistantFreeText }"
                    :widthLabel="labelWidth"
                    :widthInput="12 - labelWidth"
                />
            </div>
            <div class="mb-3">
                <VInputWithLabel
                    elId="assistant_mate_no"
                    label="Mate No. (If Available)"
                    type="text"
                    v-model:value="form.assistant_mate_no"
                    :error="form.errors.assistant_mate_no"
                    :additionalAttr="{ disabled: !isAssistantFreeText }"
                    :widthLabel="labelWidth"
                    :widthInput="12 - labelWidth"
                />
            </div>
            <div class="mb-3">
                <VInputWithLabel
                    elId="assistant_seaman_no"
                    label="Seaman No. (If Available)"
                    type="text"
                    v-model:value="form.assistant_seaman_no"
                    :error="form.errors.assistant_seaman_no"
                    :additionalAttr="{ disabled: !isAssistantFreeText }"
                    :widthLabel="labelWidth"
                    :widthInput="12 - labelWidth"
                />
            </div>

        </div>
    </div>
    <VDevider class="my-3" />
    <div class="text-end">
        <VButtonSubmit
            type="button"
            :isProcessing="form.processing"
            @onCLickSubmit="handleClickNext"
            attrClass="px-4"
        >
            Next
        </VButtonSubmit>
    </div>
</template>

<style lang="css" scoped>
.close-freetext {
    top: 4px;
    right: 4px;
}
</style>
