<script setup>
import { computed, ref } from "vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import VDevider from "@/Shared/VDevider.vue";
import VAlert from "@/Shared/VAlert.vue";

import VHeaderBreadcrumb from "@/Shared/VHeaderBreadcrumb.vue";
import VButton from "../../Shared/Buttons/VButton.vue";

let props = defineProps({
    title: String,
    additional: Object,
});

const { data, urlIndex, urlPayment } = props.additional;

const breadcrumbs = [
    {
        url: urlIndex,
        label: "Payment",
    },
    {
        url: "#",
        label: "Payment Details",
    },
];

const form = useForm({
    manifest_id: props.additional.arrManifestId,
});

const processedData = computed(() => {
    const seenBoats = new Set();

    return data.map((item) => {
        let fee = parseFloat(item.overall_fee);
        let boatFee = parseFloat(item.boat_fee);
        let standardBoatFee = parseFloat(item.standard_boat_fee ?? 0);
        let boatId = item.boat_identifier + "_" + item.departure_date;
        let isWaived = false;
        let originalFee = fee;
        let waivedAmount = boatFee;

        // Scenario 1: Backend Waived (Fee is 0 in DB because of previous payment or rule)
        // We know it's waived if boatFee is 0 but standard fee > 0.
        // In this case, original fee should include the waived amount.
        if (boatFee == 0 && standardBoatFee > 0) {
            isWaived = true;
            originalFee = fee + standardBoatFee;
            waivedAmount = standardBoatFee;
        }
        // Scenario 2: Frontend Waived (Fee > 0 in DB, but duplicate in this batch or paid previously)
        else if ((seenBoats.has(boatId) || item.is_boat_already_paid) && boatFee > 0) {
            fee -= boatFee;
            isWaived = true;
            waivedAmount = boatFee;
            // originalFee stays as is (it includes the fee)
        } else {
            seenBoats.add(boatId);
        }

        return {
            ...item,
            original_fee: originalFee.toFixed(2),
            adjusted_fee: fee.toFixed(2),
            is_waived: isWaived,
            waived_amount: waivedAmount.toFixed(2),
        };
    });
});

const total = computed(() => {
    if (!processedData.value.length) {
        return parseFloat(0).toFixed(2);
    }
    return processedData.value
        .reduce((acc, item) => acc + parseFloat(item.adjusted_fee), 0)
        .toFixed(2);
});

const goBack = () => {
    router.get(urlIndex);
};

const payNow = () => {
    form.post(urlPayment, {
        onSuccess: async (pages) => {
            const urlPayment = pages.props.urlPayment;

            if (urlPayment) {
                window.location.href = urlPayment;
                return true;
            }

            return false;
        },
    });
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
                <div class="mb-4">
                    Confirm make payment based on the selected manifests?
                </div>

                <div class="table-responsive">
                    <table
                        id="table_payment_confirmation"
                        class="table table-hover dataTable"
                        style="width: 100%"
                    >
                        <thead>
                            <tr>
                                <th scope="col"></th>
                                <th scope="col"></th>
                                <th scope="col">Manifest No.</th>
                                <th scope="col">Departure Date</th>
                                <th scope="col">Boat No</th>
                                <th scope="col">Destination</th>
                                <th scope="col" style="text-align: center">
                                    Overall Fee
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in processedData">
                                <td>
                                    <Link
                                        :href="item.url"
                                        class="btn btn-xs btn btn-outline-warning"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        title="Show Detail Manifest"
                                    >
                                        <i class="fas fa-info"></i>
                                    </Link>
                                </td>
                                <td>{{ index + 1 }}</td>
                                <td>{{ item.form_number }}</td>
                                <td>{{ item.departure_date }}</td>
                                <td>{{ item.boat_number }}</td>
                                <td>{{ item.destination }}</td>
                                <td style="text-align: center">
                                    <div v-if="item.is_waived">
                                        <span
                                            class="text-decoration-line-through text-muted me-2"
                                        >
                                            RM {{ item.original_fee }}
                                        </span>
                                        <div class="fw-bold">
                                            RM {{ item.adjusted_fee }}
                                        </div>
                                        <div
                                            class="small text-muted fst-italic"
                                            style="font-size: 0.7em"
                                        >
                                            Manifest Boat Fee Waived (same boat & date)
                                        </div>
                                    </div>
                                    <div v-else>RM {{ item.adjusted_fee }}</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="text-end my-4">
                    <div class="form-control-lg d-inline fw-bold">
                        Total Fee :
                    </div>
                    <div class="form-control-lg bg-primary text-white d-inline">
                        <strong>RM {{ total }}</strong>
                    </div>
                </div>

                <VDevider />
                <div class="d-flex justify-content-between">
                    <VButton btnStyle="btn-primary" @onClick="goBack()"
                        >Go Back</VButton
                    >
                    <VButton btnStyle="btn-success" @onClick="payNow()">
                        <span class="icon-paynow me-1">
                            <img
                                src="/assets/images/icon_paynow.png"
                                alt="paynow button"
                            />
                        </span>
                        Pay Now</VButton
                    >
                </div>
            </div>
        </div>
    </div>
</template>
