<script setup>
import VDevider from "@/Shared/VDevider.vue";
import VShowBoatmanOtherItem from "../../../Shared/Manifest/VShowBoatmanOtherItem.vue";

const props = defineProps({
    manifest: Object,
});

const form = {
    instructor: props.manifest?.instructor ?? [
        {
            id: "",
            boatman_id: "",
            name: "",
            ic_no: "",
        },
    ],
    divemaster: props.manifest?.divemaster ?? [
        {
            id: "",
            boatman_id: "",
            name: "",
            ic_no: "",
        },
    ],
    guide: props.manifest?.guide ?? [
        {
            id: "",
            boatman_id: "",
            name: "",
            ic_no: "",
        },
    ],
};
</script>

<template>
    <template v-if="form.instructor.length">
        <VDevider class="mb-2" />
        <div class="d-flex justify-content-between">
            <h5 class="d-flex align-items-center mb-3">Instructor Details</h5>
        </div>

        <VShowBoatmanOtherItem
            v-for="(boatman, index) in form.instructor"
            :key="'instructor-' + index"
            :value="form.instructor[index]"
            :index="index"
            field="Instructor"
            type="instructor"
        />
    </template>

    <template v-if="form.divemaster.length">
        <VDevider class="mb-2" />
        <div class="d-flex justify-content-between">
            <h5 class="d-flex align-items-center mb-3">Divemaster Details</h5>
        </div>
        <VShowBoatmanOtherItem
            v-for="(boatman, index) in form.divemaster"
            :key="'divemaster-' + index"
            :value="form.divemaster[index]"
            :index="index"
            field="Divemaster"
            type="divemaster"
        />
    </template>

    <template v-if="form.guide.length">
        <VDevider class="mb-2" />
        <div class="d-flex justify-content-between">
            <h5 class="d-flex align-items-center mb-3">Guide Details</h5>
        </div>

        <VShowBoatmanOtherItem
            v-for="(boatman, index) in form.guide"
            :key="'guide-' + index"
            :value="form.guide[index]"
            :index="index"
            field="Guide"
            type="guide"
        />
    </template>
</template>
