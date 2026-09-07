<script setup>
import { ref, watch } from "vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import VButtonSubmit from "@/Shared/Buttons/VButtonSubmit";
import VInputWithLabel from "@/Shared/Form/VInputWithLabel.vue";
import VAlert from "@/Shared/VAlert.vue";
import VDevider from "@/Shared/VDevider.vue";
import VTitleWithBackLink from "@/Shared/VTitleWithBackLink.vue";
import VHeaderButtonInfo from "@/Shared/HeaderButton/VButtonInfo.vue";
import VHeaderBreadcrumb from "@/Shared/VHeaderBreadcrumb.vue";
import VForm from "./_partials/VForm.vue";

const props = defineProps({
    title: String,
    additional: Array,
});

const { data, urlUpdate, urlIndex, urlShow, canView, filters } =
    props.additional;

const breadcrumbs = [
    {
        url: urlIndex,
        label: "Activity",
    },
    {
        url: "#",
        label: "Edit",
    },
];
</script>

<template>
    <Head>
        <title>Edit - Activity Management</title>
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
                        Edit Activity
                    </VTitleWithBackLink>
                    <div class="btn-wrapper">
                        <VHeaderButtonInfo v-if="canView" :href="urlShow" />
                    </div>
                </div>

                <VDevider />
                <VAlert />

                <VForm :value="data" :urlSubmit="urlUpdate" :isCreate="false" />
            </div>
        </div>
    </div>
</template>
