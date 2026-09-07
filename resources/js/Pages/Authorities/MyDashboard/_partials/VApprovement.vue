<script setup>
import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    roleId: Number,
    status: Number,
    bypassApproval: {
        type: Boolean,
        default: true,
    },
});
const appBaseUrl = usePage().props.appBaseUrl;

const ARR_AUTHORITIES = {
    role_3: {
        name: "Polis Diraja Malaysia",
        logo: appBaseUrl + "/assets/images/pdrm-logo.png",
        stamp: appBaseUrl + "/assets/images/pdrm-stamp.png",
    },
    role_4: {
        name: "Jabatan Laut Malaysia",
        logo: appBaseUrl + "/assets/images/jabatan-laut-logo.png",
        stamp: appBaseUrl + "/assets/images/jabatan-laut-stamp.png",
    },
    role_5: {
        name: "Sabah Parks",
        logo: appBaseUrl + "/assets/images/sabah-parks-logo.jpeg",
        stamp: appBaseUrl + "/assets/images/jabatan-laut-stamp.png",
    },
    role_6: {
        name: "Jabatan Pelabuhan Dan Dermaga Sabah",
        logo: appBaseUrl + "/assets/images/jabatan-pelabuhan-logo.png",
        stamp: appBaseUrl + "/assets/images/jabatan-pelabuhan-stamp.png",
    },
};

const selAuthorities = computed(() => {
    return ARR_AUTHORITIES["role_" + props.roleId];
});

const STATUS_APPROVED = 1;
const STATUS_REJECTED = -1;
</script>

<template>
    <div class="shadow p-2 position-relative">
        <!-- Show blank box when bypass approval is enabled -->
        <template v-if="props.bypassApproval">
            <div class="approval-box-area"></div>
            <div class="text-center mt-2">{{ selAuthorities?.name }}</div>
        </template>

        <!-- Show logo when bypass approval is disabled -->
        <template v-else>
            <div class="text-center">{{ selAuthorities?.name }}</div>
            <div class="row justify-content-center">
                <div
                    class="col-6 px-0 mx-0 d-flex justify-content-center align-items-center"
                >
                    <img
                        :src="selAuthorities?.logo"
                        style="max-height: 130px; max-width: 100%"
                        :alt="selAuthorities?.name"
                    />
                </div>
            </div>
        </template>

        <div
            v-if="props.status !== null"
            class="position-absolute status-icon"
            :class="{
                'text-success': props.status == STATUS_APPROVED,
                'text-danger': props.status == STATUS_REJECTED,
            }"
        >
            <i
                v-if="props.status == STATUS_APPROVED"
                class="fa fa-check-circle"
            ></i>
            <i v-else class="fa fa-ban"></i>
        </div>
    </div>
</template>

<style lang="css" scoped>
.approval-box-area {
    min-height: 150px;
    border: 1px solid #ddd;
    background-color: #f9f9f9;
}

.status-icon {
    right: 0px;
    bottom: 0px;
    font-size: 1.2rem;
}
</style>
