<script setup>
import { Link } from "@inertiajs/vue3";
import VDevider from "@/Shared/VDevider.vue";

import VButton from "@/Shared/Buttons/VButton.vue";
import VShowBoatmanOther from "./VShowBoatmanOther.vue";

const props = defineProps({
    company: Object,
    urlEdit: String,
});
const emits = defineEmits(["onPrev"]);

const onClickPrev = () => {
    emits("onPrev");
};
</script>

<template>
    <div class="d-flex justify-content-between">
        <h5 class="d-flex align-items-center fw-bold mb-0">
            Instructor Details
        </h5>

        <Link :href="urlEdit" class="btn btn-primary">Edit Profile</Link>
    </div>

    <VDevider class="mb-3" />

    <template v-for="instructor in company.instructor">
        <VShowBoatmanOther
            field="Instructor"
            type="instructor"
            :value="instructor"
        />

        <VDevider class="mb-4" />
    </template>

    <div class="d-flex justify-content-between">
        <h5 class="d-flex align-items-center fw-bold mb-0">
            Divemaster Details
        </h5>
    </div>

    <VDevider class="mb-3" />

    <template v-for="divemaster in company.divemaster">
        <VShowBoatmanOther
            field="Divemaster"
            type="divemaster"
            :value="divemaster"
        />

        <VDevider class="mb-4" />
    </template>

    <div class="d-flex justify-content-between">
        <h5 class="d-flex align-items-center fw-bold mb-0">Guide Details</h5>
    </div>

    <VDevider class="mb-3" />

    <template v-for="guide in company.guide">
        <VShowBoatmanOther field="Guide" type="guide" :value="guide" />

        <VDevider class="mb-4" />
    </template>

    <div class="d-flex justify-content-between">
        <VButton
            @onClick="onClickPrev"
            type="button"
            btnStyle="btn-primary px-4"
        >
            Previous
        </VButton>
    </div>
</template>
