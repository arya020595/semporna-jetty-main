<script setup>
import VDevider from "@/Shared/VDevider.vue";
import { useForm } from "@inertiajs/vue3";
import VButtonSubmit from "@/Shared/Buttons/VButtonSubmit";
import VFormPassenger from "../../../Shared/Manifest/VFormPassenger.vue";
import VButton from "../../../Shared/Buttons/VButton.vue";
import VFormStep4upload from "./VFormStep4upload.vue";
import { computed, ref, watch } from "vue";
import VListPassenger from "./VListPassenger.vue";
import VFormModalPassenger from "./VFormModalPassenger.vue";
import VCardBoatCapacity from "./VCardBoatCapacity.vue";
import Swal from "sweetalert2";
import VApprovementHistory from "../../Authorities/MyDashboard/_partials/VApprovementHistory.vue";

const SEMPORNA_JETTY_ID = 7;

const props = defineProps({
    additional: Object,
});

const { arrNationality, arrActivity, urlTemplate, urlTemplateStaff, arrBoat } =
    props.additional;

const isShowUpload = ref(false);
const isShowForm = ref(false);
const isShowFormStaff = ref(false);
const selectedItem = ref({});
const activePassengerIndex = ref(null);
const activeStaffIndex = ref(null);
const uploadType = ref(1);

const form = useForm({
    staff: props.additional.manifest?.staff ?? [],
});

const emits = defineEmits(["onNext", "onPrev"]);

const selBoat = computed(() => {
    return arrBoat.find(
        (item) => item.id == props.additional.manifest?.boat_id,
    );
});

const totalPassenger = computed(() => {
    const passenger = (props.additional.manifest?.passengers ?? []).length;
    const staff = form.staff.length;

    const boatman = props.additional.manifest.boatman_name ? 1 : 0;
    const asst_boatman = props.additional.manifest.assistant_name ? 1 : 0;
    const divemaster = props.additional.manifest.divemaster.filter(
        (item) => item.name?.trim() != "",
    ).length;
    const guide = props.additional.manifest.guide.filter(
        (item) => item.name?.trim() != "",
    ).length;
    const instructor = props.additional.manifest.instructor.filter(
        (item) => item.name?.trim() != "",
    ).length;

    return (
        passenger +
        staff +
        boatman +
        asst_boatman +
        divemaster +
        guide +
        instructor
    );
});

const handleClickPrev = () => {
    const formdata = form.data();

    formdata.staff = formdata.staff.map((item) => ({
        ...item,
    }));

    emits("onPrev", formdata);
};

const handleClickNext = (is_final) => {
    const formdata = form.data();

    const isRentBoat =
        props.additional.manifest?.is_rent ||
        props.additional.manifest?.type === 2;

    // Check capacity if boat is not rental
    if (!isRentBoat) {
        if (selBoat.value.capacity < totalPassenger.value) {
            Swal.fire({
                title: "Failed",
                icon: "error",
                text: "Total passenger more than boat capacity",
            });

            return;
        }
    }

    formdata.staff = formdata.staff.map((item) => ({
        ...item,
    }));

    formdata.is_final = is_final;

    emits("onNext", formdata);
};

const hasPaidFees = computed(() => {
    const manifest = props.additional.manifest;
    if (!manifest) return false;

    // Check if any manifest_fee has PAID status
    const manifestFee = manifest.manifest_fee;
    const manifestFeeAdditional = manifest.manifest_fee_additional || [];

    // Check primary fee
    if (manifestFee && manifestFee.status === 1) {
        return true;
    }

    // Check any additional fees
    if (manifestFeeAdditional.some((fee) => fee.status === 1)) {
        return true;
    }

    return false;
});

const addStaff = () => {
    selectedItem.value = {};
    activeStaffIndex.value = null;
    isShowFormStaff.value = true;
};

const deleteStaff = (index) => {
    const formData = form.data();
    form.staff = [];

    form.staff = formData.staff.filter((item, i) => {
        return i != index;
    });
};

const addPassengerFromFile = (data) => {
    let list = [];
    for (const item of data) {
        if (!item.guest_name.trim()) {
            continue;
        }

        // All required fields
        const baseData = {
            name: item.guest_name,
            ic_no: item.ic_no,
            nationality_id: item.nationality,
            nationality_name: arrNationality.find(
                (n) => n.id == item.nationality,
            )?.title,
            year_of_birth: item.year_of_birth,
            age: item.age,
            gender: item.gender,
        };

        // Passenger
        if (uploadType.value === 0) {
            list.push({
                ...baseData,
                next_of_kin: item.next_of_kin,
                emergency_contact: item.emergency_contact,
                activity_ids: item.activity_ids || [],
                activity_names: item.activity_names || "",
                is_stay_resort: item.is_stay_resort,
                resort_name: item.resort_name,
            });
        } else {
            // Staff
            list.push(baseData);
        }
    }

    const formData = form.data();
    form.staff = [...formData.staff, ...list];
    isShowUpload.value = false;
};

watch(activeStaffIndex, (newValue) => {
    if (newValue === null) {
        return;
    }

    selectedItem.value = form.staff[newValue];
    isShowFormStaff.value = true;
});

const cancelForm = () => {
    selectedItem.value = {};
    isShowForm.value = false;
    isShowFormStaff.value = false;
    activePassengerIndex.value = null;
    activeStaffIndex.value = null;
};

const saveStaff = (data) => {
    if (activeStaffIndex.value === null) {
        form.staff.push(data);
    } else {
        form.staff = form.staff.map((item, index) => {
            if (index == activeStaffIndex.value) {
                return data;
            } else {
                return item;
            }
        });
    }

    isShowFormStaff.value = false;
    activeStaffIndex.value = null;
};

const clickUploadFile = (type) => {
    uploadType.value = type;
    isShowUpload.value = true;
};

watch(
    () => props.additional.manifest?.departure_id,
    (newDepartureId) => {
        if (newDepartureId == SEMPORNA_JETTY_ID) {
            form.staff = form.staff.map((staff) => ({
                ...staff,
                ticket_code: "",
            }));
        }
    },
);
</script>

<template>
    <VFormStep4upload
        v-if="isShowUpload"
        :urlTemplate="uploadType === 1 ? urlTemplateStaff : urlTemplate"
        :arrNationality="arrNationality"
        :arrActivity="arrActivity"
        :type="uploadType"
        @onPrev="isShowUpload = false"
        @onNext="addPassengerFromFile"
    />
    <template v-else>
        <VCardBoatCapacity
            :capacity="selBoat?.capacity"
            :totalPassenger="totalPassenger"
        />

        <div class="d-flex flex-column flex-lg-row justify-content-lg-between">
            <h5 class="d-flex align-items-center mb-3 mb-lg-0">
                Boat Staff Information
            </h5>
            <div class="text-end">
                <VButton
                    btnStyle="btn-primary me-1"
                    @onClick="clickUploadFile(1)"
                >
                    <span class="material-icons"> upload </span> Upload File
                </VButton>
                <VButton btnStyle="btn-primary" @onClick="addStaff(1)">
                    <span class="material-icons"> person_add_alt_1 </span> Add
                    Staff
                </VButton>
            </div>
        </div>

        <VDevider class="mb-4" />

        <VListPassenger
            :list="form.staff"
            :type="1"
            v-model:value="activeStaffIndex"
            :departureId="additional.manifest?.departure_id"
            @onDelete="deleteStaff"
            :isStaffForm="true"
        />

        <VFormModalPassenger
            v-if="isShowFormStaff"
            title="Form Staff"
            :value="selectedItem"
            :arrActivity="arrActivity"
            :arrNationality="arrNationality"
            :isStaffForm="true"
            :departureId="additional.manifest?.departure_id"
            @onCancel="cancelForm"
            @onSave="saveStaff"
        />

        <div
            class="d-flex flex-column-reverse flex-sm-row justify-content-sm-between"
        >
            <div>
                <VButtonSubmit
                    type="button"
                    :isProcessing="form.processing"
                    @onCLickSubmit="handleClickPrev"
                    attrClass="px-4"
                >
                    Go Back
                </VButtonSubmit>
            </div>

            <div class="mb-3 mt-3 mb-lg-0 d-flex flex-column flex-sm-row">
                <VButtonSubmit
                    type="button"
                    :isProcessing="form.processing"
                    @onCLickSubmit="handleClickNext(0)"
                    attrClass="px-4 me-1 mb-1"
                    btnColor="btn-warning text-white"
                    :disabled="hasPaidFees"
                >
                    Save as Draft
                </VButtonSubmit>
                <VButtonSubmit
                    type="button"
                    :isProcessing="form.processing"
                    @onCLickSubmit="handleClickNext(1)"
                    attrClass="px-4 mb-1"
                >
                    <span class="material-icons"> description </span>
                    Generate Manifest
                </VButtonSubmit>
            </div>
        </div>

        <div class="bg-light border p-3 mt-3" v-if="additional.approvements">
            <div class="d-flex justify-content-between">
                <h5 class="d-flex align-items-center mb-1">
                    Approvement History
                </h5>
            </div>
            <VDevider class="mb-3" />
            <VApprovementHistory :approvements="additional.approvements" />
        </div>
    </template>
</template>
