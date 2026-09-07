<script setup>
import { Head } from "@inertiajs/vue3";
import VInputReadonlyWithLabel from "@/Shared/Form/VInputReadonlyWithLabel.vue";
import VDevider from "@/Shared/VDevider.vue";
import VTitleWithBackLink from "@/Shared/VTitleWithBackLink.vue";
import VHeaderButtonEdit from "@/Shared/HeaderButton/VButtonEdit.vue";
import VHeaderBreadcrumb from "@/Shared/VHeaderBreadcrumb.vue";

const props = defineProps({
    title: String,
    additional: Array,
});

const { urlEdit, urlIndex, canEdit, filters, nationality } = props.additional;

const breadcrumbs = [
    {
        url: urlIndex,
        label: "Nationality",
    },
    {
        url: "#",
        label: "Detail",
    },
];
</script>

<template>
    <Head>
        <title>Show - Nationality Management</title>
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
                        Detail Nationality
                    </VTitleWithBackLink>
                    <div class="btn-wrapper">
                        <VHeaderButtonEdit v-if="canEdit" :href="urlEdit" />
                    </div>
                </div>

                <VDevider />
                <div class="row">
                    <div class="col-lg-6">
                        <VInputReadonlyWithLabel
                            label="Code"
                            type="text"
                            :value="nationality.code"
                        />
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <VInputReadonlyWithLabel
                            label="Nationality"
                            type="text"
                            :value="nationality.title"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
