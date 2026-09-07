<script setup>
import { ref, computed } from "vue";
import { Head, router } from "@inertiajs/vue3";
import VHeaderBreadcrumb from "@/Shared/VHeaderBreadcrumb.vue";
import VAlert from "@/Shared/VAlert.vue";
import DatatableFooterWrapper from "@/Shared/Tables/DatatableFooterWrapper.vue";
import TransactionFilterBar from "./_partials/TransactionFilterBar.vue";
import TransactionBreakdownTable from "./_partials/TransactionBreakdownTable.vue";
import TransactionTable from "./_partials/TransactionTable.vue";

const props = defineProps({
    title: String,
    additional: Object,
});

const { urlIndex, filters: initialFilters } = props.additional;

const filters = ref({
    search_fields: initialFilters?.search_fields ?? [],
    search_values: initialFilters?.search_values ?? [],
    per_page: initialFilters?.per_page ?? 20,
    order_by: initialFilters?.order_by ?? "created_at",
    order_type: initialFilters?.order_type ?? "desc",
    date_from: initialFilters?.date_from ?? "",
    date_to: initialFilters?.date_to ?? "",
    status_filter: initialFilters?.status_filter ?? "",
});

const expandedRows = ref(new Set());
const breadcrumbs = [{ url: "#", label: "Transaction History" }];
const payments = computed(() => props.additional.data?.data ?? []);
const pagination = computed(() => props.additional.data?.meta ?? null);

function toggleRow(id) {
    expandedRows.value.has(id)
        ? expandedRows.value.delete(id)
        : expandedRows.value.add(id);
}
function isExpanded(id) {
    return expandedRows.value.has(id);
}

function getData(params) {
    router.get(urlIndex, params, { preserveState: true, replace: true });
}

function applyFilters() {
    getData({
        per_page: filters.value.per_page,
        order_by: filters.value.order_by,
        order_type: filters.value.order_type,
        date_from: filters.value.date_from || undefined,
        date_to: filters.value.date_to || undefined,
        search_fields: filters.value.status_filter !== "" ? ["status"] : [],
        search_values:
            filters.value.status_filter !== ""
                ? [filters.value.status_filter]
                : [],
    });
}

function resetFilters() {
    filters.value.date_from = "";
    filters.value.date_to = "";
    filters.value.status_filter = "";
    getData({ per_page: filters.value.per_page });
}

function sortBy(field) {
    filters.value.order_type =
        filters.value.order_by === field && filters.value.order_type === "asc"
            ? "desc"
            : "asc";
    filters.value.order_by = field;
    applyFilters();
}

function sortClass(field) {
    return [
        "sorting",
        filters.value.order_by === field && filters.value.order_type === "asc"
            ? "sorting_asc"
            : "",
        filters.value.order_by === field && filters.value.order_type === "desc"
            ? "sorting_desc"
            : "",
    ];
}

function changePageLength(value) {
    getData({ ...initialFilters, per_page: value });
}
</script>

<template>
    <Head
        ><title>{{ title }}</title></Head
    >

    <div class="p-3">
        <VHeaderBreadcrumb :breadcrumbs="breadcrumbs" />
        <VAlert />

        <!-- Filter -->
        <TransactionFilterBar
            :filters="filters"
            @applyFilters="applyFilters"
            @resetFilters="resetFilters"
        />

        <!-- Main Table -->
        <TransactionTable
            :payments="payments"
            :columns="props.additional.columns"
            :filters="filters"
            :pagination="props.additional.data?.meta ?? {}"
            @onFilter="getData"
        />

        <div class="card mt-3">
            <div class="card-body py-2">
                <DatatableFooterWrapper
                    v-if="pagination"
                    :pagination="pagination"
                    :filters="additional.filters"
                    @onChange="changePageLength"
                />
            </div>
        </div>
    </div>
</template>
