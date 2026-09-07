<script setup>
import { Link } from "@inertiajs/vue3";
import VDevider from "@/Shared/VDevider.vue";

import VInputWithLabel from "@/Shared/Form/VInputWithLabel.vue";
import VShowBoatDetails from "./VShowBoatDetails.vue";
import VButton from "@/Shared/Buttons/VButton.vue";

const props = defineProps({
    company: Object,
    urlEdit: String,
});
const emits = defineEmits(["onNext", "onBack"]);

const labelWidth = 4;

const onClickNext = () => {
    emits("onNext");
};

const onClickBack = () => {
    emits("onBack");
};
</script>

<template>
    <div class="d-flex justify-content-between">
        <h5 class="d-flex align-items-center fw-bold mb-0">Company Details</h5>

        <Link :href="urlEdit" class="btn btn-primary">Edit Profile</Link>
    </div>

    <VDevider />
    <div class="row mt-4">
        <div class="col-lg-8 mb-3">
            <VInputWithLabel
                elId="number"
                label="Company ID"
                type="text"
                :value="company.number"
                :widthLabel="labelWidth"
                :widthInput="12 - labelWidth"
                :additionalAttr="{ disabled: true }"
            />
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8 mb-3">
            <VInputWithLabel
                elId="name"
                label="Company Name"
                type="text"
                :value="company.name"
                :widthLabel="labelWidth"
                :widthInput="12 - labelWidth"
                :additionalAttr="{ disabled: true }"
            />
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-lg-8 mb-3">
            <VInputWithLabel
                elId="registration_no"
                label="SSM Number"
                type="text"
                :value="company.registration_no"
                :widthLabel="labelWidth"
                :widthInput="12 - labelWidth"
                :additionalAttr="{ disabled: true }"
            />
        </div>
    </div>

    <VShowBoatDetails :boats="company.boats" :value="company.boat" />

    <div class="d-flex justify-content-between">
        <VButton
            @onClick="onClickBack"
            type="button"
            btnStyle="btn-primary px-4"
        >
            Go Back
        </VButton>

        <VButton
            @onClick="onClickNext"
            type="button"
            btnStyle="btn-primary px-4"
        >
            Next
        </VButton>
    </div>
</template>
