<script setup>
import { Head, router, useForm } from "@inertiajs/vue3";
import VAlert from "@/Shared/VAlert.vue";
import VHeaderBreadcrumb from "@/Shared/VHeaderBreadcrumb.vue";
import VShowStep1 from "./_partials/VShowStep1.vue";
import { ref } from "vue";
import VShowStep2 from "./_partials/VShowStep2.vue";

const props = defineProps({
    title: String,
    additional: Array,
});

const { company, urlBack, urlEdit } = props.additional;

const step = ref(props.additional.currentStep ?? 1);

const breadcrumbs = [
    {
        url: "#",
        label: "Profile",
    },
];

const onNext = () => {
    step.value = 2;
    changeUrlParam(2);
};

const onPrev = () => {
    step.value = 1;
    changeUrlParam(1);
};

const onBack = () => {
    router.get(urlBack);
};

const changeUrlParam = (value) => {
    const searchURL = new URL(window.location);
    searchURL.searchParams.set("step", value);
    window.history.replaceState({}, "", searchURL);
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
                <VShowStep2
                    v-if="step == 2"
                    :company="company"
                    :urlEdit="urlEdit"
                    @onPrev="onPrev"
                />
                <VShowStep1
                    v-else
                    :company="company"
                    :urlEdit="urlEdit"
                    @onNext="onNext"
                    @onBack="onBack"
                />
            </div>
        </div>
    </div>
</template>
