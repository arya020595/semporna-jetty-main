<script setup>
import VDevider from "@/Shared/VDevider.vue";

const props = defineProps({
    approvements: Array,
});

const STATUS_APPROVED = 1;
const STATUS_REJECTED = -1;
const STATUS_AMEND = 3;
</script>

<template>
    <div v-for="(approvement, index) in approvements">
        <div class="row">
            <div class="col-lg-4">
                <span class="fw-bold">{{ approvement.role }}</span
                ><br />
                <span class="material-icons" style="font-size: 20px">
                    assignment_ind
                </span>
                : {{ approvement.user }} <br />
                <span class="material-icons" style="font-size: 20px">
                    email
                </span>
                :
                {{ approvement.user_email }}
            </div>
            <div class="col-lg-8">
                <span
                    class="badge rounded-pill mb-2"
                    :class="{
                        'bg-success': approvement.status == STATUS_APPROVED,
                        'bg-danger': approvement.status == STATUS_REJECTED,
                        'bg-warning text-dark': approvement.status == STATUS_AMEND,
                    }"
                >
                    {{ approvement.status_text }}
                </span>
                <span class="badge bg-secondary rounded-pill mb-2 ms-1">
                    v{{ approvement.version }}
                </span>
                <span class="ms-1">at</span> <span class="text-secondary">{{ approvement.date }}</span>
                <div class="bg-white border rounded py-2 px-3">
                    {{ approvement.comments ? approvement.comments : " - " }}
                </div>
            </div>
        </div>
        <VDevider class="my-3" v-if="index < approvements.length - 1" />
    </div>
</template>

<style lang="css" scoped></style>
