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
import VButton from "../../Shared/Buttons/VButton.vue";
import { useManifest } from "../../Composable/useManifest";
import Swal from "sweetalert2";
import VApprovementHistory from "./_partials/VApprovementHistory.vue";
import VShowJettyApproval from "../../Shared/Manifest/VShowJettyApproval.vue";

const props = defineProps({
    title: String,
    additional: Array,
});

const {
    qrcode,
    boats,
    company,
    manifest,
    urlBack,
    urlDownload,
    urlApprove,
    canApprove,
    approvements,
    bypassApproval,
    isAuthority,
    isSeafest,
} = props.additional;

const breadcrumbs = [
    {
        url: "#",
        label: "Manifest Form",
    },
];

const form = useForm({
    status: null,
    comments: "",
});

const {
    isPaymentStatusPaid,
    isPaymentStatusPending,
    isStatusNotInitiated,
    isStatusPending,
    isStatusApproved,
    isStatusRejected,
    isStatusOnProgress,
} = useManifest(manifest);

const submitApproval = async (value) => {
    const approvalText =
        value == 1 ? "Approve" : value == 3 ? "set Amend to" : "Reject";

    const result = await Swal.fire({
        title: "Are you sure want to " + approvalText + " this manifest?",
        showCancelButton: true,
        confirmButtonText: "Yes",
    });

    /* Read more about isConfirmed, isDenied below */
    if (!result.isConfirmed) {
        return false;
    }

    form.status = value;
    form.post(urlApprove);
};
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
                        Back To Activity</Link
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
                                    'bg-warning text-white':
                                        isStatusPending || isStatusOnProgress,
                                    'bg-success text-white': isStatusApproved,
                                    'bg-danger text-white': isStatusRejected,
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
                    :isAuthority="isAuthority"
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

                <VDevider />
                <div class="row" v-if="canApprove && !isSeafest">
                    <div class="col-md-6 mb-2">
                        <h5 class="fw-bold">Approve This Manifest?</h5>
                    </div>
                    <div class="col-md-6 mb-2">
                        <textarea
                            v-model="form.comments"
                            class="form-control comment-textarea mb-3"
                            placeholder="Comments"
                            :disabled="form.isProcessing"
                        ></textarea>
                        <div class="text-md-end">
                            <VButton
                                btnStyle="btn-success me-2 px-4"
                                @onClick="submitApproval(1)"
                            >
                                Approve
                            </VButton>
                            <VButton
                                btnStyle="btn-warning me-2 px-4"
                                @onClick="submitApproval(3)"
                            >
                                Amend
                            </VButton>
                            <VButton
                                btnStyle="btn-danger px-4"
                                @onClick="submitApproval(-1)"
                            >
                                Reject
                            </VButton>
                        </div>
                    </div>
                </div>

                <VShowJettyApproval :manifest="manifest" v-if="!isSeafest" />

                <div class="bg-light border p-3 mt-3" v-if="approvements && !isSeafest">
                    <div class="d-flex justify-content-between">
                        <h5 class="d-flex align-items-center mb-1">
                            Approvement History
                        </h5>
                    </div>
                    <VDevider class="mb-3" />
                    <VApprovementHistory :approvements="approvements" />
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="css">
.status-indicator {
    min-width: 140px;
}

.comment-textarea {
    min-height: 120px;
    max-height: 120px;
}
</style>
