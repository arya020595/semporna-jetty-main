<script setup>
import Swal from "sweetalert2";
import VButtonIconEdit from "@/Shared/Buttons/VButtonIconEdit.vue";
import VButtonIconDelete from "@/Shared/Buttons/VButtonIconDelete.vue";
import VButtonIconShow from "@/Shared/Buttons/VButtonIconShow.vue";

const props = defineProps({
    list: Array,
    value: Number | null,
    isReadOnly: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["update:value", "onDelete", "onShow"]);

const clickEdit = (id) => {
    emit("update:value", id);
};

const clickShow = (id) => {
    emit("onShow", id);
};

const clickDelete = async (id) => {
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

    emit("onDelete", id);
};
</script>

<template>
    <div class="bg-light p-2">
        <div class="table-responsive">
            <table class="table">
                <tbody>
                    <tr>
                        <th class="form-table-action-column" scope="col"></th>
                        <td class="fw-bold">Boat License</td>
                        <td class="fw-bold">Expiry Date</td>
                        <td class="fw-bold">Capacity</td>
                        <td class="fw-bold">Boatman</td>
                        <td class="fw-bold">Assistant</td>
                    </tr>
                    <tr v-for="(item, index) in props.list" :key="item.id">
                        <td class="text-nowrap">
                            <!-- Read Only Mode: Show icon only -->
                            <template v-if="isReadOnly">
                                <VButtonIconShow
                                    classStyle="text-info"
                                    label="View Details"
                                    @onClick="clickShow(item.id)"
                                />
                            </template>
                            <template v-else>
                                <VButtonIconEdit
                                    classStyle="text-warning"
                                    @onClick="clickEdit(item.id)"
                                    @keyup.enter="clickEdit(item.id)"
                                    @onKeyUp=""
                                />
                                <VButtonIconDelete
                                    classStyle="text-danger"
                                    @onClick="clickDelete(item.id)"
                                    @onKeyUp=""
                                />
                            </template>
                        </td>
                        <td>{{ item.license }}</td>
                        <td>{{ item.license_expiry_date }}</td>
                        <td>{{ item.capacity }}</td>
                        <td>{{ item.boatman_count }} Boatman</td>
                        <td>{{ item.asst_count }} Asst.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
