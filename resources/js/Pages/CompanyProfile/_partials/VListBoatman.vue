<script setup>
import VButtonIconDelete from "@/Shared/Buttons/VButtonIconDelete.vue";

const props = defineProps({
    list: Array,
    isReadOnly: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["onDelete"]);

const clickDelete = (index) => {
    emit("onDelete", index);
};
</script>

<template>
    <div class="bg-light p-2 mb-3">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th
                            v-if="!isReadOnly"
                            class="form-table-action-column"
                            scope="col"
                        ></th>
                        <th
                            scope="col"
                            class="fw-bold text-center"
                            style="width: 60px"
                        >
                            No.
                        </th>
                        <th scope="col" class="fw-bold" style="width: 40%">
                            Name
                        </th>
                        <th scope="col" class="fw-bold">
                            IC / Passport Number
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="!props.list || props.list.length === 0">
                        <td
                            :colspan="isReadOnly ? 3 : 4"
                            class="text-center text-muted"
                        >
                            No data available
                        </td>
                    </tr>
                    <tr v-for="(item, index) in props.list" :key="index">
                        <td v-if="!isReadOnly" class="text-nowrap">
                            <VButtonIconDelete
                                classStyle="text-danger"
                                @onClick="clickDelete(index)"
                            />
                        </td>
                        <td class="text-center align-middle">
                            {{ index + 1 }}
                        </td>
                        <td>{{ item.name }}</td>
                        <td>{{ item.ic_no || item.ic }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
