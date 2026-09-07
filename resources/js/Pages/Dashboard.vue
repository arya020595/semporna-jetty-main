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
        label: "Dashboard",
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
        <div class="card mt-3">
            <div class="card-body p-2">
                <div class="ratio ratio-16x9">
                    <iframe
                        title="MyJetty Dashboard"
                        src="https://app.powerbi.com/view?r=eyJrIjoiNDljNjU4NGMtYTJlZi00NDEyLThlZDMtMWVjNGNiMGZjZmU5IiwidCI6IjNiNmFjMTJhLTgwMDAtNGYwZS1iYmMyLWYwNzhiNTY0NGFlNiIsImMiOjEwfQ%3D%3D"
                        frameborder="0"
                        allowfullscreen="true"
                        style="border: none;"
                    ></iframe>
                </div>
            </div>
        </div>
    </div>
</template>

