<script setup>
import { Head, useForm, Link, usePage } from "@inertiajs/vue3";
import VHeaderBreadcrumb from "@/Shared/VHeaderBreadcrumb.vue";

import VAlert from "@/Shared/VAlert.vue";
import _ from "lodash";
import VDevider from "@/Shared/VDevider.vue";
import VShowOverall from "@/Pages/Manifest/_partials/VShowOverall.vue";
import { useManifest } from "@/Composable/useManifest";
import VPassengerTable from "@/Pages/UserActivity/_partials/VPassengerTable.vue";
import VApprovement from "@/Pages/UserActivity/_partials/VApprovement.vue";
import { computed } from "vue";
import VStaffTable from "./_partials/VStaffTable.vue";

const appBaseUrl = usePage().props.appBaseUrl;

const props = defineProps({
    title: String,
    additional: Array,
});

const { qrcode, manifest, urlBack, urlDownload, bypassApproval, isSeafest } =
    props.additional;

const breadcrumbs = [
    {
        url: "#",
        label: "Approved Manifest Form",
    },
];

const {
    primaryPassengers,
    additionalPassengers,
    isStatusNotInitiated,
    isStatusPending,
    isStatusApproved,
    isPaymentStatusPaid,
    isPaymentStatusPending,
} = useManifest(manifest);

const ROLE_PDRM = 3;
const ROLE_JABATAN_LAUT = 4;
const ROLE_SABAH_PARKS = 5;
const ROLE_JABATAN_PELABUHAN = 6;

const approvementRequired = computed(() => {
    const roles = [
        ROLE_JABATAN_PELABUHAN,
        ROLE_JABATAN_LAUT,
        ROLE_PDRM,
        ROLE_SABAH_PARKS,
    ];
    const currentVersion = manifest.version;
    const versionApprovements = manifest.approvement
        .filter((a) => a.version === currentVersion)
        .sort((a, b) => b.id - a.id); // latest first
    return roles.map((roleId) => {
        const found = versionApprovements.find((a) => a.role_id === roleId);
        return {
            role_id: roleId,
            status: found ? found.status : 0,
            status_text: found ? found.status_text : "Pending",
            comments: found ? found.comments : null,
        };
    });
});
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
                <div
                    class="d-flex flex-md-row flex-column justify-content-md-between mb-5"
                >
                    <div class="text-start">
                        <Link :href="urlBack" class="btn fw-bold">
                            <span class="material-icons"> chevron_left </span>
                            Back To Activity</Link
                        >
                    </div>
                    <div class="text-emd">
                        <a
                            :href="urlDownload"
                            target="_blank"
                            class="btn btn-primary"
                        >
                            <span class="icon-pdf me-1">
                                <img
                                    src="/assets/images/icon_pdf.png"
                                    alt="pdf button"
                                />
                            </span>
                            Download PDF</a
                        >
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 d-flex align-items-end">
                        <strong>
                            Majlis Daerah Semporna Peti Surat 134, 91308
                            Semporna Sabah
                        </strong>
                    </div>
                    <div class="col-md-4 text-center">
                        <img
                            id="logo-1"
                            :src="
                                appBaseUrl +
                                '/assets/images/Coat_of_arms_of_Sabah.jpg'
                            "
                            class="logo-semporna"
                            alt="Sabah Logo"
                        />
                        <img
                            id="logo-2"
                            :src="
                                appBaseUrl + '/assets/images/semporna-logo.png'
                            "
                            class="logo-semporna"
                            alt="Semporna Logo"
                        />
                    </div>
                    <div class="col-md-4 text-lg-end text-center">
                        <div class="d-flex mb-1">
                            <strong class="text-nowrap pe-2 col-6"
                                >Payment Status:</strong
                            >
                            <span
                                class="status-indicator text-center col-6"
                                :class="{
                                    'bg-warning text-white':
                                        isPaymentStatusPending,
                                    'bg-success text-white':
                                        isPaymentStatusPaid,
                                }"
                                >{{ manifest.payment_status_text }}</span
                            >
                        </div>
                        <div class="d-flex mb-2" v-if="!bypassApproval && !isSeafest">
                            <strong class="text-nowrap pe-2 col-6"
                                >Approval Status:</strong
                            >
                            <span
                                class="status-indicator text-center col-6"
                                :class="{
                                    'bg-secondary text-white':
                                        isStatusNotInitiated,
                                    'bg-warning text-white': isStatusPending,
                                    'bg-success text-white': isStatusApproved,
                                }"
                                >{{ manifest.status_text }}</span
                            >
                        </div>
                        <div v-if="qrcode" class="text-end">
                            <img
                                :src="qrcode"
                                alt="qrcode for approval"
                                width="150px"
                                class="mb-2"
                            />
                            <div
                                class="text-muted"
                                style="font-size: 0.75em; max-width: 400px"
                            >
                                Please proceed to the authorities with the QR
                                code for scanning, and kindly bring along your
                                hardcopy license as well.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <strong
                        >Passenger Manifest Form (For All Resort
                        Company)</strong
                    >
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div>
                            <strong>Manifest Form No. : </strong>
                            <span>{{ manifest.form_number }}</span>
                        </div>
                        <div>
                            <strong
                                >From {{ manifest.departure_name }} To :
                            </strong>
                            <span>{{
                                manifest.manifest_destination
                                    .map((item) => item.ref_destination_name)
                                    .join(", ")
                            }}</span>
                        </div>
                        <div>
                            <strong>Company : </strong>
                            <span>{{ manifest.company_name }}</span>
                        </div>
                    </div>
                    <div
                        class="col-md-6 text-end d-flex flex-column justify-content-end"
                    >
                        <div>
                            <strong>Date : </strong>
                            <span>{{ manifest.departure_date }}</span>
                        </div>
                        <div>
                            <strong>Departure Time : </strong>
                            <span>{{ manifest.departure_time }}</span>
                        </div>
                    </div>
                </div>

                <!-- Table Passenger -->
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <VPassengerTable :passengers="primaryPassengers" />
                    </div>
                </div>
                <!-- End of Table Passenger -->

                <div class="row">
                    <div class="col-md-6">
                        <div>
                            <strong>Boatman : </strong>
                            <span>{{ manifest.boatman_name }}</span>
                        </div>
                        <div>
                            <strong>Seaman Number : </strong>
                            <span>{{ manifest.seaman_no }}</span>
                        </div>
                        <div>
                            <strong>IC Number : </strong>
                            <span>{{ manifest.boatman_ic_no }}</span>
                        </div>
                        <br />
                        <div>
                            <strong>Assistant Boatman : </strong>
                            <span>{{ manifest.assistant_name }}</span>
                        </div>
                        <div>
                            <strong>IC Number : </strong>
                            <span>{{ manifest.assistant_ic_no }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <div>
                                    <strong>Boat No : </strong>
                                    <span>{{ manifest.boat_number }}</span>
                                </div>
                            </div>
                            <div class="col-md-6"></div>
                        </div>
                    </div>
                </div>
                <VDevider class="my-3" />

                <template v-if="manifest.staff.length">
                    <!-- Table Passenger -->
                    <h5 class="text-bold">Staff</h5>
                    <div class="card shadow-sm mb-3">
                        <div class="card-body">
                            <VStaffTable :passengers="manifest.staff" />
                        </div>
                    </div>
                    <!-- End of Table Passenger -->

                    <VDevider class="my-3" />
                </template>

                <template v-if="additionalPassengers.length">
                    <!-- Table Passenger -->
                    <h5 class="text-bold">
                        Extra Passenger with Permission of Officer of Jabatan
                        Pelabuhan & Dermaga
                    </h5>
                    <div class="card shadow-sm mb-3">
                        <div class="card-body">
                            <VPassengerTable
                                :passengers="additionalPassengers"
                            />
                        </div>
                    </div>
                    <!-- End of Table Passenger -->

                    <VDevider class="my-3" />
                </template>

                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <h5 class="text-bold">Approved By:</h5>
                        <div class="row">
                            <div
                                v-for="approvement in approvementRequired"
                                class="col-12 col-md-6"
                                :class="
                                    'col-lg-' + 12 / approvementRequired.length
                                "
                            >
                                <VApprovement
                                    :roleId="approvement.role_id"
                                    :status="approvement.status"
                                    :bypassApproval="isSeafest || bypassApproval"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <VShowOverall
                        v-if="manifest.manifest_fee"
                        :manifest="manifest"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="css">
.status-indicator {
    min-width: 140px;
}

.logo-semporna {
    max-height: 150px;
}
</style>
