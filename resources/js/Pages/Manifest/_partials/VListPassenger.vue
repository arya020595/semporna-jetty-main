<script setup>
import { computed } from "vue";
import Swal from "sweetalert2";
import VButtonIconEdit from "@/Shared/Buttons/VButtonIconEdit.vue";
import VButtonIconDelete from "@/Shared/Buttons/VButtonIconDelete.vue";

const props = defineProps({
    list: Array,
    type: Number,
    value: Number | null,
    isStaffForm: {
        type: Boolean,
        default: false,
    },
    departureCode: {
        type: [Number, String],
        default: null,
    },
    passengers: {
        type: Array,
        default: () => [],
    },
    canDeleteGuestByIndex: {
        type: Function,
        default: () => true,
    },
});

const SEMPORNA_JETTY_CODE = "DPTR_00001";
const SEAFEST_JETTY_CODE = "DPTR_00002";

const isSempornaJetty = computed(() => {
    return (
        props.departureCode == SEMPORNA_JETTY_CODE ||
        props.departureCode == SEAFEST_JETTY_CODE
    );
});

const emit = defineEmits(["update:value", "onDelete"]);

const clickEdit = (id) => {
    emit("update:value", id);
};

const clickDelete = async (index) => {
    const result = await Swal.fire({
        icon: "warning",
        title: "Are you sure?",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    });

    if (!result.isConfirmed) {
        return false;
    }

    emit("onDelete", index);
};
</script>

<template>
    <div class="bg-light p-2">
        <div class="table-responsive">
            <table class="table">
                <tbody>
                    <tr>
                        <th class="form-table-action-column" scope="col"></th>
                        <th class="form-table-action-column" scope="col">
                            No.
                        </th>
                        <td class="fw-bold">
                            {{ type == 0 ? "Guest Name" : "Staff Name" }}
                        </td>
                        <td class="fw-bold">IC/Passport No.</td>
                        <td class="fw-bold">Nationality</td>
                        <td class="fw-bold">Year of Birth</td>
                        <td class="fw-bold">Age</td>
                        <td class="fw-bold">Gender</td>
                        <td v-if="!isStaffForm" class="fw-bold">Next of Kin</td>
                        <td v-if="!isStaffForm" class="fw-bold">
                            Emergency Contact
                        </td>
                        <td v-if="!isStaffForm" class="fw-bold">Activity</td>
                        <td v-if="!isStaffForm" class="fw-bold">
                            Stay at Resort
                        </td>
                        <td
                            v-if="!isStaffForm && !isSempornaJetty"
                            class="fw-bold"
                        >
                            Ticket
                        </td>
                    </tr>
                    <tr v-if="list.length == 0">
                        <td
                            colspan="10"
                            class="text-center fw-bold text-secondary"
                        >
                            {{
                                type == 0
                                    ? "No passenger added!"
                                    : "No staff added!"
                            }}
                        </td>
                    </tr>
                    <tr v-for="(item, index) in list" :key="item.id">
                        <td class="text-nowrap">
                            <VButtonIconEdit
                                classStyle="text-warning"
                                @onClick="clickEdit(index)"
                                @keyup.enter="clickEdit(index)"
                                @onKeyUp=""
                            />
                            <VButtonIconDelete
                                v-if="
                                    type === 0
                                        ? canDeleteGuestByIndex(index)
                                        : true
                                "
                                classStyle="text-danger"
                                @onClick="clickDelete(index)"
                                @onKeyUp=""
                            />
                        </td>
                        <td>{{ index + 1 }}</td>
                        <td>{{ item.name }}</td>
                        <td>{{ item.ic_no }}</td>
                        <td>{{ item.nationality_name }}</td>
                        <td>{{ item.year_of_birth }}</td>
                        <td>{{ item.age }}</td>
                        <td>{{ item.gender }}</td>
                        <td v-if="!isStaffForm">{{ item.next_of_kin }}</td>
                        <td v-if="!isStaffForm">
                            {{ item.emergency_contact }}
                        </td>
                        <td v-if="!isStaffForm">
                            {{
                                item.activity_names || item.activity_name || "-"
                            }}
                        </td>
                        <td v-if="!isStaffForm">
                            {{
                                item.is_stay_resort == 1
                                    ? item.resort_name + " (Yes)"
                                    : "No"
                            }}
                        </td>
                        <td v-if="!isStaffForm && !isSempornaJetty">
                            {{ item.ticket_code }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
