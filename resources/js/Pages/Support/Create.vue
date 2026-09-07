<script setup>
import { Head, router, useForm } from "@inertiajs/vue3";
import VDevider from "@/Shared/VDevider.vue";
import VAlert from "@/Shared/VAlert.vue";

import VHeaderBreadcrumb from "@/Shared/VHeaderBreadcrumb.vue";
import VButton from "../../Shared/Buttons/VButton.vue";
import VTextareaWithLabel from "../../Shared/Form/Survey/VTextareaWithLabel.vue";
import VInputWithLabel from "../../Shared/Form/VInputWithLabel.vue";

let props = defineProps({
    title: String,
    additional: Object,
});

const { urlStore, user } = props.additional;

const breadcrumbs = [
    {
        url: "#",
        label: "Support",
    },
];

const form = useForm({
    from: user?.name ?? "",
    email: user?.email ?? "",
    subject: "",
    description: "",
});

const submit = () => {
    form.post(urlStore, {
        onSuccess: () => {
            form.reset();
        },
    });
};

const widthLabel = 2;
const widthLabelTextArea = 1;
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
                <div class="d-flex justify-content-between">
                    <h5 class="d-flex align-items-center mb-0">
                        Contact Support
                    </h5>
                </div>
                <VDevider class="mb-3" />

                <div class="mb-3">Please fill in the fields provided :</div>

                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <VInputWithLabel
                                    elId="from"
                                    label="From:"
                                    placeholder="From"
                                    v-model:value="form.from"
                                    :error="form.errors?.from"
                                    :widthLabel="widthLabel"
                                    :widthInput="12 - widthLabel"
                                    :additionalAttr="{
                                        disabled: !isAssistantFreeText,
                                    }"
                                />
                            </div>
                            <div class="col-md-6 mb-3">
                                <VInputWithLabel
                                    elId="email"
                                    label="Email:"
                                    placeholder="Email"
                                    v-model:value="form.email"
                                    :error="form.errors?.email"
                                    :widthLabel="widthLabel"
                                    :widthInput="12 - widthLabel"
                                    :additionalAttr="{
                                        disabled: !isAssistantFreeText,
                                    }"
                                />
                            </div>
                        </div>

                        <VDevider class="mb-4" />

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <VInputWithLabel
                                    elId="subject"
                                    label="Subject:"
                                    placeholder="Subject"
                                    v-model:value="form.subject"
                                    :error="form.errors?.subject"
                                    :widthLabel="widthLabelTextArea"
                                    :widthInput="12 - widthLabelTextArea"
                                />
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <VTextareaWithLabel
                                    elId="description"
                                    label="Description:"
                                    v-model:value="form.description"
                                    :error="form.errors?.description"
                                    :widthLabel="widthLabelTextArea"
                                    :widthInput="12 - widthLabelTextArea"
                                    :rows="12"
                                />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-sm-9 offset-sm-2">
                                        <VButton
                                            btnStyle="btn-primary px-4"
                                            @onClick="submit()"
                                        >
                                            Submit</VButton
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
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
