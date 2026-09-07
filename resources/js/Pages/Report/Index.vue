<script setup>
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import VAlert from "@/Shared/VAlert.vue";

import VHeaderBreadcrumb from "@/Shared/VHeaderBreadcrumb.vue";
import VButton from "../../Shared/Buttons/VButton.vue";
import VSelectDefaultWithBlockLabel from "../../Shared/Form/VSelectDefaultWithBlockLabel.vue";
import VInputWithBlockLabel from "../../Shared/Form/VInputWithBlockLabel.vue";
import VSelectAsyncWithBlockLabel from "../../Shared/Form/VSelectAsyncWithBlockLabel.vue";

const props = defineProps({
    title: String,
    additional: Object,
});

const { urlIndex, urlDownload, urlDeparture, isOperator } = props.additional;

const form = useForm({
    departure_id: props.additional.formData?.departure_id,
    start: props.additional.formData?.start,
    end: props.additional.formData?.end,
});

const breadcrumbs = [
    {
        url: "#",
        label: "Reporting",
    },
];

const submit = () => {
    form.get(urlIndex);
};

const download = () => {
    window.open(urlDownload, "_blank").focus();
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
                <div class="row">
                    <div class="col-md-4 col-lg-3">
                        <div class="shadow px-2 py-3 mb-3">
                            <h5 class="fw-bold">Reporting Filter</h5>

                            <div class="mt-3">
                                <div class="mb-3" v-if="!isOperator">
                                    <VSelectAsyncWithBlockLabel
                                        elId="departure_id"
                                        label="Jetty:"
                                        v-model:value="form.departure_id"
                                        :error="form.errors.departure_id"
                                        :url="urlDeparture"
                                    />
                                </div>

                                <div class="mb-3">
                                    <VInputWithBlockLabel
                                        elId="start"
                                        label="Start:"
                                        type="date"
                                        v-model:value="form.start"
                                        :error="form.errors.start"
                                    />
                                </div>
                                <div class="mb-3">
                                    <VInputWithBlockLabel
                                        elId="end"
                                        label="End:"
                                        type="date"
                                        v-model:value="form.end"
                                        :error="form.errors.end"
                                    />
                                </div>

                                <VButton
                                    @onClick="submit"
                                    btnStyle="btn-primary"
                                    >Filter</VButton
                                >
                            </div>
                        </div>

                        <VButton @onClick="download" btnStyle="btn-primary">
                            <span class="icon-pdf me-1">
                                <img
                                    src="/assets/images/icon_pdf.png"
                                    alt="pdf button"
                                />
                            </span>
                            Download PDF</VButton
                        >
                    </div>
                    <div class="col-md-8 col-lg-9">
                        <object
                            type="application/pdf"
                            :data="urlDownload"
                            width="100%"
                            height="700px"
                        ></object>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="css" scoped>
.overlay {
    top: 0px;
    bottom: 0px;
    right: 0px;
    left: 0px;
    position: fixed;
}
</style>
