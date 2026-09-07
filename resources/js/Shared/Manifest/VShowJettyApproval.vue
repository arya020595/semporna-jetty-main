<script setup>
import {
    STATUS_AMEND,
    STATUS_APPROVED,
    STATUS_PENDING,
    STATUS_REJECTED,
} from "../../Config/approvement";
import VDevider from "../VDevider.vue";

const props = defineProps({
    manifest: Object,
});
</script>

<template>
    <div class="bg-light border p-3 mt-3">
        <div class="d-flex justify-content-between">
            <h5 class="d-flex align-items-center mb-1">Jetty Approval</h5>
        </div>
        <VDevider class="mb-3" />

        <div class="row">
            <div class="col-md-4 col-lg-3">Status:</div>
            <div class="col-md-8 col-lg-9">
                <span
                    class="badge"
                    :class="{
                        'bg-secondary':
                            manifest.jetty_approval_status == STATUS_PENDING,
                        'bg-success':
                            manifest.jetty_approval_status == STATUS_APPROVED,
                        'bg-warning':
                            manifest.jetty_approval_status == STATUS_AMEND,
                        'bg-danger':
                            manifest.jetty_approval_status == STATUS_REJECTED,
                    }"
                    >{{ manifest.jetty_approval_status_text }}</span
                >
            </div>
            <div class="col-md-4 col-lg-3 mt-2">Approved By:</div>
            <div class="col-md-8 col-lg-9 mt-2">
                <strong>{{ manifest.jetty_approval_user?.name || "-" }}</strong>
            </div>
            <div class="col-md-4 col-lg-3 mt-2">Comments:</div>
            <div class="col-md-8 col-lg-9 mt-2">
                {{ manifest.jetty_approval_comments || "-" }}
            </div>
        </div>
    </div>
</template>
