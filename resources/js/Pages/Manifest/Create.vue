<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import VHeaderBreadcrumb from "@/Shared/VHeaderBreadcrumb.vue";
import { router } from "@inertiajs/vue3";

import VAlert from "@/Shared/VAlert.vue";
import VForm from "./_partials/VForm.vue";
import { onBeforeUnmount, ref } from "vue";
import { listTab } from "./_partials/tabs.config";
import { useTabManifestStore } from "../../Store/tabManifest";
import { useFormDataStore } from "../../Store/formData";
import _ from "lodash";
import Swal from "sweetalert2";

const props = defineProps({
    title: String,
    additional: Array,
});

const {
    boats,
    company,
    arrInstructor,
    arrDivemaster,
    arrGuide,
    arrDestination,
    arrDeparture,
    arrNationality,
    arrActivity,
    urlTemplate,
    urlTemplateStaff,
    urlStore,
} = props.additional;

const tabStore = useTabManifestStore();
const formdataStore = useFormDataStore();

const manifest = !_.isEmpty(formdataStore.getForm())
    ? formdataStore.formData
    : props.additional.manifest;

const breadcrumbs = [
    {
        url: "#",
        label: "Manifest Form",
    },
];

let tabs = listTab;

const currentTab = ref(
    !_.isEmpty(tabStore.current) ? tabStore.current : tabs[0]
);
const currentTabKey = ref(currentTab.value.key);

const handleOnNext = (data) => {
    const currentIndex = tabs.findIndex(
        (item) => item.key == currentTab.value.key
    );
    const tab = tabs[currentIndex + 1] ?? tabs[tabs.length - 1];

    currentTab.value = tab;
    currentTabKey.value = currentTab.value.key;

    tabStore.setCurrent(tab);

    formdataStore.setForm(data);

    if (currentIndex == tabs.length - 1) {
        submitForm(data);
    }
};

const handleOnPrev = (data) => {
    const currentIndex = tabs.findIndex(
        (item) => item.key == currentTab.value.key
    );
    const tab = tabs[currentIndex - 1] ?? tabs[tabs.length - 1];

    currentTab.value = tab;
    currentTabKey.value = currentTab.value.key;

    tabStore.setCurrent(tab);

    formdataStore.setForm(data);
};

const form = useForm({});

const submitForm = async (data) => {
    const result = await Swal.fire({
        icon: "warning",
        title: "Do you want to save this manifest?",
        showCancelButton: true,
        confirmButtonColor: "#28A745",
        cancelButtonColor: "#dfdfdf",
        confirmButtonText: "Submit",
        cancelButtonText: "Cancel",
    });

    if (!result.isConfirmed) {
        return false;
    }

    form.transform(() => data).post(urlStore, {
        onSuccess: () => {
            localStorage.clear();

            tabStore.resetAll();
            formdataStore.reset();
        },
    });
};

onBeforeUnmount(() => {
    localStorage.clear();

    tabStore.resetAll();
    formdataStore.reset();
});
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
                <VForm
                    :manifest="manifest ?? {}"
                    :boats="boats"
                    :company="company"
                    :arrInstructor="arrInstructor"
                    :arrDivemaster="arrDivemaster"
                    :arrGuide="arrGuide"
                    :arrDestination="arrDestination"
                    :arrDeparture="arrDeparture"
                    :arrNationality="arrNationality"
                    :arrActivity="arrActivity"
                    :urlTemplate="urlTemplate"
                    :urlTemplateStaff="urlTemplateStaff"
                    :errors="form.errors"
                    :tab="currentTab"
                    @onNext="handleOnNext"
                    @onPrev="handleOnPrev"
                />
            </div>
        </div>
    </div>
</template>
