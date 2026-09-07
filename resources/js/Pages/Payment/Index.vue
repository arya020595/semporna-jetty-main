<script setup>
import { ref, watch } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import debounce from "lodash/debounce";

import Datatables from "@/Shared/Tables/Datatables.vue";
import DatatableFooterWrapper from "@/Shared/Tables/DatatableFooterWrapper.vue";

import VAlert from "@/Shared/VAlert.vue";

import VHeaderBreadcrumb from "@/Shared/VHeaderBreadcrumb.vue";
import VButton from "../../Shared/Buttons/VButton.vue";
import VModal from "@/Shared/VModal.vue";
import VInputWithLabel from "@/Shared/Form/VInputWithLabel.vue";

let props = defineProps({
    title: String,
    additional: Object,
});

const { columns, urlIndex, urlConfirmation, urlReceipts } = props.additional;

const selectedData = ref([]);
const isMultiplePayment = ref(false);

// Receipt Generation State
const showReceiptModal = ref(false);
const receiptModalRef = ref(null);
const receiptForm = ref({
    start_date: "",
    end_date: "",
});

const breadcrumbs = [
    {
        url: "#",
        label: "Payment",
    },
];

const changePageLength = (value) => {
    getData({ per_page: value });
};

const changeOrder = (value) => {
    getData({
        search_fields: value.search_fields,
        search_values: value.search_values,
        per_page: props.additional.filters.per_page ?? 20,
        order_by: value.order_by,
        order_type: value.order_type,
    });
};

const getData = (params) => {
    router.get(urlIndex, params, {
        preserveState: true,
        replace: true,
    });
};

const selectPayment = (value) => {
    selectedData.value = value;
};

const setMultiplePayment = (value) => {
    isMultiplePayment.value = value;

    const filters = props.additional.filters;
    filters.search_fields = ["payment_status"];
    filters.search_values = [value ? 0 : null];
    getData(filters);
};

const payNow = () => {
    if (!selectedData.value.length) {
        return false;
    }

    router.get(urlConfirmation, {
        manifest_id: selectedData.value,
    });
};

const openReceiptModal = () => {
    const today = new Date().toISOString().split("T")[0];
    receiptForm.value.start_date = today;
    receiptForm.value.end_date = today;
    showReceiptModal.value = true;
};

const closeReceiptModal = () => {
    if (receiptModalRef.value) {
        receiptModalRef.value.closeModal();
    } else {
        showReceiptModal.value = false;
    }
};

const downloadReceipts = () => {
    const url = `${urlReceipts}?start_date=${receiptForm.value.start_date}&end_date=${receiptForm.value.end_date}`;
    window.open(url, "_blank");
    closeReceiptModal();
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
                <div class="text-end">
                    <VButton
                        v-if="!isMultiplePayment"
                        btnStyle="btn-primary me-2"
                        @onClick="openReceiptModal"
                    >
                        <span class="material-icons me-1">receipt_long</span>
                        Generate Payment Receipts
                    </VButton>

                    <VButton
                        v-if="!isMultiplePayment"
                        btnStyle="btn-success"
                        @onClick="setMultiplePayment(true)"
                    >
                        <span class="icon-paynow me-1">
                            <img
                                src="/assets/images/icon_paynow.png"
                                alt="paynow button"
                            />
                        </span>
                        Make Multiple Payment
                    </VButton>
                    <template v-if="isMultiplePayment">
                        <VButton
                            btnStyle="btn-success me-1"
                            :isDisabled="!selectedData.length"
                            @onClick="payNow"
                        >
                            <span class="icon-paynow me-1">
                                <img
                                    src="/assets/images/icon_paynow.png"
                                    alt="paynow button"
                                />
                            </span>
                            Pay Now
                        </VButton>
                        <VButton
                            btnStyle="btn-danger"
                            @onClick="setMultiplePayment(false)"
                        >
                            <span class="material-icons me-1">cancel</span>
                            Cancel
                        </VButton>
                    </template>
                </div>
                <div class="dataTables_wrapper dt-bootstrap5">
                    <Datatables
                        :columns="
                            columns.filter((item) => {
                                if (
                                    isMultiplePayment &&
                                    item.name == 'action'
                                ) {
                                    return false;
                                }

                                return true;
                            })
                        "
                        :pagination="additional.data"
                        :filters="additional.filters"
                        :isCheckbox="isMultiplePayment"
                        @onFilter="changeOrder"
                        @onChecked="selectPayment"
                    />

                    <DatatableFooterWrapper
                        :pagination="additional.data.meta"
                        :filters="additional.filters"
                        @onChange="changePageLength"
                    />
                </div>
            </div>
        </div>
    </div>

    <VModal
        v-if="showReceiptModal"
        ref="receiptModalRef"
        title="Generate Payment Receipts"
        @onClose="showReceiptModal = false"
    >
        <template #body>
            <div class="p-3">
                <div class="row">
                    <div class="col-12 mb-3">
                        <VInputWithLabel
                            elId="receipt_start_date"
                            label="Start Date"
                            type="date"
                            v-model:value="receiptForm.start_date"
                            :widthLabel="4"
                            :widthInput="8"
                        />
                    </div>
                    <div class="col-12 mb-3">
                        <VInputWithLabel
                            elId="receipt_end_date"
                            label="End Date"
                            type="date"
                            v-model:value="receiptForm.end_date"
                            :widthLabel="4"
                            :widthInput="8"
                            :additionalAttr="{ min: receiptForm.start_date }"
                        />
                    </div>
                </div>
            </div>
        </template>
        <template #footer>
            <VButton btnStyle="btn-secondary me-2" @onClick="closeReceiptModal">
                Cancel
            </VButton>
            <VButton btnStyle="btn-primary" @onClick="downloadReceipts">
                Download PDF
            </VButton>
        </template>
    </VModal>
</template>
