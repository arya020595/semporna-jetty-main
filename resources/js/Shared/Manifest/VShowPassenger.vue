<script setup>
import VDevider from "@/Shared/VDevider.vue";
import VInputWithLabel from "../Form/VInputWithLabel.vue";

const props = defineProps({
    index: Number,
    value: Object,
    isStaff: {
        type: Boolean,
        default: false,
    },
});

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

const labelWidth = 4;
</script>

<template>
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <VInputWithLabel
                    :elId="index + '_name'"
                    label="Guest Name"
                    type="text"
                    :value="value.name"
                    :widthLabel="labelWidth"
                    :widthInput="12 - labelWidth"
                    :additionalAttr="{ disabled: true }"
                />
            </div>
            <div class="mb-3">
                <VInputWithLabel
                    :elId="index + '_ic_no'"
                    label="IC/Passport No."
                    type="text"
                    :value="value.ic_no"
                    :widthLabel="labelWidth"
                    :widthInput="12 - labelWidth"
                    :additionalAttr="{ disabled: true }"
                />
            </div>
            <div class="mb-3">
                <VInputWithLabel
                    :elId="index + '_year_of_birth'"
                    label="Year of Birth"
                    :value="value.year_of_birth"
                    :widthLabel="labelWidth"
                    :widthInput="12 - labelWidth"
                    :additionalAttr="{ disabled: true }"
                />
            </div>
            <div class="mb-3">
                <VInputWithLabel
                    :elId="index + '_nationality_id'"
                    label="Nationality"
                    :value="value.nationality_name"
                    :widthLabel="labelWidth"
                    :widthInput="12 - labelWidth"
                    :additionalAttr="{ disabled: true }"
                />
            </div>
            <div class="mb-3">
                <VInputWithLabel
                    :elId="index + '_age'"
                    label="Age"
                    :value="value.age"
                    :widthLabel="labelWidth"
                    :widthInput="12 - labelWidth"
                    :additionalAttr="{ disabled: true }"
                />
            </div>
            <div class="mb-3" v-if="value.ticket_code">
                <VInputWithLabel
                    :elId="index + '_ticket_code'"
                    label="Ticket"
                    :value="value.ticket_code"
                    :widthLabel="labelWidth"
                    :widthInput="12 - labelWidth"
                    :additionalAttr="{ disabled: true }"
                />
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <VInputWithLabel
                    :elId="index + '_gender'"
                    label="Gender"
                    :value="value.gender_text"
                    :widthLabel="labelWidth"
                    :widthInput="12 - labelWidth"
                    :additionalAttr="{ disabled: true }"
                />
            </div>
            <div class="mb-3" v-if="!isStaff">
                <VInputWithLabel
                    :elId="index + '_next_of_kin'"
                    label="Next of Kin"
                    type="text"
                    :value="value.next_of_kin"
                    :widthLabel="labelWidth"
                    :widthInput="12 - labelWidth"
                    :additionalAttr="{ disabled: true }"
                />
            </div>
            <div class="mb-3" v-if="!isStaff">
                <VInputWithLabel
                    :elId="index + '_emergency_contact'"
                    label="Emergency Contact"
                    type="text"
                    :value="value.emergency_contact"
                    :widthLabel="labelWidth"
                    :widthInput="12 - labelWidth"
                    :additionalAttr="{ disabled: true }"
                />
            </div>
            <div class="mb-3" v-if="!isStaff">
                <VInputWithLabel
                    :elId="index + '_activity_id'"
                    label="Activity"
                    :value="value.activity_name"
                    :widthLabel="labelWidth"
                    :widthInput="12 - labelWidth"
                    :additionalAttr="{ disabled: true }"
                />
            </div>
        </div>
    </div>
    <VDevider class="mb-4" />
</template>
