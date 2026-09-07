<script setup>
import { watch } from "vue";
import { Head, router, useForm } from "@inertiajs/vue3";
import VDevider from "@/Shared/VDevider.vue";
import VAlert from "@/Shared/VAlert.vue";
import debounce from "lodash/debounce";

import VHeaderBreadcrumb from "@/Shared/VHeaderBreadcrumb.vue";
import VButton from "../../Shared/Buttons/VButton.vue";
import VInputWithBlockLabel from "../../Shared/Form/VInputWithBlockLabel.vue";
import VInputCCExpirationWithBlockLabel from "../../Shared/Form/VInputCCExpirationWithBlockLabel.vue";
import VInputPaymentMethodWithBlockLabel from "../../Shared/Form/VInputPaymentMethodWithBlockLabel.vue";
import Swal from "sweetalert2";

let props = defineProps({
    title: String,
    additional: Object,
});

const { arrManifestId, total, urlIndex, urlStore } = props.additional;

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
    payment_method: "",
    first_name: "",
    last_name: "",
    credit_card_no: "",
    security_code: "",
    card_expiration: "",
    amount: total,
    manifest_id: arrManifestId,
});

watch(
    () => form.credit_card_no,
    (newValue) => {
        if (!newValue) return;
        formatCreditCard(newValue);
    }
);

const formatCreditCard = debounce((creditCardNumber) => {
    // Remove all non-digit characters
    let formatted = creditCardNumber.replace(/\D/g, "");

    formatted = formatted.substring(0, 16);

    // Add space after every 4 digits
    formatted = formatted.replace(/(.{4})/g, "$1 ").trim("");

    formatted = formatted.replaceAll(" ", "-");

    // Update the model
    form.credit_card_no = formatted;
}, 1);

const cancel = async () => {
    const result = await Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success me-3",
            cancelButton: "btn btn-danger",
        },
        buttonsStyling: false,
    }).fire({
        html: `
            <span class="icon-paynow-blue">
                <img
                    src="/assets/images/icon_paynow_blue.png"
                    alt="paynow button"
                />
            </span>
            <div class="fw-bold">Cancel Payment?</div>
            <div class="mt-2 small">If you choose to cancel payment <strong>the manifest form will not be updated</strong> and will be redirected to your Activity Page</div>
  `,
        showCancelButton: true,
        focusConfirm: false,
        confirmButtonText: `Proceed Payment`,
        confirmButtonAriaLabel: "Proceed Payment",
        cancelButtonText: `Cancel Payment`,
        cancelButtonAriaLabel: "Cancel",
    });

    if (result.isDismissed && result.dismiss == "cancel") {
        router.get(urlIndex);
        return;
    }

    return false;
};

const payNow = async () => {
    const result = await Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-secondary me-3",
        },
        buttonsStyling: false,
    }).fire({
        html: `
            <span class="icon-paynow-blue">
                <img
                    src="/assets/images/icon_paynow_blue.png"
                    alt="paynow button"
                />
            </span>
            <div class="fw-bold">Confirm Make Payment?</div>
  `,
        showCancelButton: true,
        focusConfirm: false,
        confirmButtonText: `Proceed!`,
        confirmButtonAriaLabel: "Proceed!",
        cancelButtonText: `Cancel`,
        cancelButtonAriaLabel: "Cancel",
        reverseButtons: true,
    });

    if (!result.isConfirmed) {
        return false;
    }

    form.post(urlStore, {
        data: form,
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
                <div class="d-flex justify-content-between">
                    <h5 class="d-flex align-items-center mb-0">
                        Payment Details
                    </h5>
                </div>
                <VDevider class="mb-3" />

                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <VInputPaymentMethodWithBlockLabel
                                label="Payment Method:"
                                v-model:value="form.payment_method"
                                :error="form.errors?.payment_method"
                            />
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <VInputWithBlockLabel
                                    elId="first_name"
                                    label="First Name:"
                                    placeholder="First Name"
                                    v-model:value="form.first_name"
                                    :error="form.errors?.first_name"
                                />
                            </div>
                            <div class="col-md-6">
                                <VInputWithBlockLabel
                                    elId="last_name"
                                    label="Last Name:"
                                    placeholder="Last Name"
                                    v-model:value="form.last_name"
                                    :error="form.errors?.last_name"
                                />
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <VInputWithBlockLabel
                                    elId="credit_card_no"
                                    label="Credit Card No:"
                                    placeholder="0000-0000-0000-0000"
                                    v-model:value="form.credit_card_no"
                                    :error="form.errors?.credit_card_no"
                                />
                            </div>
                            <div class="col-md-6">
                                <VInputWithBlockLabel
                                    elId="security_code"
                                    label="Security Code"
                                    placeholder="CVV"
                                    v-model:value="form.security_code"
                                    :error="form.errors?.security_code"
                                />
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <VInputCCExpirationWithBlockLabel
                                    elId="card_expiration"
                                    label="Card Expiration:"
                                    v-model:value="form.card_expiration"
                                    :error="form.errors?.card_expiration"
                                />
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="fw-bold">Total Fee :</div>
                            <div class="fw-bold">
                                <h3 class="fw-bold">
                                    RM {{ parseFloat(total).toFixed(2) }}
                                </h3>
                            </div>
                        </div>

                        <div class="">
                            <VButton btnStyle="btn-success" @onClick="payNow()">
                                <span class="icon-paynow me-1">
                                    <img
                                        src="/assets/images/icon_paynow.png"
                                        alt="paynow button"
                                    /> </span
                                >Submit Payment</VButton
                            >
                            <VButton
                                btnStyle="btn-secondary ms-2"
                                @onClick="cancel()"
                                >Cancel Payment</VButton
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Overlay -->
    <div
        v-if="form.processing"
        class="modal show d-block"
        tabindex="-1"
        aria-hidden="true"
    >
        <div
            class="overlay bg-black bg-opacity-50 flex items-center justify-center z-50"
        ></div>
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body p-2 text-center">
                    <div class="mb-2">
                        <img
                            src="/assets/images/icon_payment_loading.png"
                            alt="paynow button"
                            style="max-width: 100px; width: 100%"
                        />
                    </div>
                    <div class="fw-bold mb-4">Payment Process</div>
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="css" scoped>
.overlay {
    top: 0px;
    bottom: 0px;
    right: 0px;
    left: 0px;
    position: fixed;
}
</style>
