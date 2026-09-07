<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import VDevider from "@/Shared/VDevider.vue";
import VAlert from "@/Shared/VAlert.vue";
import VHeaderBreadcrumb from "@/Shared/VHeaderBreadcrumb.vue";

import VButtonSubmit from "@/Shared/Buttons/VButtonSubmit";
import VFormBoatmanOther from "./_partials/VFormBoatmanOther.vue";
import VButton from "../../Shared/Buttons/VButton.vue";

const props = defineProps({
    title: String,
    additional: Array,
});

const { company, urlBack, urlSubmit } = props.additional;

const breadcrumbs = [
    {
        url: "#",
        label: "Profile",
    },
];

const form = useForm({
    instructor: company.instructor?.length
        ? company.instructor
        : [
              {
                  id: "",
                  name: "",
                  ic_no: "",
              },
          ],
    divemaster: company.divemaster?.length
        ? company.divemaster
        : [
              {
                  id: "",
                  name: "",
                  ic_no: "",
              },
          ],
    guide: company.guide?.length
        ? company.guide
        : [
              {
                  id: "",
                  name: "",
                  ic_no: "",
              },
          ],
});

const submit = () => {
    form.post(urlSubmit, {
        preserveScroll: true,
    });
};

const addBoatman = (type) => {
    form[type].push({
        name: "",
        ic_no: "",
    });
};

const deleteBoatman = (type, index) => {
    const formData = form.data();
    form[type] = [];

    form[type] = formData[type].filter((item, i) => {
        return i != index;
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
                <div class="d-flex justify-content-between">
                    <h5 class="d-flex align-items-center mb-0">
                        Instructor Details
                    </h5>
                </div>
                <VDevider />

                <VFormBoatmanOther
                    v-for="(boatman, index) in form.instructor"
                    :key="'instructor-' + index"
                    v-model:value="form.instructor[index]"
                    :index="index"
                    @onDelete="deleteBoatman('instructor', index)"
                    :isShowDelete="true"
                    field="Instructor"
                    type="instructor"
                />
                <div class="d-flex justify-content-end mb-4 mt-2">
                    <VButton
                        btnStyle="btn-success"
                        @onClick="addBoatman('instructor')"
                    >
                        <span class="material-icons me-1"
                            >add_circle_outline</span
                        >
                        Add Instructor
                    </VButton>
                </div>

                <div class="d-flex justify-content-between">
                    <h5 class="d-flex align-items-center mb-0">
                        Divemaster Details
                    </h5>
                </div>
                <VDevider />
                <VFormBoatmanOther
                    v-for="(boatman, index) in form.divemaster"
                    :key="'divemaster-' + index"
                    v-model:value="form.divemaster[index]"
                    :index="index"
                    @onDelete="deleteBoatman('divemaster', index)"
                    :isShowDelete="true"
                    field="Divemaster"
                    type="divemaster"
                />
                <div class="d-flex justify-content-end mb-4 mt-2">
                    <VButton
                        btnStyle="btn-success"
                        @onClick="addBoatman('divemaster')"
                    >
                        <span class="material-icons me-1"
                            >add_circle_outline</span
                        >
                        Add Divemaster
                    </VButton>
                </div>

                <div class="d-flex justify-content-between">
                    <h5 class="d-flex align-items-center mb-0">
                        Guide Details
                    </h5>
                </div>
                <VDevider />
                <VFormBoatmanOther
                    v-for="(boatman, index) in form.guide"
                    :key="'guide-' + index"
                    v-model:value="form.guide[index]"
                    :index="index"
                    @onDelete="deleteBoatman('guide', index)"
                    :isShowDelete="true"
                    field="Guide"
                    type="guide"
                />
                <div class="d-flex justify-content-end mb-4 mt-2">
                    <VButton
                        btnStyle="btn-success"
                        @onClick="addBoatman('guide')"
                    >
                        <span class="material-icons me-1"
                            >add_circle_outline</span
                        >
                        Add Guide
                    </VButton>
                </div>

                <VDevider class="my-3" />

                <div class="d-flex justify-content-between">
                    <Link :href="urlBack" class="btn btn-light px-4">
                        Previous
                    </Link>
                    <VButtonSubmit
                        type="button"
                        :isProcessing="form.processing"
                        @onCLickSubmit="submit"
                        attrClass="px-4"
                    >
                        Submit
                    </VButtonSubmit>
                </div>
            </div>
        </div>
    </div>
</template>
