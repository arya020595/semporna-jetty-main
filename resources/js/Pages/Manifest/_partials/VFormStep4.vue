<script setup>
import VDevider from "@/Shared/VDevider.vue";
import { useForm } from "@inertiajs/vue3";
import VButtonSubmit from "@/Shared/Buttons/VButtonSubmit";
import VButton from "../../../Shared/Buttons/VButton.vue";
import VFormStep4upload from "./VFormStep4upload.vue";
import { computed, ref, watch } from "vue";
import VListPassenger from "./VListPassenger.vue";
import VFormModalPassenger from "./VFormModalPassenger.vue";
import VCardBoatCapacity from "./VCardBoatCapacity.vue";

const SEMPORNA_JETTY_CODE = "DPTR_00001";
const SEAFEST_JETTY_CODE = "DPTR_00002";

const props = defineProps({
    additional: Object,
});

const { arrNationality, arrActivity, urlTemplate, arrBoat, arrDeparture } =
    props.additional;

const isShowUpload = ref(false);
const isShowForm = ref(false);
const selectedItem = ref({});
const activePassengerIndex = ref(null);
const uploadType = ref(0);

const form = useForm({
    passengers: props.additional.manifest?.passengers ?? [],
});

const emits = defineEmits(["onNext", "onPrev"]);

const selBoat = computed(() => {
    return arrBoat.find(
        (item) => item.id == props.additional.manifest?.boat_id
    );
});

const selectedDepartureCode = computed(() => {
    const departureId = props.additional.manifest?.departure_id;
    const departure = arrDeparture.find((item) => item.id == departureId);
    return departure?.code;
});

const totalPassenger = computed(() => {
    const passenger = form.passengers.length;
    const staff = (props.additional.manifest?.staff ?? []).length;

    const boatman = props.additional.manifest.boatman_name ? 1 : 0;
    const asst_boatman = props.additional.manifest.assistant_name ? 1 : 0;
    const divemaster = props.additional.manifest.divemaster.filter(
        (item) => item.name?.trim() != ""
    ).length;
    const guide = props.additional.manifest.guide.filter(
        (item) => item.name?.trim() != ""
    ).length;
    const instructor = props.additional.manifest.instructor.filter(
        (item) => item.name?.trim() != ""
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
    emits("onPrev");
};

const handleClickNext = () => {
    const formdata = form.data();

    formdata.passengers = formdata.passengers.map((item) => ({
        ...item,
    }));

    emits("onNext", formdata);
};

// Check if specific guest can be deleted based on their linked manifest fee status
const canDeleteGuestByIndex = (index) => {
    const guest = form.passengers[index];
    if (!guest) return true;

    // Seafest Jetty uses manual payment, so always allow guest deletion
    if (selectedDepartureCode.value == SEAFEST_JETTY_CODE) {
        return true;
    }

    // If guest doesn't have manifest_fee_id yet (new guest not saved), allow delete
    if (!guest.manifest_fee_id) {
        return true;
    }

    // Check if linked manifest fee is paid
    // manifest_fee_status: 1 = PAID, 0 = PENDING, -1 = FAILED
    return guest.manifest_fee_status !== 1; // Cannot delete if fee is PAID
};

const addPassenger = () => {
    selectedItem.value = {};
    activePassengerIndex.value = null;
    isShowForm.value = true;
};

const deletePassenger = (index) => {
    const formData = form.data();
    form.passengers = [];

    form.passengers = formData.passengers.filter((item, i) => {
        return i != index;
    });
};

const addPassengerFromFile = (data) => {
    let list = [];
    for (const item of data) {
        if (!item.guest_name.trim()) {
            continue;
        }

        list.push({
            name: item.guest_name,
            ic_no: item.ic_no,
            nationality_id: item.nationality,
            nationality_name: arrNationality.find(
                (n) => n.id == item.nationality
            )?.title,
            year_of_birth: item.year_of_birth,
            age: item.age,
            gender: item.gender,
            next_of_kin: item.next_of_kin,
            emergency_contact: item.emergency_contact,
            activity_ids: item.activity_ids,
            activity_names: item.activity_names,
            is_stay_resort: item.is_stay_resort,
            resort_name: item.resort_name,
        });
    }

    const formData = form.data();

    form.passengers = [...formData.passengers, ...list];

    isShowUpload.value = false;
};

watch(activePassengerIndex, (newValue) => {
    if (newValue === null) {
        return;
    }

    selectedItem.value = form.passengers[newValue];
    isShowForm.value = true;
});

const cancelForm = () => {
    selectedItem.value = {};
    isShowForm.value = false;
    activePassengerIndex.value = null;
};

const savePassenger = (data) => {
    if (activePassengerIndex.value === null) {
        form.passengers.push(data);
    } else {
        form.passengers = form.passengers.map((item, index) => {
            if (index == activePassengerIndex.value) {
                return data;
            } else {
                return item;
            }
        });
    }
    isShowForm.value = false;
    activePassengerIndex.value = null;
};

const clickUploadFile = (type) => {
    uploadType.value = type;
    isShowUpload.value = true;
};

watch(
    () => selectedDepartureCode.value,
    (newCode) => {
        if (
            newCode == SEMPORNA_JETTY_CODE ||
            newCode == SEAFEST_JETTY_CODE
        ) {
            form.passengers = form.passengers.map((passenger) => ({
                ...passenger,
                ticket_code: "",
            }));
        }
    }
);
</script>

<template>
    <VFormStep4upload
        v-if="isShowUpload"
        :urlTemplate="urlTemplate"
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
                Boat Passenger Information
            </h5>
            <div class="text-end">
                <VButton
                    btnStyle="btn-primary me-1"
                    @onClick="clickUploadFile(0)"
                >
                    <span class="material-icons"> upload </span> Upload File
                </VButton>
                <VButton btnStyle="btn-primary" @onClick="addPassenger(0)">
                    <span class="material-icons"> person_add_alt_1 </span> Add
                    Guest
                </VButton>
            </div>
        </div>
        <VDevider class="mb-4" />

        <VListPassenger
            :list="form.passengers"
            :type="0"
            v-model:value="activePassengerIndex"
            :departureId="additional.manifest?.departure_id"
            :departureCode="selectedDepartureCode"
            :passengers="form.passengers"
            :canDeleteGuestByIndex="canDeleteGuestByIndex"
            @onDelete="deletePassenger"
        />

        <VDevider class="mb-4" />

        <VFormModalPassenger
            v-if="isShowForm"
            title="Form Passenger"
            :value="selectedItem"
            :arrActivity="arrActivity"
            :arrNationality="arrNationality"
            :departureId="additional.manifest?.departure_id"
            :departureCode="selectedDepartureCode"
            @onCancel="cancelForm"
            @onSave="savePassenger"
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
                    @onCLickSubmit="handleClickNext(1)"
                    attrClass="px-4 mb-1"
                >
                    Next
                </VButtonSubmit>
            </div>
        </div>
    </template>
</template>
