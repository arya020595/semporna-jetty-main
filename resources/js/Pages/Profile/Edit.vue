<script setup>
import { Head, useForm } from "@inertiajs/vue3";

import VTitleWithBackLink from "@/Shared/VTitleWithBackLink.vue";
import VButtonSubmit from "@/Shared/Buttons/VButtonSubmit";
import VDevider from "@/Shared/VDevider.vue";
import VInputWithLabel from "@/Shared/Form/VInputWithLabel.vue";
import VInputReadonlyWithLabel from "@/Shared/Form/VInputReadonlyWithLabel.vue";
import VInputPasswordWithLabel from "@/Shared/Form/VInputPasswordWithLabel.vue";
import VHeaderBreadcrumb from "@/Shared/VHeaderBreadcrumb.vue";
import VAlert from "@/Shared/VAlert.vue";

const props = defineProps({
    user: Object,
    hasPassword: Boolean,
    roles: Array,
    filters: {
        type: Object,
        default: null,
    },
    canView: Boolean,
    urlShow: String,
    urlUpdate: String,
    urlUpdateCreds: String,
});

const form = useForm({
    staf_id: props.user.staf_id,
    name: props.user.name,
    ic_no: props.user.ic_no,
    roles: props.user.roles.map((item) => item.id),
    status: props.user.status,
    _method: "PUT",
});

const formCreds = useForm({
    email: props.user.email,
    password_old: "",
    password: "",
    password_confirmation: "",
    _method: "PUT",
});

const arrStatus = [
    { id: 1, description: "Active" },
    { id: 0, description: "Non-Active" },
];

const breadcrumbs = [
    {
        url: props.urlShow,
        label: "Profile",
    },
    {
        url: "#",
        label: "Edit Profile",
    },
];

const submit = () => {
    form.post(props.urlUpdate, {
        preserveScroll: true,
        forceFormData: true,
    });
};

const submitCreds = () => {
    formCreds.post(props.urlUpdateCreds, {
        onSuccess: () => {
            formCreds.password_old = "";
            formCreds.password = "";
            formCreds.password_confirmation = "";
        },
    });
};
</script>

<template>
    <Head>
        <title>Edit - Profile</title>
    </Head>
    <div class="p-3">
        <VHeaderBreadcrumb :breadcrumbs="breadcrumbs" />

        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <VTitleWithBackLink :href="urlShow">
                        Edit Profile
                    </VTitleWithBackLink>
                </div>

                <VDevider />
                <VAlert />

                <form @submit.prevent="submit">
                    <div class="row mt-4">
                        <div class="col-lg-6 mb-3">
                            <VInputReadonlyWithLabel
                                elId="staf_id"
                                label="Staf ID"
                                type="text"
                                :isPlainText="false"
                                :value="form.staf_id"
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

                    <div class="row" v-if="hasPassword">
                        <div class="col-lg-6 mb-3">
                            <VInputPasswordWithLabel
                                elId="password_old"
                                label="Old Password"
                                :error="formCreds.errors.password_old"
                                v-model:value="formCreds.password_old"
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
