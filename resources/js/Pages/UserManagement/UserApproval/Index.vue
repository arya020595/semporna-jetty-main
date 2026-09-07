<script setup>
import { Head, Link, router, useForm } from "@inertiajs/vue3";

import Datatables from "@/Shared/Tables/Datatables.vue";
import DatatableFooterWrapper from "@/Shared/Tables/DatatableFooterWrapper.vue";
import VAlert from "@/Shared/VAlert.vue";
import VHeaderBreadcrumb from "@/Shared/VHeaderBreadcrumb.vue";
import VButtonCreate from "@/Shared/HeaderButton/VButtonCreate";
import Swal from "sweetalert2";

let props = defineProps({
    title: String,
    additional: Object,
});

const { canCreate, urlCreate, urlIndex, columns } = props.additional;

const breadcrumbs = [
    {
        url: "#",
        label: "User Approval",
    },
];

const form = useForm({
    is_approved: null,
});

const changePageLength = (value) => {
    getUser({ per_page: value });
};

const onFilter = (value) => {
    getUser({
        search_fields: value.search_fields,
        search_values: value.search_values,
        per_page: value.per_page ?? 20,
        order_by: value.order_by,
        order_type: value.order_type,
    });
};

const getUser = (params) => {
    router.get(urlIndex, params, {
        preserveState: true,
        replace: true,
    });
};

const tableClickAction = async (value) => {
    const result = await Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success px-5 me-3",
            denyButton: "btn btn-danger px-5 ",
        },
        buttonsStyling: false,
    }).fire({
        html: formatAlertHtml(value.data),
        showDenyButton: true,
        confirmButtonText: `Approve!`,
        confirmButtonAriaLabel: "Approve!",
        denyButtonText: `Reject`,
        denyButtonAriaLabel: "Reject",
        showCloseButton: true,
    });

    if (result.isConfirmed) {
        form.is_approved = true;
        form.post(value.url);
    } else if (result.isDenied) {
        form.is_approved = false;
        form.post(value.url);
    }
};

const formatAlertHtml = (data) => {
    return `
            <div class="fw-bold text-start mb-4">Approve This User?</div>

            <div class="row align-items-sm-center mb-2" style="width:100%">
                <label class="col-md-4 label-size text-start fw-bold mb-sm-0 mb-0">
                    Full Name :
                </label>
                <div class="col-md-8 custom-position-relative">
                    <input
                        type="text"
                        class="form-control-plaintext"
                        value="${data.name}"
                        readonly
                    />
                </div>
            </div>
            <div class="row align-items-sm-center mb-2" style="width:100%">
                <label class="col-md-4 label-size text-start fw-bold mb-sm-0 mb-0">
                    Email Address :
                </label>
                <div class="col-md-8 custom-position-relative">
                    <input
                        type="text"
                        class="form-control-plaintext"
                        value="${data.email}"
                        readonly
                    />
                </div>
            </div>
            <div class="row align-items-sm-center mb-2" style="width:100%">
                <label class="col-md-4 label-size text-start fw-bold mb-sm-0 mb-0">
                    IC No. :
                </label>
                <div class="col-md-8 custom-position-relative">
                    <input
                        type="text"
                        class="form-control-plaintext"
                        value="${data.ic_no}"
                        readonly
                    />
                </div>
            </div>
            <div class="row align-items-sm-center mb-2" style="width:100%">
                <label class="col-md-4 label-size text-start fw-bold mb-sm-0 mb-0">
                    User Role :
                </label>
                <div class="col-md-8 custom-position-relative">
                    <input
                        type="text"
                        class="form-control-plaintext"
                        value="${data.role}"
                        readonly
                    />
                </div>
            </div>
            <div class="row align-items-sm-center mb-2" style="width:100%">
                <label class="col-md-4 label-size text-start fw-bold mb-sm-0 mb-0">
                    Jetty :
                </label>
                <div class="col-md-8 custom-position-relative">
                    <input
                        type="text"
                        class="form-control-plaintext"
                        value="${data.jetty}"
                        readonly
                    />
                </div>
            </div>`;
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
                <div class="text-end mb-3" v-if="canCreate">
                    <VButtonCreate :href="urlCreate"> Add User </VButtonCreate>
                </div>
                <div class="dataTables_wrapper dt-bootstrap5">
                    <Datatables
                        :columns="columns"
                        :pagination="additional.users"
                        :filters="additional.filters"
                        @onFilter="onFilter"
                        @onActionClick="tableClickAction"
                    />
                    <DatatableFooterWrapper
                        :pagination="additional.users.meta"
                        :filters="additional.filters"
                        @onChange="changePageLength"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
