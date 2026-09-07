<script setup>
import { ref, onMounted, watch } from "vue";
import debounce from "lodash/debounce";
import { formatDate } from "@/Helpers/date.js";
import DatatableActionBtn from "./DatatableActionBtn.vue";

const props = defineProps({
    columns: Array,
    pagination: Object,
    config: Object,
    filters: Object,
    isCheckbox: {
        type: Boolean,
        default: false,
    },
});

const checkedData = ref([]);

const search_values = ref([]);

const emit = defineEmits(["onFilter", "onChecked", "onActionClick"]);

const search_fields = props.columns
    .filter((item) => item.searchable)
    .map((item) => item.name);

onMounted(() => {
    search_values.value = search_values.value.map((item, key) => {
        if (!props.filters.search_values) return item;

        item.value = props.filters.search_values[key] ?? "";
        return item;
    });
});

const changeOrder = (column) => {
    if (!column.orderable) return;

    emit("onFilter", {
        order_by: column.name,
        order_type:
            props.filters.order_type == "asc" &&
            props.filters.order_by == column.name
                ? "desc"
                : "asc",
        search_fields: props.filters.search_fields,
        search_values: props.filters.search_values,
    });
};

const search = debounce((column) => {
    const values = search_values.value.map((item) => item.value);

    emit("onFilter", {
        order_by: props.filters.order_by,
        order_type: props.filters.order_type,
        search_fields: search_fields,
        search_values: values,
    });
}, 500);

watch(checkedData, (newValue) => {
    emit("onChecked", checkedData.value);
});

const clickAction = (value) => {
    emit("onActionClick", value);
};
</script>
<template>
    <div class="row dt-row mb-2">
        <div class="col-sm-12 table-responsive">
            <table
                id="example"
                class="table table-hover dataTable"
                style="width: 100%"
                aria-describedby="example_info"
            >
                <thead>
                    <tr>
                        <th v-if="isCheckbox" scope="col"></th>
                        <th scope="col"></th>
                        <th
                            v-for="column in columns"
                            :key="column.name"
                            :class="{
                                sorting: column.orderable,
                                sorting_asc:
                                    filters.order_by == column.name &&
                                    filters.order_type == 'asc',
                                sorting_desc:
                                    filters.order_by == column.name &&
                                    filters.order_type != 'asc',
                            }"
                            @click="changeOrder(column)"
                            scope="col"
                        >
                            {{ column.label }}
                        </th>
                    </tr>
                    <tr v-if="search_fields.length > 0">
                        <th v-if="isCheckbox" scope="col"></th>
                        <th scope="col"></th>
                        <th
                            v-for="column in columns"
                            :key="column.name"
                            scope="col"
                        >
                            <template v-if="column.searchable == true">
                                <select
                                    v-if="column.searchtype == 'select'"
                                    class="form-select form-select-sm"
                                    ref="search_values"
                                    @input="search"
                                >
                                    <option value="">
                                        {{ "Select " + column.label }}
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
                                    v-else-if="column.searchtype == 'date'"
                                    class="form-control form-control-sm"
                                    type="date"
                                    ref="search_values"
                                    :placeholder="'Pick ' + column.label"
                                    @input="search"
                                />
                                <input
                                    v-else
                                    class="form-control form-control-sm"
                                    type="search"
                                    ref="search_values"
                                    :placeholder="'Search ' + column.label"
                                    @input="search"
                                />
                            </template>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="pagination.data.length == 0">
                        <td
                            :colspan="columns.length + 1"
                            class="text-center py-3 text-secondary"
                        >
                            <h3>
                                <span
                                    class="material-icons"
                                    style="font-size: 40pt"
                                >
                                    content_paste_search
                                </span>
                            </h3>

                            <strong>There is no data yet!</strong>
                        </td>
                    </tr>
                    <tr
                        v-else
                        v-for="(data, index) in pagination.data"
                        :key="index"
                        :class="{
                            odd: index % 2 == 0,
                            even: index % 2 == 1,
                        }"
                    >
                        <td v-if="isCheckbox">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="checkboxNoLabel"
                                :name="'checkbox_' + data.id"
                                v-model="checkedData"
                                :value="data.id"
                                aria-label="..."
                                :disabled="data.checkbox_state == -1"
                            />
                        </td>
                        <td>
                            <span>{{ pagination.meta.from + index }}</span>
                        </td>

                        <td
                            v-for="column in columns"
                            :key="column.name"
                            :class="column.class"
                        >
                            <template v-if="column.name == 'action'">
                                <DatatableActionBtn
                                    v-for="button in data.action"
                                    :key="button"
                                    :actionData="button.actionData"
                                    :icon="button.icon"
                                    :url="button.url"
                                    :label="button.label"
                                    :classStyle="button.classStyle"
                                    :method="button.method ?? 'get'"
                                    @onActionClick="clickAction"
                                />
                            </template>

                            <template v-else-if="column.isHtml">
                                <div v-html="data[column.name]"></div>
                            </template>

                            <template v-else>
                                {{
                                    column.isDateTime
                                        ? formatDate(data[column.name])
                                        : data[column.name]
                                }}
                            </template>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
