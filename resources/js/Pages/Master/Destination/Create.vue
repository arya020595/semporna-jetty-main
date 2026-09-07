<script setup>
import { Head } from "@inertiajs/vue3";
import VAlert from "@/Shared/VAlert.vue";
import VDevider from "@/Shared/VDevider.vue";
import VHeaderBreadcrumb from "@/Shared/VHeaderBreadcrumb.vue";
import VTitleWithBackLink from "@/Shared/VTitleWithBackLink.vue";
import VForm from "./_partials/VForm.vue";

const props = defineProps({
    title: String,
    additional: Array,
});

const { urlIndex, urlStore, filters } = props.additional;

const breadcrumbs = [
    {
        url: urlIndex,
        label: "Island",
    },
    {
        url: "#",
        label: "Add New",
    },
];
</script>

<template>
    <Head>
        <title>{{ title }}</title>
    </Head>

    <div class="p-3">
        <VHeaderBreadcrumb :breadcrumbs="breadcrumbs" />

        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <VTitleWithBackLink
                        :href="urlIndex"
                        :filters="filters ?? {}"
                    >
                        Add New Island
                    </VTitleWithBackLink>
                </div>
                <VDevider class="mb-3" />
                <VAlert />

                <VForm :value="{}" :urlSubmit="urlStore" :isCreate="true" />
            </div>
        </div>
    </div>
</template>
