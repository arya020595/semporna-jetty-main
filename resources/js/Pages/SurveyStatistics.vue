<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import VHeaderBreadcrumb from "@/Shared/VHeaderBreadcrumb.vue";
import VInputWithLabel from "@/Shared/Form/VInputWithLabel.vue";
import VButton from "../Shared/Buttons/VButton.vue";

import VDevider from "@/Shared/VDevider.vue";
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
} from "chart.js";
import { Bar, Line } from "vue-chartjs";

ChartJS.register(
    CategoryScale,
    LinearScale,
    BarElement,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend
);

const props = defineProps({
    title: String,
    additional: Object,
});

const breadcrumbs = [
    {
        url: "#",
        label: "Survey Statistics",
    },
];

const { start, end, urlSubmit, chartByTemplate, chartByPeriode } =
    props.additional;

const form = useForm({
    start: start,
    end: end,
});

const reLoad = () => {
    form.get(urlSubmit);
};
</script>

<template>
    <Head>
        <title>{{ title }}</title>
    </Head>

    <div class="p-3">
        <VHeaderBreadcrumb :breadcrumbs="breadcrumbs" />
        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-4">
                        <VInputWithLabel
                            elId="start"
                            label="Start"
                            type="date"
                            v-model:value="form.start"
                            :error="form.errors?.start"
                        />
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-4">
                        <VInputWithLabel
                            elId="end"
                            label="End"
                            type="date"
                            v-model:value="form.end"
                            :error="form.errors?.end"
                        />
                    </div>
                    <div class="col-4">
                        <VButton btnStyle="btn-success" @onClick="reLoad"
                            >Update</VButton
                        >
                    </div>
                </div>
                <VDevider />
                <div class="row">
                    <div class="col-12 col-md-6">
                        <Bar
                            :data="chartByTemplate"
                            :options="{ plugins: { legend: false } }"
                        />
                    </div>
                    <div class="col-12 col-md-6">
                        <Line :data="chartByPeriode" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
