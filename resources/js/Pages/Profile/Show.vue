<script setup>
import { Head, Link } from "@inertiajs/vue3";
import VTitleWithBackLink from "@/Shared/VTitleWithBackLink.vue";
import VHeaderButtonEdit from "@/Shared/HeaderButton/VButtonEdit.vue";
import VHeaderBreadcrumb from "@/Shared/VHeaderBreadcrumb.vue";
import VInputReadonlyWithLabel from "@/Shared/Form/VInputReadonlyWithLabel.vue";
import VDevider from "@/Shared/VDevider.vue";
import VContentEditorInlineReadonlyWithLabel from "../../Shared/Form/VContentEditorInlineReadonlyWithLabel.vue";

defineProps({
    user: Object,
    filters: Object,
    canEdit: Boolean,
    urlEdit: String,
    urlIndex: String,
});

const breadcrumbs = [
    {
        url: "#",
        label: "Profile",
    },
];
</script>

<template>
    <Head>
        <title>Profile</title>
    </Head>

    <div class="p-3">
        <VHeaderBreadcrumb :breadcrumbs="breadcrumbs" />

        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <h5>Profile User</h5>
                    <div class="btn-wrapper">
                        <VHeaderButtonEdit v-if="canEdit" :href="urlEdit" />
                    </div>
                </div>
                <VDevider />
                <div class="row">
                    <div class="col-lg-6">
                        <VInputReadonlyWithLabel
                            label="Staf ID"
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
    </div>
</template>
