<script setup>
import { Head, useForm, Link } from "@inertiajs/vue3";
import VHeaderBreadcrumb from "@/Shared/VHeaderBreadcrumb.vue";

import VAlert from "@/Shared/VAlert.vue";
import _ from "lodash";
import VDevider from "@/Shared/VDevider.vue";
import VShowStep1 from "../Manifest/_partials/VShowStep1.vue";
import VShowStep2 from "../Manifest/_partials/VShowStep2.vue";
import VShowStep3 from "../Manifest/_partials/VShowStep3.vue";
import VShowStep4 from "../Manifest/_partials/VShowStep4.vue";
import VShowOverall from "../Manifest/_partials/VShowOverall.vue";
import VShowOverallAdditional from "../Manifest/_partials/VShowOverallAdditional.vue";
import { computed } from "vue";
import { useManifest } from "../../Composable/useManifest";

const props = defineProps({
    title: String,
    additional: Array,
});

const {
    qrcode,
    boats,
    company,
    manifest,
    urlEdit,
    urlBack,
    urlPayment,
    urlDownload,
    bypassApproval,
    isSeafest,
} = props.additional;

const breadcrumbs = [
    {
        url: "#",
        label: "Payment Status",
    },
];

const {
    isPaymentStatusPaid,
    isPaymentStatusPending,
    isStatusNotInitiated,
    isStatusPending,
    isStatusApproved,
} = useManifest(manifest);
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
                <div class="d-flex justify-content-between mb-5">
                    <Link :href="urlBack" class="btn fw-bold">
                        <span class="material-icons"> chevron_left </span>
                        Back To Payment Status</Link
                    >
                    <div class="">
                        <a
                            v-if="!isPaymentStatusPending"
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
                <div class="d-flex justify-content-between">
                    <h3 class="d-flex align-items-center mb-0 fw-bold">
                        RECEIPT
                    </h3>
                    <div v-if="qrcode" class="text-end">
                        <img
                            :src="qrcode"
                            alt="qrcode for approval"
                            width="120px"
                            class="mb-2"
                        />
                        <div class="text-muted" style="font-size: 0.8em; max-width: 400px;">
                            Please proceed to the authorities with the QR code for scanning, and kindly bring along your hardcopy license as well.
                        </div>
                    </div>
                </div>
                <VDevider />
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div>
                            <strong>Date Created : </strong>
                            <span>{{ manifest.created_date }}</span>
                        </div>
                        <div>
                            <strong>Manifest Form Ref No. : </strong>
                            <span>{{ manifest.form_number }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div
                            class="d-flex align-items-center justify-content-end mb-1"
                        >
                            <strong class="me-3">Payment Status:</strong>
                            <span
                                class="px-4 status-indicator text-center"
                                :class="{
                                    'bg-success text-white':
                                        isPaymentStatusPaid,
                                    'bg-warning text-white':
                                        isPaymentStatusPending,
                                }"
                                >{{ manifest.payment_status_text }}</span
                            >
                        </div>
                        <div
                            v-if="!bypassApproval && !isSeafest"
                            class="d-flex align-items-center justify-content-end"
                        >
                            <strong class="me-3">Authority Approval:</strong>
                            <span
                                class="px-4 status-indicator text-center"
                                :class="{
                                    'bg-secondary text-white':
                                        isStatusNotInitiated,
                                    'bg-warning text-white': isStatusPending,
                                    'bg-success text-white': isStatusApproved,
                                }"
                                >{{ manifest.status_text }}</span
                            >
                        </div>
                    </div>
                </div>
                <VShowStep1
                    :manifest="manifest"
                    :boats="boats"
                    :company="company"
                />

                <VShowStep2 :manifest="manifest" />

                <VShowStep3 :manifest="manifest" />

                <VShowStep4 :manifest="manifest" />

                <div class="mb-3">
                    <VShowOverall
                        v-if="manifest.manifest_fee"
                        :manifest="manifest"
                    />
                </div>

                <div
                    v-if="manifest.manifest_fee_additional.length"
                    class="mb-3"
                >
                    <h6 class="bg-primary text-white px-4 py-3 mb-4">
                        Additional Payment
                    </h6>
                    <div
                        v-for="manifest_fee in manifest.manifest_fee_additional"
                    >
                        <VShowOverallAdditional :manifest_fee="manifest_fee" />
                        <VDevider class="my-3" />
                    </div>
                </div>

                <div
                    v-if="manifest.manifest_fee_additional_pending"
                    class="mb-3"
                >
                    <h6 class="bg-primary text-white px-4 py-3 mb-4">
                        Additional Payment
                        <span>(PENDING)</span>
                    </h6>
                    <VShowOverallAdditional
                        :manifest_fee="manifest.manifest_fee_additional_pending"
                    />
                </div>

                <template v-if="!isSeafest">
                    <h6 class="bg-primary text-white px-4 py-3">Approvals</h6>

                    <div
                        class="bg-light border p-3 mt-3"
                        v-if="additional.approvements"
                    >
                        <div class="d-flex justify-content-between">
                            <h5 class="d-flex align-items-center mb-1">
                                Approvement History
                            </h5>
                        </div>
                        <VDevider class="mb-3" />
                        <VApprovementHistory
                            :approvements="additional.approvements"
                        />
                    </div>

                    <VShowJettyApproval :manifest="manifest" />
                </template>
            </div>
        </div>
    </div>
</template>

<style lang="css">
.status-indicator {
    min-width: 140px;
}
</style>
