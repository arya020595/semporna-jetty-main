<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";

import VTitleWithBackLink from "@/Shared/VTitleWithBackLink.vue";
import VButtonSubmit from "@/Shared/Buttons/VButtonSubmit";
import VDevider from "@/Shared/VDevider.vue";
import VInputWithLabel from "@/Shared/Form/VInputWithLabel.vue";
import VSelectMultipleWithLabel from "@/Shared/Form/VSelectMultipleWithLabel.vue";
import VInputPasswordWithLabel from "@/Shared/Form/VInputPasswordWithLabel.vue";
import VSelectDefaultWithLabel from "@/Shared/Form/VSelectDefaultWithLabel.vue";
import VHeaderBreadcrumb from "@/Shared/VHeaderBreadcrumb.vue";

const props = defineProps({
    title: String,
    additional: Object,
});

const { urlStore, urlIndex, roles } = props.additional;

const form = useForm({
    file_picture: null,
    name: "",
    ic_no: "",
    roles: [],
    status: 1,
    email: "",
    password: "",
    password_confirmation: "",
});

const arrStatus = [
    { id: 1, description: "Active" },
    { id: 0, description: "Non-Active" },
];

const breadcrumbs = [
    {
        url: "/user",
        label: "User Jetty",
    },
    {
        url: "#",
        label: "Add New",
    },
];

const submit = () => {
    form.post(urlStore, {
        preserveScroll: true,
        forceFormData: true,
    });
};
</script>

<template>
    <Head>
        <title>{{ title }}</title>
    </Head>
    <div class="p-3">
        <VHeaderBreadcrumb :breadcrumbs="breadcrumbs" />

        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <VTitleWithBackLink
                        :href="urlIndex"
                        :filters="additional.filters ?? {}"
                    >
                        Add New User
                    </VTitleWithBackLink>
                </div>
                <VDevider />

                <div class="alert alert-info py-2 mt-3" role="alert">
                    <i class="fas fa-info-circle me-1"></i>
                    Staff ID will be generated automatically upon submission.
                </div>

                <form @submit.prevent="submit">
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <VInputWithLabel
                                elId="name"
                                label="Name"
                                type="text"
                                :error="form.errors.name"
                                v-model:value="form.name"
                            />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <VInputWithLabel
                                elId="ic_no"
                                label="IC No."
                                type="text"
                                :error="form.errors.ic_no"
                                v-model:value="form.ic_no"
                            />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <VSelectDefaultWithLabel
                                elId="status"
                                label="Status"
                                :error="form.errors.status"
                                v-model:value="form.status"
                                :options="arrStatus"
                            />
                        </div>
                    </div>
                    <VDevider class="my-4" />

                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <VInputWithLabel
                                elId="email"
                                label="Email"
                                type="email"
                                :error="form.errors.email"
                                v-model:value="form.email"
                            />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <VInputPasswordWithLabel
                                elId="password"
                                label="New Password"
                                :error="form.errors.password"
                                v-model:value="form.password"
                            />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <VInputPasswordWithLabel
                                elId="password_confirmation"
                                label="New Password Confirmation"
                                :error="form.errors.password_confirmation"
                                v-model:value="form.password_confirmation"
                            />
                        </div>
                    </div>
                    <VDevider class="my-4" />

                    <div class="text-end">
                        <VButtonSubmit
                            type="submit"
                            :isProcessing="form.processing"
                        >
                            Submit
                        </VButtonSubmit>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
