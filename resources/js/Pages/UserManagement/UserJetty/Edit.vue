<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";

import VTitleWithBackLink from "@/Shared/VTitleWithBackLink.vue";
import VHeaderButtonInfo from "@/Shared/HeaderButton/VButtonInfo.vue";
import VButtonSubmit from "@/Shared/Buttons/VButtonSubmit";
import VDevider from "@/Shared/VDevider.vue";
import VInputWithLabel from "@/Shared/Form/VInputWithLabel.vue";
import VSelectMultipleWithLabel from "@/Shared/Form/VSelectMultipleWithLabel.vue";
import VInputPasswordWithLabel from "@/Shared/Form/VInputPasswordWithLabel.vue";
import VSelectDefaultWithLabel from "@/Shared/Form/VSelectDefaultWithLabel.vue";
import VHeaderBreadcrumb from "@/Shared/VHeaderBreadcrumb.vue";
import VAlert from "@/Shared/VAlert.vue";

const props = defineProps({
    title: String,
    additional: Object,
});

const {
    user,
    roles,
    filters,
    canView,
    urlShow,
    urlUpdate,
    urlIndex,
    urlUpdateCreds,
} = props.additional;

const form = useForm({
    staf_id: user.staf_id,
    name: user.name,
    ic_no: user.ic_no,
    status: user.status,
    _method: "PUT",
});

const formCreds = useForm({
    email: user.email,
    password: "",
    password_confirmation: "",
    _method: "PUT",
});

const arrStatus = [
    { id: 1, description: "Active" },
    { id: 0, description: "Pending" },
    { id: -1, description: "Rejected" },
];

const breadcrumbs = [
    {
        url: "/user",
        label: "User Jetty",
    },
    {
        url: "#",
        label: "Edit",
    },
];

const submit = () => {
    form.post(urlUpdate, {
        preserveScroll: true,
        forceFormData: true,
    });
};

const submitCreds = () => {
    formCreds.post(urlUpdateCreds, {
        onSuccess: () => {
            formCreds.value.password = "";
            formCreds.value.password_confirmation = "";
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

        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <VTitleWithBackLink
                        :href="urlIndex"
                        :filters="filters ?? {}"
                    >
                        Edit User
                    </VTitleWithBackLink>
                    <div class="btn-wrapper">
                        <VHeaderButtonInfo v-if="canView" :href="urlShow" />
                    </div>
                </div>
                <VDevider />
                <VAlert />

                <form @submit.prevent="submit">
                    <div class="row mt-4">
                        <div class="col-lg-6 mb-3">
                            <VInputWithLabel
                                elId="staf_id"
                                label="Staff ID"
                                type="text"
                                :error="form.errors.staf_id"
                                v-model:value="form.staf_id"
                                :additionalAttr="{ disabled: true }"
                            />
                        </div>
                    </div>
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
                </form>
                <form @submit.prevent="submitCreds">
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <VInputWithLabel
                                elId="email"
                                label="Email"
                                type="email"
                                :error="formCreds.errors.email"
                                v-model:value="formCreds.email"
                            />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <VInputPasswordWithLabel
                                elId="password"
                                label="New Password"
                                :error="formCreds.errors.password"
                                v-model:value="formCreds.password"
                            />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <VInputPasswordWithLabel
                                elId="password_confirmation"
                                label="New Password Confirmation"
                                :error="formCreds.errors.password_confirmation"
                                v-model:value="formCreds.password_confirmation"
                            />
                        </div>
                        <div class="col-lg-6">
                            <button
                                type="button"
                                class="btn btn-secondary"
                                :disabled="formCreds.processing"
                                @click="submitCreds"
                            >
                                Change Password
                            </button>
                        </div>
                    </div>
                </form>
                <VDevider class="my-4" />

                <div class="text-end">
                    <VButtonSubmit
                        type="button"
                        :isProcessing="form.processing"
                        @onCLickSubmit="submit"
                    >
                        Submit
                    </VButtonSubmit>
                </div>
            </div>
        </div>
    </div>
</template>
