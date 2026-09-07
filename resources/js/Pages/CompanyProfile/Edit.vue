<script setup>
import { Head, useForm, Link, router } from "@inertiajs/vue3";
import VDevider from "@/Shared/VDevider.vue";
import VAlert from "@/Shared/VAlert.vue";
import VHeaderBreadcrumb from "@/Shared/VHeaderBreadcrumb.vue";

import VButtonSubmit from "@/Shared/Buttons/VButtonSubmit";
import VInputWithLabel from "@/Shared/Form/VInputWithLabel.vue";
import VButton from "../../Shared/Buttons/VButton.vue";
import VFormBoatDetails from "./_partials/VFormBoatDetails.vue";
import VListBoat from "./_partials/VListBoat.vue";
import VFormBoatmanOther from "./_partials/VFormBoatmanOther.vue";
import { ref, watch } from "vue";
import VFormModalBoatDetails from "./_partials/VFormModalBoatDetails.vue";

const props = defineProps({
    title: String,
    additional: Array,
});

const { company, urlSubmit, urlSubmitBoat, urlDeleteBoat, urlIndex } =
    props.additional;

const breadcrumbs = [
    {
        url: "#",
        label: "Profile",
    },
];

const labelWidth = 4;

const activeBoatID = ref(null);
const isShowForm = ref(false);
const selectedBoat = ref({});
const isShowAlert = ref(false);

const form = useForm({
    number: company.number,
    name: company.name,
    registration_no: company.registration_no,
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

watch(activeBoatID, (newValue) => {
    if (!newValue) {
        return;
    }

    const boat = props.additional.company?.boats?.find(
        (item) => item.id == newValue,
    );

    selectedBoat.value = boat;
    isShowForm.value = true;
    isShowAlert.value = false;
});

const submit = () => {
    form.post(urlSubmit, {
        preserveScroll: true,
        onFinish: () => {
            isShowAlert.value = true;
        },
    });
};

const deleteBoat = (id) => {
    form.delete(urlDeleteBoat.replace(":boatId", id));
};

const cancelForm = () => {
    selectedBoat.value = {};
    isShowForm.value = false;
    activeBoatID.value = null;
};

const successForm = (data) => {
    isShowForm.value = false;
    activeBoatID.value = null;
};

const addBoatDetails = () => {
    isShowForm.value = true;
    isShowAlert.value = false;
};

const addBoatmanOther = (type) => {
    form[type].push({
        name: "",
        ic_no: "",
    });
};

const deleteBoatmanOther = (type, index) => {
    const formData = form.data();
    form[type] = [];

    form[type] = formData[type].filter((item, i) => {
        return i != index;
    });
};

const onClickBack = () => {
    router.visit(urlIndex);
};
</script>

<template>
    <Head>
        <title>{{ title }}</title>
    </Head>

    <div class="p-3">
        <VHeaderBreadcrumb :breadcrumbs="breadcrumbs" />

        <VAlert v-if="isShowAlert" />

        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <h5 class="d-flex align-items-center mb-0">
                        Company Details
                    </h5>
                </div>

                <VDevider />
                <div class="row mt-4">
                    <div class="col-lg-8 mb-3">
                        <VInputWithLabel
                            elId="number"
                            label="Company ID"
                            type="text"
                            v-model:value="form.number"
                            :error="form.errors.number"
                            :widthLabel="labelWidth"
                            :widthInput="12 - labelWidth"
                            :additionalAttr="{ disabled: true }"
                        />
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 mb-3">
                        <VInputWithLabel
                            elId="name"
                            label="Company Name"
                            type="text"
                            v-model:value="form.name"
                            :error="form.errors.name"
                            :widthLabel="labelWidth"
                            :widthInput="12 - labelWidth"
                            :additionalAttr="{ disabled: true }"
                        />
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-8 mb-3">
                        <VInputWithLabel
                            elId="registration_no"
                            label="SSM Number"
                            type="text"
                            v-model:value="form.registration_no"
                            :error="form.errors.registration_no"
                            :widthLabel="labelWidth"
                            :widthInput="12 - labelWidth"
                            :additionalAttr="{ disabled: true }"
                        />
                    </div>
                </div>

                <VDevider class="my-3" />

                <div class="d-flex justify-content-between mb-4">
                    <h5 class="d-flex align-items-center mb-0 text-primary">
                        Boat List
                    </h5>

                    <VButton btnStyle="btn-success" @onClick="addBoatDetails">
                        <span class="material-icons me-1"
                            >add_circle_outline</span
                        >
                        Add Boat
                    </VButton>
                </div>

                <VListBoat
                    :list="additional.company.boats"
                    v-model:value="activeBoatID"
                    @onDelete="deleteBoat"
                />

                <VDevider class="my-3" />

                <div class="d-flex justify-content-between mt-4">
                    <h5 class="d-flex align-items-center mb-0 text-primary">
                        Instructor Details
                    </h5>

                    <VButton
                        btnStyle="btn-success"
                        @onClick="addBoatmanOther('instructor')"
                    >
                        <span class="material-icons me-1"
                            >add_circle_outline</span
                        >
                        Add Instructor
                    </VButton>
                </div>
                <div class="table-responsive mt-3">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th
                                    class="form-table-action-column"
                                    scope="col"
                                ></th>
                                <th
                                    scope="col"
                                    class="fw-bold text-center"
                                    style="width: 50px"
                                >
                                    No.
                                </th>
                                <th scope="col" class="fw-bold">
                                    Instructor Name
                                </th>
                                <th scope="col" class="fw-bold">
                                    IC No. / Passport No.
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <VFormBoatmanOther
                                v-for="(boatman, index) in form.instructor"
                                :key="'instructor-' + index"
                                v-model:value="form.instructor[index]"
                                :index="index"
                                @onDelete="
                                    deleteBoatmanOther('instructor', index)
                                "
                                :isShowDelete="true"
                                field="Instructor"
                                type="instructor"
                            />
                        </tbody>
                    </table>
                </div>

                <VDevider class="my-3" />

                <div class="d-flex justify-content-between mt-4">
                    <h5 class="d-flex align-items-center mb-0 text-primary">
                        Divemaster Details
                    </h5>

                    <VButton
                        btnStyle="btn-success"
                        @onClick="addBoatmanOther('divemaster')"
                    >
                        <span class="material-icons me-1"
                            >add_circle_outline</span
                        >
                        Add Divemaster
                    </VButton>
                </div>
                <div class="table-responsive mt-3">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th
                                    class="form-table-action-column"
                                    scope="col"
                                ></th>
                                <th
                                    scope="col"
                                    class="fw-bold text-center"
                                    style="width: 50px"
                                >
                                    No.
                                </th>
                                <th scope="col" class="fw-bold">
                                    Divemaster Name
                                </th>
                                <th scope="col" class="fw-bold">
                                    IC No. / Passport No.
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <VFormBoatmanOther
                                v-for="(boatman, index) in form.divemaster"
                                :key="'divemaster-' + index"
                                v-model:value="form.divemaster[index]"
                                :index="index"
                                @onDelete="
                                    deleteBoatmanOther('divemaster', index)
                                "
                                :isShowDelete="true"
                                field="Divemaster"
                                type="divemaster"
                            />
                        </tbody>
                    </table>
                </div>

                <VDevider class="my-3" />

                <div class="d-flex justify-content-between mt-4">
                    <h5 class="d-flex align-items-center mb-0 text-primary">
                        Guide Details
                    </h5>

                    <VButton
                        btnStyle="btn-success"
                        @onClick="addBoatmanOther('guide')"
                    >
                        <span class="material-icons me-1"
                            >add_circle_outline</span
                        >
                        Add Guide
                    </VButton>
                </div>
                <div class="table-responsive mt-3">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th
                                    class="form-table-action-column"
                                    scope="col"
                                ></th>
                                <th
                                    scope="col"
                                    class="fw-bold text-center"
                                    style="width: 50px"
                                >
                                    No.
                                </th>
                                <th scope="col" class="fw-bold">Guide Name</th>
                                <th scope="col" class="fw-bold">
                                    IC No. / Passport No.
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <VFormBoatmanOther
                                v-for="(boatman, index) in form.guide"
                                :key="'guide-' + index"
                                v-model:value="form.guide[index]"
                                :index="index"
                                @onDelete="deleteBoatmanOther('guide', index)"
                                :isShowDelete="true"
                                field="Guide"
                                type="guide"
                            />
                        </tbody>
                    </table>
                </div>

                <VDevider class="my-4" />

                <div class="d-flex justify-content-between">
                    <VButton
                        @onClick="onClickBack"
                        type="button"
                        btnStyle="btn-primary px-4"
                    >
                        Back
                    </VButton>
                    <VButtonSubmit
                        type="button"
                        :isProcessing="form.processing"
                        @onCLickSubmit="submit"
                        attrClass="px-4"
                    >
                        Save
                    </VButtonSubmit>
                </div>
            </div>
        </div>
    </div>

    <VFormModalBoatDetails
        v-if="isShowForm"
        :boats="company.boats"
        :value="selectedBoat"
        :urlSubmit="urlSubmitBoat"
        @onCancel="cancelForm"
        @onSuccess="successForm"
    />
</template>
