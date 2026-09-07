<script setup>
import { ref, onMounted } from "vue";
import debounce from "lodash/debounce";
import TransactionBreakdownTable from "./TransactionBreakdownTable.vue";

const props = defineProps({
    payments: { type: Array, required: true },
    columns: { type: Array, required: true },
    filters: { type: Object, required: true },
    pagination: { type: Object, required: true },
});

const emit = defineEmits(["onFilter"]);

const expandedRows = ref(new Set());

const search_fields = props.columns
    .filter((column) => column.searchable)
    .map((column) => column.name);

const search_values = ref(
    search_fields.map((field) => {
        const index = props.filters.search_fields?.indexOf(field);
        return {
            name: field,
            value:
                index !== -1 && index !== undefined
                    ? props.filters.search_values?.[index]
                    : "",
        };
    }),
);

function toggleRow(id) {
    if (expandedRows.value.has(id)) {
        expandedRows.value.delete(id);
    } else {
        expandedRows.value.add(id);
    }
}

function isExpanded(id) {
    return expandedRows.value.has(id);
}

const changeOrder = (column) => {
    if (!column.orderable) return;

    emit("onFilter", {
        ...props.filters,
        order_by: column.name,
        order_type:
            props.filters.order_type === "asc" &&
            props.filters.order_by === column.name
                ? "desc"
                : "asc",
    });
};

const search = debounce(() => {
    const fields = search_values.value.map((item) => item.name);
    const values = search_values.value.map((item) => item.value);

    emit("onFilter", {
        ...props.filters,
        search_fields: fields,
        search_values: values,
    });
}, 500);

function sortClass(field) {
    return [
        "sorting",
        props.filters.order_by === field && props.filters.order_type === "asc"
            ? "sorting_asc"
            : "",
        props.filters.order_by === field && props.filters.order_type === "desc"
            ? "sorting_desc"
            : "",
    ];
}

function getSearchValueRef(fieldName) {
    const ref = search_values.value.find((item) => item.name === fieldName);
    return ref || { value: "" };
}
</script>

<template>
    <div class="card">
        <div class="card-body">
            <div class="dataTables_wrapper dt-bootstrap5">
                <div class="row dt-row mb-2">
                    <div class="col-sm-12 table-responsive">
                        <table
                            class="table table-hover dataTable"
                            style="width: 100%"
                        >
                            <thead>
                                <tr>
                                    <th style="width: 40px"></th>
                                    <th style="width: 40px">No.</th>
                                    <th
                                        v-for="column in columns"
                                        :key="column.name"
                                        :class="[
                                            column.orderable
                                                ? sortClass(column.name)
                                                : '',
                                            column.name === 'amount'
                                                ? 'text-end'
                                                : '',
                                        ]"
                                        @click="changeOrder(column)"
                                        :style="{
                                            cursor: column.orderable
                                                ? 'pointer'
                                                : 'default',
                                        }"
                                    >
                                        {{ column.label }}
                                    </th>
                                </tr>
                                <!-- Search Row -->
                                <tr v-if="search_fields.length > 0">
                                    <th></th>
                                    <th></th>
                                    <th
                                        v-for="column in columns"
                                        :key="'search-' + column.name"
                                    >
                                        <template v-if="column.searchable">
                                            <select
                                                v-if="
                                                    column.searchtype ===
                                                    'select'
                                                "
                                                class="form-select form-select-sm"
                                                v-model="
                                                    getSearchValueRef(
                                                        column.name,
                                                    ).value
                                                "
                                                @change="search"
                                            >
                                                <option value="">
                                                    {{ "All " + column.label }}
                                                </option>
                                                <option
                                                    v-for="option in column.options"
                                                    :key="option.id"
                                                    :value="option.id"
                                                >
                                                    {{ option.label }}
                                                </option>
                                            </select>
                                            <input
                                                v-else-if="
                                                    column.searchtype === 'date'
                                                "
                                                class="form-control form-control-sm"
                                                type="date"
                                                v-model="
                                                    getSearchValueRef(
                                                        column.name,
                                                    ).value
                                                "
                                                @input="search"
                                            />
                                            <input
                                                v-else
                                                class="form-control form-control-sm"
                                                :type="
                                                    column.searchtype ===
                                                    'number'
                                                        ? 'number'
                                                        : 'search'
                                                "
                                                v-model="
                                                    getSearchValueRef(
                                                        column.name,
                                                    ).value
                                                "
                                                :placeholder="
                                                    'Search ' + column.label
                                                "
                                                @input="search"
                                            />
                                        </template>
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-if="payments.length === 0">
                                    <td
                                        :colspan="columns.length + 2"
                                        class="text-center py-4 text-secondary"
                                    >
                                        <span
                                            class="material-icons d-block mb-2"
                                            style="font-size: 40pt"
                                            >content_paste_search</span
                                        >
                                        <strong>There is no data yet!</strong>
                                    </td>
                                </tr>

                                <template
                                    v-for="(payment, index) in payments"
                                    :key="payment.id"
                                >
                                    <tr
                                        :class="{
                                            odd: index % 2 === 0,
                                            even: index % 2 !== 0,
                                        }"
                                        style="cursor: pointer"
                                        @click="toggleRow(payment.id)"
                                    >
                                        <td class="text-center align-middle">
                                            <span
                                                class="material-icons text-secondary"
                                                style="
                                                    font-size: 20px;
                                                    transition: transform 0.2s;
                                                "
                                                :style="{
                                                    transform: isExpanded(
                                                        payment.id,
                                                    )
                                                        ? 'rotate(90deg)'
                                                        : 'rotate(0)',
                                                }"
                                                >chevron_right</span
                                            >
                                        </td>
                                        <td class="align-middle">
                                            {{ pagination.from + index }}
                                        </td>

                                        <template
                                            v-for="column in columns"
                                            :key="column.name"
                                        >
                                            <td
                                                v-if="
                                                    column.name ===
                                                    'departure_date'
                                                "
                                                class="align-middle"
                                            >
                                                <div
                                                    v-if="
                                                        payment.departure_date_display.includes(
                                                            '|',
                                                        )
                                                    "
                                                >
                                                    <div>
                                                        {{
                                                            payment.departure_date_display.split(
                                                                "|",
                                                            )[0]
                                                        }}
                                                    </div>
                                                    <small
                                                        class="text-muted"
                                                        style="
                                                            font-size: 0.75rem;
                                                        "
                                                    >
                                                        {{
                                                            payment.departure_date_display.split(
                                                                "|",
                                                            )[1]
                                                        }}
                                                    </small>
                                                </div>
                                                <div v-else>
                                                    {{
                                                        payment.departure_date_display
                                                    }}
                                                </div>
                                            </td>

                                            <td
                                                v-else-if="
                                                    column.name === 'code'
                                                "
                                                class="align-middle"
                                            >
                                                <div
                                                    class="text-primary fw-semibold"
                                                >
                                                    {{ payment.code }}
                                                </div>
                                                <small
                                                    v-if="
                                                        payment.transaction_id
                                                    "
                                                    class="text-muted"
                                                    >TXN:
                                                    {{
                                                        payment.transaction_id
                                                    }}</small
                                                >
                                            </td>

                                            <td
                                                v-else-if="
                                                    column.name === 'created_at'
                                                "
                                                class="align-middle"
                                            >
                                                {{ payment.created_at }}
                                            </td>

                                            <td
                                                v-else-if="
                                                    column.name === 'first_name'
                                                "
                                                class="align-middle"
                                            >
                                                <div>
                                                    {{ payment.payer_name }}
                                                </div>
                                                <small
                                                    v-if="payment.email !== '-'"
                                                    class="text-muted"
                                                    >{{ payment.email }}</small
                                                >
                                            </td>

                                            <td
                                                v-else-if="
                                                    column.name ===
                                                    'manifest_count'
                                                "
                                                class="align-middle"
                                            >
                                                <span
                                                    class="badge bg-secondary"
                                                >
                                                    {{ payment.manifest_count }}
                                                    {{
                                                        payment.manifest_count ===
                                                        1
                                                            ? "manifest"
                                                            : "manifests"
                                                    }}
                                                </span>
                                            </td>

                                            <td
                                                v-else-if="
                                                    column.name === 'amount'
                                                "
                                                class="text-end align-middle fw-semibold"
                                            >
                                                {{ payment.amount_formatted }}
                                            </td>

                                            <td
                                                v-else-if="
                                                    column.name === 'status'
                                                "
                                                class="text-center align-middle"
                                                @click.stop
                                            >
                                                <span
                                                    class="badge w-100"
                                                    :class="{
                                                        'bg-success':
                                                            payment.status_badge ===
                                                            'success',
                                                        'bg-warning text-dark':
                                                            payment.status_badge ===
                                                            'warning',
                                                        'bg-danger':
                                                            payment.status_badge ===
                                                            'danger',
                                                        'bg-secondary':
                                                            payment.status_badge ===
                                                            'secondary',
                                                    }"
                                                    >{{
                                                        payment.status_text
                                                    }}</span
                                                >
                                            </td>

                                            <!-- Fallback -->
                                            <td v-else class="align-middle">
                                                {{ payment[column.name] }}
                                            </td>
                                        </template>
                                    </tr>

                                    <tr v-if="isExpanded(payment.id)">
                                        <td
                                            :colspan="columns.length + 2"
                                            class="p-0"
                                        >
                                            <TransactionBreakdownTable
                                                :fees="payment.manifest_fees"
                                                :amount-formatted="
                                                    payment.amount_formatted
                                                "
                                            />
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
