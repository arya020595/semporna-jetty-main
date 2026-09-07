<script setup>
import { ref, watch } from "vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import VButtonSubmit from "@/Shared/Buttons/VButtonSubmit";
import VInputWithLabel from "@/Shared/Form/VInputWithLabel.vue";
import VAlert from "@/Shared/VAlert.vue";
import VDevider from "@/Shared/VDevider.vue";
import VHeaderBreadcrumb from "@/Shared/VHeaderBreadcrumb.vue";
import VTitleWithBackLink from "@/Shared/VTitleWithBackLink.vue";
import RoleRow from "./_partials/RoleRow.vue";

const props = defineProps({
    title: String,
    additional: Object,
});

const { filters, menulist, urlStore, urlIndex } = props.additional;

const form = useForm({
    name: "",
    selPermission: [],
});

const breadcrumbs = [
    {
        url: urlIndex,
        label: "Role Management",
    },
    {
        url: "#",
        label: "Add New",
    },
];

const submit = () => {
    form.post(urlStore, {
        preserveScroll: true,
    });
};

const formatPermissionName = (code, permissionName) => {
    return permissionName.replace(code + "-", "");
};
</script>

<template>
    <Head>
        <title>{{ title }}</title>
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
                        Add New Role
                    </VTitleWithBackLink>
                </div>
                <VDevider />
                <VAlert />
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
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Menu</th>
                                    <th>Permission</th>
                                </tr>
                            </thead>
                            <tbody>
                                <RoleRow
                                    v-for="(menu, index) in menulist"
                                    :key="menu.id"
                                    :menu="menu"
                                    v-model:value="form.selPermission"
                                    parentIteration=""
                                    :index="index"
                                    :isHeader="true"
                                />
                            </tbody>
                        </table>
                    </div>

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
