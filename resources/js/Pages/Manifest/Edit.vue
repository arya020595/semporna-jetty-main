<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import VHeaderBreadcrumb from "@/Shared/VHeaderBreadcrumb.vue";

import VAlert from "@/Shared/VAlert.vue";
import VForm from "./_partials/VForm.vue";
import { ref } from "vue";
import { listTab } from "./_partials/tabs.config";
import _ from "lodash";
import Swal from "sweetalert2";

const props = defineProps({
    title: String,
    additional: Object,
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
    urlUpdate,
} = props.additional;

const manifest = JSON.parse(JSON.stringify(props.additional.manifest));

const breadcrumbs = [
    {
        url: "#",
        label: "Update Manifest Form",
    },
];

let tabs = listTab;

const currentTab = ref(tabs[0]);
const currentTabKey = ref(currentTab.value.key);

const handleOnNext = (data) => {
    const currentIndex = tabs.findIndex(
        (item) => item.key == currentTab.value.key
    );
    const tab = tabs[currentIndex + 1] ?? tabs[tabs.length - 1];

    currentTab.value = tab;
    currentTabKey.value = currentTab.value.key;

    if (currentIndex == tabs.length - 1) {
        submitForm(data);
    }
};

const handleOnPrev = () => {
    const currentIndex = tabs.findIndex(
        (item) => item.key == currentTab.value.key
    );
    const tab = tabs[currentIndex - 1] ?? tabs[tabs.length - 1];

    currentTab.value = tab;
    currentTabKey.value = currentTab.value.key;
};

const form = useForm({});

const submitForm = async (formValue) => {
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

    form.transform(() => formValue).put(urlUpdate, {
        onSuccess: () => {
            localStorage.clear();
        },
    });
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
