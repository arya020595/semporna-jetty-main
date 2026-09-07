<script setup>
import VInputWithLabel from "@/Shared/Form/VInputWithLabel.vue";
import VDevider from "@/Shared/VDevider.vue";
import { computed } from "vue";

const props = defineProps({
    boats: Object,
    company: Object,
    manifest: Object,
    isAuthority: Boolean,
});

const initialBoats = props.boats?.[0] ?? null;

const initialBoatman = initialBoats?.boatman?.[0] ?? {};

const initialAsst = initialBoats?.asst?.[0] ?? {};

const MANIFEST_TYPE_RENTAL = 2;

const form = {
    is_rent: props.manifest?.type == MANIFEST_TYPE_RENTAL,
    departure_date: props.manifest?.departure_date ?? "",
    departure_time: props.manifest?.departure_time ?? "",
    company_id: props.manifest?.company_id ?? company.id,
    company_name: props.manifest?.company_name ?? company.name,

    boat_id: props.manifest?.boat_id ?? initialBoats?.id,
    boat_number: props.manifest?.boat_number ?? initialBoats?.number,

    boatman_id: props.manifest?.boatman_id ?? initialBoatman?.id,
    boatman_name: props.manifest?.boatman_name ?? initialBoatman?.name,
    boatman_mate_no:
        props.manifest?.boatman_mate_no ?? initialBoatman?.mate_card,
    seaman_no: props.manifest?.seaman_no ?? initialBoatman?.seaman_card_no,
    boatman_ic_no: props.manifest?.boatman_ic_no ?? initialBoatman?.ic_no,

    assistant_id: props.manifest?.assistant_id,
    assistant_name: props.manifest?.assistant_name,
    assistant_mate_no: props.manifest?.assistant_mate_no,
    assistant_ic_no: props.manifest?.assistant_ic_no,
    assistant_seaman_no: props.manifest?.assistant_seaman_no,
};

const labelWidth = 4;

const selectedBoat = computed(() => {
    if (form.is_rent) {
        return false;
    }

    return props.boats.find((b) => b.id === form.boat_id) || null;
});

const mainBoatman = computed(() => {
    if (!selectedBoat.value) return null;

    // BoatResource transforms boatmanMain to 'boatman' property
    const boatmanArray = selectedBoat.value.boatman;
    if (!boatmanArray || boatmanArray.length === 0) return null;

    // Try to find by boatman_id first
    if (form.boatman_id) {
        const found = boatmanArray.find((bm) => bm.id === form.boatman_id);
        if (found) return found;
    }

    // Fallback: return first boatman
    return boatmanArray[0];
});
</script>

<template>
    <h6 class="bg-primary text-white px-4 py-3">Departure Details</h6>

    <div class="row mt-4">
        <div class="col-md-6 mb-3">
            <VInputWithLabel
                elId="departure_date"
                label="Departure Date"
                type="date"
                :value="form.departure_date"
                :widthLabel="labelWidth"
                :widthInput="12 - labelWidth"
                :additionalAttr="{ disabled: true }"
            />
        </div>
        <div class="col-md-6 mb-3">
            <VInputWithLabel
                elId="departure_time"
                label="Departure Time"
                type="time"
                :value="form.departure_time"
                :widthLabel="labelWidth"
                :widthInput="12 - labelWidth"
                :additionalAttr="{ disabled: true }"
            />
        </div>
    </div>

    <h6 class="bg-primary text-white px-4 py-3">
        Boat & Company Information {{ form?.is_rent ? "(Rental Boat)" : "" }}
    </h6>

    <div class="row mt-4 mb-3">
        <div class="col-md-6">
            <div class="mb-3">
                <VInputWithLabel
                    elId="company_name"
                    label="Company Name"
                    type="text"
                    :value="form.company_name"
                    :widthLabel="labelWidth"
                    :widthInput="12 - labelWidth"
                    :additionalAttr="{ disabled: true }"
                />
            </div>
            <div class="mb-3">
                <VInputWithLabel
                    elId="boat"
                    label="Boat Number"
                    type="text"
                    :value="form.boat_number"
                    :widthLabel="labelWidth"
                    :widthInput="12 - labelWidth"
                    :additionalAttr="{ disabled: true }"
                />
            </div>
            <!-- Boat License Files -->
            <div
                class="mb-3"
                v-if="!form.is_rent && selectedBoat && isAuthority"
            >
                <div class="row">
                    <label class="col-sm-4 label-size fw-bold mb-sm-0 mb-2">
                        Boat License Files
                    </label>
                    <div class="col-sm-8">
                        <template
                            v-if="
                                selectedBoat.license_file_existing &&
                                selectedBoat.license_file_existing.length > 0
                            "
                        >
                            <a
                                v-for="(
                                    fileUrl, index
                                ) in selectedBoat.license_file_existing"
                                :key="index"
                                :href="fileUrl"
                                target="_blank"
                                class="btn btn-success btn-sm mx-1 mb-2"
                                :title="`View License Page ${index + 1}`"
                            >
                                <span class="material-icons">visibility</span>
                            </a>
                        </template>
                        <span v-else class="text-muted">
                            No license files uploaded
                        </span>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <VInputWithLabel
                    elId="boatman"
                    label="Boatman"
                    type="text"
                    :value="form.boatman_name"
                    :widthLabel="labelWidth"
                    :widthInput="12 - labelWidth"
                    :additionalAttr="{ disabled: true }"
                />
            </div>
            <div class="mb-3">
                <VInputWithLabel
                    elId="boatman_mate_no"
                    label="Mate No."
                    type="text"
                    :value="form.boatman_mate_no"
                    :widthLabel="labelWidth"
                    :widthInput="12 - labelWidth"
                    :additionalAttr="{ disabled: true }"
                />
            </div>
            <div class="mb-3">
                <VInputWithLabel
                    elId="seaman_no"
                    label="Seaman No."
                    type="text"
                    :value="form.seaman_no"
                    :widthLabel="labelWidth"
                    :widthInput="12 - labelWidth"
                    :additionalAttr="{ disabled: true }"
                />
            </div>
            <div class="mb-3">
                <VInputWithLabel
                    elId="boatman_ic_no"
                    label="IC No."
                    type="text"
                    :value="form.boatman_ic_no"
                    :widthLabel="labelWidth"
                    :widthInput="12 - labelWidth"
                    :additionalAttr="{ disabled: true }"
                />
            </div>
            <!-- Boatman Documents -->
            <div
                class="mb-3"
                v-if="!form.is_rent && mainBoatman && isAuthority"
            >
                <!-- Mate Card -->
                <div class="row mb-2">
                    <label class="col-sm-4 label-size fw-bold mb-sm-0 mb-1">
                        Mate Card
                    </label>
                    <div class="col-sm-8">
                        <a
                            v-if="mainBoatman.mate_card_file_existing"
                            :href="mainBoatman.mate_card_file_existing"
                            target="_blank"
                            class="btn btn-success btn-sm"
                            title="View Mate Card"
                        >
                            <span class="material-icons">visibility</span>
                        </a>
                        <span v-else class="text-muted">
                            No file uploaded
                        </span>
                    </div>
                </div>
                <!-- Seaman Card -->
                <div class="row mb-2">
                    <label class="col-sm-4 label-size fw-bold mb-sm-0 mb-1">
                        Seaman Card
                    </label>
                    <div class="col-sm-8">
                        <a
                            v-if="mainBoatman.seaman_card_file_existing"
                            :href="mainBoatman.seaman_card_file_existing"
                            target="_blank"
                            class="btn btn-success btn-sm"
                            title="View Seaman Card"
                        >
                            <span class="material-icons">visibility</span>
                        </a>
                        <span v-else class="text-muted">
                            No file uploaded
                        </span>
                    </div>
                </div>
                <!-- Boatman IC -->
                <div class="row mb-2">
                    <label class="col-sm-4 label-size fw-bold mb-sm-0 mb-1">
                        Boatman IC
                    </label>
                    <div class="col-sm-8">
                        <a
                            v-if="mainBoatman.ic_no_file_existing"
                            :href="mainBoatman.ic_no_file_existing"
                            target="_blank"
                            class="btn btn-success btn-sm"
                            title="View IC File"
                        >
                            <span class="material-icons">visibility</span>
                        </a>
                        <span v-else class="text-muted">
                            No file uploaded
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <VInputWithLabel
                    elId="assistant"
                    label="Assistant"
                    type="text"
                    :value="form.assistant_name"
                    :widthLabel="labelWidth"
                    :widthInput="12 - labelWidth"
                    :additionalAttr="{ disabled: true }"
                />
            </div>
            <div class="mb-3">
                <VInputWithLabel
                    elId="assistant_mate_no"
                    label="Mate No."
                    type="text"
                    :value="form.assistant_mate_no"
                    :widthLabel="labelWidth"
                    :widthInput="12 - labelWidth"
                    :additionalAttr="{ disabled: true }"
                />
            </div>
            <div class="mb-3">
                <VInputWithLabel
                    elId="assistant_ic_no"
                    label="IC No."
                    type="text"
                    :value="form.assistant_ic_no"
                    :widthLabel="labelWidth"
                    :widthInput="12 - labelWidth"
                    :additionalAttr="{ disabled: true }"
                />
            </div>
            <div class="mb-3">
                <VInputWithLabel
                    elId="assistant_seaman_no"
                    label="Seaman No."
                    type="text"
                    :value="form.assistant_seaman_no"
                    :widthLabel="labelWidth"
                    :widthInput="12 - labelWidth"
                    :additionalAttr="{ disabled: true }"
                />
            </div>
        </div>
    </div>
</template>
