<script setup>
import { Head, Link } from "@inertiajs/vue3";
import VTitleWithBackLink from "@/Shared/VTitleWithBackLink.vue";
import VHeaderButtonEdit from "@/Shared/HeaderButton/VButtonEdit.vue";
import VHeaderBreadcrumb from "@/Shared/VHeaderBreadcrumb.vue";
import VInputReadonlyWithLabel from "@/Shared/Form/VInputReadonlyWithLabel.vue";
import VDevider from "@/Shared/VDevider.vue";

import { formatDate } from "../../../Helpers/date";
import { ROLE_OPERATOR, ROLE_OPERATOR_JETTY } from "../../../Config/role";

const props = defineProps({
    title: String,
    additional: Object,
});

const { user, filters, canEdit, urlEdit, urlIndex, accessLogs } =
    props.additional;

const breadcrumbs = [
    {
        url: "/user",
        label: "User Profile",
    },
    {
        url: "#",
        label: "Detail",
    },
];
</script>

<template>
    <Head>
        <title>Detail - User Profile</title>
    </Head>

    <div class="p-3">
        <VHeaderBreadcrumb :breadcrumbs="breadcrumbs" />

        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <VTitleWithBackLink :href="urlIndex" :data="filters ?? {}">
                        Detail User
                    </VTitleWithBackLink>
                    <div class="btn-wrapper">
                        <VHeaderButtonEdit v-if="canEdit" :href="urlEdit" />
                    </div>
                </div>
                <VDevider />
                <div class="row">
                    <div class="col-lg-6">
                        <VInputReadonlyWithLabel
                            label="Staff ID"
                            :value="user.staf_id"
                        />
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <VInputReadonlyWithLabel
                            label="Name"
                            :value="user.name"
                        />
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <VInputReadonlyWithLabel
                            label="Roles"
                            :value="
                                user.roles?.map((item) => item.name).join(',')
                            "
                        />
                    </div>
                    <div class="col-lg-6">
                        <VInputReadonlyWithLabel
                            v-if="user.roles[0]?.id == ROLE_OPERATOR"
                            label="Company"
                            :value="user.company_name"
                        />
                        <VInputReadonlyWithLabel
                            v-if="user.roles[0]?.id == ROLE_OPERATOR_JETTY"
                            label="Jetty"
                            :value="user.jetty_name"
                        />
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <VInputReadonlyWithLabel
                            label="Status"
                            :value="user.status_text"
                        />
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <VInputReadonlyWithLabel
                            label="IC No."
                            :value="user.ic_no"
                        />
                    </div>
                </div>
                <VDevider class="my-4" />

                <div class="row">
                    <div class="col-lg-6 mb-3">
                        <VInputReadonlyWithLabel
                            label="Email"
                            :value="user.email"
                        />
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <h5>Access log</h5>
                <VDevider />
                <div v-for="log in accessLogs" class="fs-6">
                    <span class="fw-lighter">{{
                        formatDate(log.created_at)
                    }}</span>
                    -
                    {{ log.action.toUpperCase() }}
                </div>
            </div>
        </div>
    </div>
</template>
