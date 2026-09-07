<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import { read, utils } from "xlsx";

import VButtonSubmit from "@/Shared/Buttons/VButtonSubmit";

import VDevider from "@/Shared/VDevider.vue";
import { computed, ref, watch } from "vue";
import Swal from "sweetalert2";
import { findBestMatch } from "../../../Helpers/string";
import { useDropZone } from "@vueuse/core";

let props = defineProps({
    arrActivity: Array,
    arrNationality: Array,
    urlTemplate: String,
    type: Number,
});

const dropZoneRef = ref();

const form = useForm({
    file: null,
    file_data: [],
});

const emits = defineEmits(["onPrev", "onNext"]);

watch(
    () => form.file,
    (newValue) => {
        if (!form.file) return false;
        if (!hasExtension(form.file.name, ["xls", "xlsx"])) {
            form.file = null;
            Swal.fire({
                icon: "warning",
                title: "File not allowed!",
                text: "Choose excel file (xls or xlsx)!",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "Okay!",
            });
            return false;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            const workbook = read(e.target.result);

            const data = utils.sheet_to_row_object_array(
                workbook.Sheets[workbook.SheetNames[0]]
            );

            form.file_data = data.map((item) => {
                // All required fields
                const baseData = {
                    guest_name: item["guest_name"],
                    ic_no: item["ic_no/pass_no"],
                    nationality: item["nationality"],
                    year_of_birth: item["year_of_birth"] || item["Year of Birth"] || item["year of birth"] || "",
                    age: item["age"],
                    gender: item["gender (M/F)"],
                };

                // If passenger
                if (props.type === 0) {
                    const activityString =
                        item["activity (Activity 1/Activity 2)"] || "";
                    const activityArray = activityString
                        // Split by comma, semi-colon, or slashes
                        .split(/[;,/]/)
                        .map((s) => s.trim())
                        // Remove empty strings
                        .filter((s) => s);

                    return {
                        ...baseData,
                        next_of_kin:
                            item[
                                "next_of_kin (Name/Relation/Phone Number/Address)"
                            ],
                        emergency_contact:
                            item["emergency_contact (+Country Code + Phone No)"],
                        activity: activityArray,
                        stay_at_resort: item["Stay at resort? (Y/N)"],
                        resort_name: item["resort_name"],
                    };
                }
                return baseData;
            });
        };
        reader.readAsArrayBuffer(form.file);
    }
);

const header = computed(() => {
    if (!form.file_data.length) return [];

    let arrTemp = [];

    for (const property in form.file_data[0]) {
        arrTemp.push(property);
    }

    return arrTemp;
});

function hasExtension(fileName, exts) {
    // const fileName = document.getElementById(inputID).value;
    return new RegExp("(" + exts.join("|").replace(/\./g, "\\.") + ")$").test(
        fileName
    );
}

function formatActivity(id) {
    const selected = props.arrActivity.find((item) => item.id == id);
    return selected?.title;
}

const handleClickPrev = () => {
    emits("onPrev");
};

const handleClickNext = async () => {
    const result = await Swal.fire({
        icon: "warning",
        title: "Are you sure?",
        text: `Add ${props.type === 0 ? "Passenger" : "Staff"} Data!`,
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes!",
    });

    if (!result.isConfirmed) {
        return false;
    }

    const formData = form.data();

    const data = formData.file_data.map((item) => {
        let bestMatch = findItem(item.nationality, props.arrNationality);
        item.nationality = bestMatch?.id;

        // Passenger
        if (props.type === 0) {
            item.activity_ids = [];
            if (Array.isArray(item.activity)) {
                item.activity_ids = item.activity
                    .map((actName) => {
                        const match = findItem(actName, props.arrActivity);
                        return match?.id;
                    })
                    .filter((id) => id);
            }

            item.activity_names = item.activity_ids
                .map((id) => props.arrActivity.find((a) => a.id == id)?.title)
                .filter(Boolean)
                .join(", ");

            item.is_stay_resort = item.resort_name ? 1 : 0;
        }

        return item;
    });
    emits("onNext", data);
};

const findItem = (input, list, key = "title") => {
    let bestMatch = list.find(
        (item) => input.toLowerCase() == item[key]?.toLowerCase()
    );
    if (!bestMatch) {
        const result = findBestMatch(input, list, key);
        bestMatch = result.match;
    }
    return bestMatch;
};

function onDrop(file) {
    console.log(file);
    form.file = file[0];
}

const { isOverDropZone } = useDropZone(dropZoneRef, {
    onDrop,
    // whether to prevent default behavior for unhandled events
    preventDefaultForUnhandled: false,
});
</script>

<template>
    <div class="d-flex justify-content-between">
        <h5 v-if="type == 0" class="d-flex align-items-center mb-0">
            Upload File (Boat Passenger Information)
        </h5>
        <h5 v-else class="d-flex align-items-center mb-0">
            Upload File (Boat Staff Information)
        </h5>
    </div>

    <VDevider class="mb-4" />

    <label
        ref="dropZoneRef"
        for="upload-file"
        class="fw-bold d-flex flex-column align-items-center justify-content-center upload-box text-secondary py-5"
    >
        <img class="upload-icons" src="/assets/images/upload-icon.png" />

        Drag & Drop a file to upload
        <span class="fw-normal font-small text-secondary">
            (Support .xls, .xlsx)
        </span>
        <a :href="urlTemplate" class="btn btn-lg btn-success mt-3">
            <img
                class="inline-icons me-2"
                src="/assets/images/excel-icon.png"
            />
            Download Template
        </a>
    </label>
    <input
        type="file"
        id="upload-file"
        class="d-none"
        @input="form.file = $event.target.files[0]"
    />

    <div class="mt-5" v-if="form.file_data.length > 0">
        <div class="table-responsive">
            <table class="table table-bordered">
                <caption class="d-none">
                    Table Passengers / Staff
                </caption>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>IC/Passport No.</th>
                        <th>Nationality</th>
                        <th>Year of Birth</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th v-if="type == 0">Next Of Kin</th>
                        <th v-if="type == 0">Emergency Call</th>
                        <th v-if="type == 0">Activity</th>
                        <th v-if="type == 0">Stay at Resort</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in form.file_data" :key="index">
                        <td>{{ item["guest_name"] ?? "" }}</td>
                        <td>{{ item["ic_no"] ?? "" }}</td>
                        <td>
                            {{ item["nationality"] ?? "" }}
                        </td>
                        <td>{{ item["year_of_birth"] ?? "" }}</td>
                        <td>{{ item["age"] ?? "" }}</td>
                        <td>{{ item["gender"] ?? "" }}</td>
                        <td v-if="type == 0">
                            {{ item["next_of_kin"] ?? "" }}
                        </td>
                        <td v-if="type == 0">
                            {{ item["emergency_contact"] ?? "" }}
                        </td>
                        <td v-if="type == 0">{{ item["activity"] ?? "" }}</td>
                        <td v-if="type == 0">
                            {{
                                item["resort_name"]
                                    ? item["resort_name"] + " (Yes)"
                                    : "(No)"
                            }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <VDevider class="my-4" />

    <div class="d-flex justify-content-between">
        <VButtonSubmit
            type="button"
            :isProcessing="form.processing"
            @onCLickSubmit="handleClickPrev"
            attrClass="px-4"
        >
            Go Back
        </VButtonSubmit>

        <VButtonSubmit
            type="button"
            :isProcessing="form.processing"
            @onCLickSubmit="handleClickNext"
            attrClass="px-4"
        >
            Add {{ type == 1 ? "Staff" : "Passenger" }}
        </VButtonSubmit>
    </div>
</template>

<style scoped>
.upload-box {
    border: 1px dashed #ccc;
    cursor: pointer;
}

.upload-box .upload-icons {
    width: 150px;
}

.upload-box .inline-icons {
    height: 1.5rem;
}
</style>
