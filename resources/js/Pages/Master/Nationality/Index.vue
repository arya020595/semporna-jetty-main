<script setup>
import { ref, watch } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import debounce from "lodash/debounce";

import Datatables from "@/Shared/Tables/Datatables.vue";
import DatatableFooterWrapper from "@/Shared/Tables/DatatableFooterWrapper.vue";

import VAlert from "@/Shared/VAlert.vue";

import VHeaderBreadcrumb from "@/Shared/VHeaderBreadcrumb.vue";
import VButtonCreate from "@/Shared/HeaderButton/VButtonCreate";

let props = defineProps({
    title: String,
    additional: Array,
    nationality: Object,
    filters: Object,
    columns: Array,
    canCreate: Boolean,
    urlCreate: String,
});

const breadcrumbs = [
    {
        url: "#",
        label: "Nationality",
    },
];

const changePageLength = (value) => {
    getNationality({ per_page: value });
};

const changeOrder = (value) => {
    getNationality({
        search_fields: value.search_fields,
        search_values: value.search_values,
        per_page: props.additional.filters.per_page ?? 20,
        order_by: value.order_by,
        order_type: value.order_type,
    });
};

const getNationality = (params) => {
    router.get("/nationality", params, {
        preserveState: true,
        replace: true,
    });
};
</script>

<template>
    <Head>
        <title>Nationality Management</title>
    </Head>

    <div class="p-3">
        <VHeaderBreadcrumb :breadcrumbs="breadcrumbs" />

        <VAlert />

        <div class="card">
            <div class="card-body">
                <div class="text-end mb-3">
                    <VButtonCreate :href="additional.urlCreate">
                        Add Nationality
                    </VButtonCreate>
                </div>

                <div class="dataTables_wrapper dt-bootstrap5">
                    <Datatables
                        :columns="additional.columns"
                        :pagination="additional.nationality"
                        :filters="additional.filters"
                        @onFilter="changeOrder"
                    />

                    <DatatableFooterWrapper
                        :pagination="additional.nationality.meta"
                        :filters="additional.filters"
                        @onChange="changePageLength"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
