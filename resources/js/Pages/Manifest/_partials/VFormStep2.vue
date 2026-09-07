<script setup>
import VDevider from "@/Shared/VDevider.vue";
import { useForm } from "@inertiajs/vue3";
import VButtonSubmit from "@/Shared/Buttons/VButtonSubmit";
import VFormBoatmanOtherItem from "../../../Shared/Manifest/VFormBoatmanOtherItem.vue";
import VButton from "../../../Shared/Buttons/VButton.vue";
import { computed, onActivated, onMounted, onUpdated, watch } from "vue";
import VFormInstructor from "../../../Shared/Manifest/VFormInstructor.vue";

const props = defineProps({
    additional: Object,
});

const { arrInstructor, arrDivemaster, arrGuide } = props.additional;

onActivated(() => {
    if (props.additional.manifest.is_dive_activity != 1) {
        if (props.additional.tabDirection == 1) {
            handleClickNext();
        } else {
            handleClickPrev();
        }
    }
});

const form = useForm({
    instructor: props.additional.manifest?.instructor ?? [
        {
            id: "",
            boatman_id: "",
            name: "",
            ic_no: "",
        },
    ],
    divemaster: props.additional.manifest?.divemaster ?? [
        {
            id: "",
            boatman_id: "",
            name: "",
            ic_no: "",
        },
    ],
    guide: props.additional.manifest?.guide ?? [
        {
            id: "",
            boatman_id: "",
            name: "",
            ic_no: "",
        },
    ],
});

const emits = defineEmits(["onNext", "onPrev"]);

const selectedInstructor = computed(() => {
    return form.instructor.map((item) => item.boatman_id);
});

const selectedDivemaster = computed(() => {
    return form.divemaster.map((item) => item.boatman_id);
});

const selectedGuide = computed(() => {
    return form.guide.map((item) => item.boatman_id);
});

const handleClickPrev = () => {
    emits("onPrev");
};

const handleClickNext = () => {
    emits("onNext", form.data());
};

const addBoatman = (type) => {
    form[type].push({
        boatman_id: "",
        name: "",
        ic_no: "",
    });
};

const deleteBoatman = (type, index) => {
    const formData = form.data();
    form[type] = [];

    form[type] = formData[type].filter((item, i) => {
        return i != index && (item.boatman_id || item.name);
    });
};
</script>

<template>
    <div class="d-flex justify-content-between">
        <h5 class="d-flex align-items-center mb-0">Instructor Details</h5>
    </div>
    <VDevider class="mb-4" />

    <VFormBoatmanOtherItem
        v-for="(boatman, index) in form.instructor"
        :key="'instructor-' + index"
        v-model:value="form.instructor[index]"
        :index="index"
        @onDelete="deleteBoatman('instructor', index)"
        :isShowDelete="true"
        :arrSelected="selectedInstructor"
        field="Instructor"
        type="instructor"
        :options="arrInstructor"
    />
    <div class="row">
        <div class="col-lg-6">
            <div class="row">
                <div class="col-sm-8 offset-sm-4">
                    <VButton
                        btnStyle="btn-success"
                        @onClick="addBoatman('instructor')"
                    >
                        <span class="material-icons me-1"
                            >add_circle_outline</span
                        >
                        Add Instructor</VButton
                    >
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-between mt-5">
        <h5 class="d-flex align-items-center mb-0">Divemaster Details</h5>
    </div>
    <VDevider class="mb-4" />
    <VFormBoatmanOtherItem
        v-for="(boatman, index) in form.divemaster"
        :key="'divemaster-' + index"
        v-model:value="form.divemaster[index]"
        :index="index"
        @onDelete="deleteBoatman('divemaster', index)"
        :isShowDelete="true"
        :arrSelected="selectedDivemaster"
        field="Divemaster"
        type="divemaster"
        :options="arrDivemaster"
    />
    <div class="row">
        <div class="col-lg-6">
            <div class="row">
                <div class="col-sm-8 offset-sm-4">
                    <VButton
                        btnStyle="btn-success"
                        @onClick="addBoatman('divemaster')"
                    >
                        <span class="material-icons me-1"
                            >add_circle_outline</span
                        >
                        Add Divemaster</VButton
                    >
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between mt-5">
        <h5 class="d-flex align-items-center mb-0">Guide Details</h5>
    </div>
    <VDevider class="mb-4" />

    <VFormBoatmanOtherItem
        v-for="(boatman, index) in form.guide"
        :key="'guide-' + index"
        v-model:value="form.guide[index]"
        :index="index"
        @onDelete="deleteBoatman('guide', index)"
        :isShowDelete="true"
        :arrSelected="selectedGuide"
        field="Guide"
        type="guide"
        :options="arrGuide"
    />
    <div class="row">
        <div class="col-lg-6">
            <div class="row">
                <div class="col-sm-8 offset-sm-4">
                    <VButton
                        btnStyle="btn-success"
                        @onClick="addBoatman('guide')"
                    >
                        <span class="material-icons me-1"
                            >add_circle_outline</span
                        >
                        Add Guide</VButton
                    >
                </div>
            </div>
        </div>
    </div>

    <VDevider class="my-3" />
    <div class="d-flex justify-content-between">
        <VButtonSubmit
            type="button"
            :isProcessing="form.processing"
            @onCLickSubmit="handleClickPrev"
            attrClass="px-4"
        >
            Go Back
        </VButtonSubmit>

        <VButtonSubmit
            type="button"
            :isProcessing="form.processing"
            @onCLickSubmit="handleClickNext"
            attrClass="px-4"
        >
            Next
        </VButtonSubmit>
    </div>
</template>
