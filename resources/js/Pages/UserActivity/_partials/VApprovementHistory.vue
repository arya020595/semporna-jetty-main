<script setup>
import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import VDevider from "../../../Shared/VDevider.vue";

const props = defineProps({
    approvements: Array,
});

const STATUS_APPROVED = 1;
const STATUS_REJECTED = -1;
const STATUS_AMEND = 3;
</script>

<template>
    <div v-for="approvement in approvements">
        <div class="row">
            <div class="col-lg-4">
                <span
                    class="badge rounded-pill"
                    :class="{
                        'bg-success': approvement.status == STATUS_APPROVED,
                        'bg-danger': approvement.status == STATUS_REJECTED,
                        'bg-warning text-dark': approvement.status == STATUS_AMEND,
                    }"
                >
                    {{ approvement.status_text }}
                </span>
                <span class="badge bg-secondary rounded-pill ms-1">
                    v{{ approvement.version }}
                </span>
                <br />
                <span class="fw-bold">{{ approvement.role }}</span> at
                <span class="text-secondary">{{ approvement.date }}</span>
            </div>
            <div class="col-lg-8">{{ approvement.comments }}</div>
        </div>
        <VDevider class="my-2" />
    </div>
</template>

<style lang="css" scoped></style>
