<script setup>
import { Head, Link } from "@inertiajs/vue3";
import VDevider from "@/Shared/VDevider.vue";
import VAlert from "@/Shared/VAlert.vue";
import VHeaderBreadcrumb from "@/Shared/VHeaderBreadcrumb.vue";
import VInputReadonlyWithLabel from "../../Shared/Form/VInputReadonlyWithLabel.vue";
import VListBoat from "./_partials/VListBoat.vue";
import VListBoatman from "./_partials/VListBoatman.vue";
import VFormModalBoatDetails from "./_partials/VFormModalBoatDetails.vue";
import { ref, watch } from "vue";

const props = defineProps({
    title: String,
    additional: Array,
});

const { company, urlEdit } = props.additional;

const breadcrumbs = [
    {
        url: "#",
        label: "Profile",
    },
];

const labelWidth = 4;

const activeBoatID = ref(null);
const isShowModal = ref(false);
const selectedBoat = ref({});

const showBoatDetails = (id) => {
    const boat = company?.boats?.find((item) => item.id == id);
    selectedBoat.value = boat;
    isShowModal.value = true;
};

const closeModal = () => {
    selectedBoat.value = {};
    isShowModal.value = false;
    activeBoatID.value = null;
};
</script>

<template>
    <Head>
        <title>{{ title }}</title>
    </Head>

    <div class="p-3">
        <VHeaderBreadcrumb :breadcrumbs="breadcrumbs" />

        <VAlert />

        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <h5 class="d-flex align-items-center mb-0 text-primary">
                        Company Details
                    </h5>

                    <div>
                        <Link :href="urlEdit" class="btn btn-primary"
                            >Edit Profile</Link
                        >
                    </div>
                </div>

                <VDevider />
                <div class="row mt-4">
                    <div class="col-lg-6 mb-3 mb-lg-2">
                        <VInputReadonlyWithLabel
                            elId="number"
                            label="Company ID"
                            type="text"
                            :value="company.number"
                            :widthLabel="labelWidth"
                            :widthInput="12 - labelWidth"
                        />
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 mb-3">
                        <VInputReadonlyWithLabel
                            elId="name"
                            label="Company Name"
                            type="text"
                            :value="company.name"
                            :widthLabel="labelWidth"
                            :widthInput="12 - labelWidth"
                        />
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-6 mb-3">
                        <VInputReadonlyWithLabel
                            elId="registration_no"
                            label="SSM Number"
                            type="text"
                            :value="company.registration_no"
                            :widthLabel="labelWidth"
                            :widthInput="12 - labelWidth"
                        />
                    </div>
                </div>

                <VDevider class="my-3" />

                <h5 class="d-flex align-items-center mb-3 text-primary">
                    Boat List
                </h5>

                <VListBoat
                    :list="company.boats"
                    :isReadOnly="true"
                    @onShow="showBoatDetails"
                />

                <VDevider class="my-3" />

                <h5 class="d-flex align-items-center mb-3 text-primary">
                    Instructor List
                </h5>
                <VListBoatman :list="company.instructor" :isReadOnly="true" />

                <VDevider class="my-3" />

                <h5 class="d-flex align-items-center mb-3 text-primary">
                    Divemaster List
                </h5>
                <VListBoatman :list="company.divemaster" :isReadOnly="true" />

                <VDevider class="my-3" />

                <h5 class="d-flex align-items-center mb-3 text-primary">
                    Guide List
                </h5>
                <VListBoatman :list="company.guide" :isReadOnly="true" />
            </div>
        </div>
    </div>

    <VFormModalBoatDetails
        v-if="isShowModal"
        :value="selectedBoat"
        :isReadOnly="true"
        @onCancel="closeModal"
    />
</template>
