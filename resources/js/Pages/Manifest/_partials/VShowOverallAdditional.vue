<script setup>
import VDevider from "@/Shared/VDevider.vue";
import { computed, reactive } from "vue";
import VShowFeeDetailAdditional from "../../../Shared/Manifest/VShowFeeDetailAdditional.vue";
import { formatDateOnly } from "../../../Helpers/date";

const props = defineProps({
    manifest_fee: Object,
});

const form = reactive({
    local_adult: props.manifest_fee.local_adult,
    local_child: props.manifest_fee.local_child,

    foreign_adult: props.manifest_fee.foreign_adult,
    foreign_child: props.manifest_fee.foreign_child,

    ticket_local_adult: props.manifest_fee.ticket_local_adult,
    ticket_local_child: props.manifest_fee.ticket_local_child,

    ticket_foreign_adult: props.manifest_fee.ticket_foreign_adult,
    ticket_foreign_child: props.manifest_fee.ticket_foreign_child,

    boat_fee: parseFloat(props.manifest_fee.boat_fee),

    local_adult_fee: parseFloat(props.manifest_fee.local_adult_fee),
    local_child_fee: parseFloat(props.manifest_fee.local_child_fee),

    foreign_adult_fee: parseFloat(props.manifest_fee.foreign_adult_fee),
    foreign_child_fee: parseFloat(props.manifest_fee.foreign_child_fee),
});

const subTotal = computed(() => {
    return "RM " + parseFloat(props.manifest_fee.total).toFixed(2);
});
</script>

<template>
    <h5 class="date">Date: {{ formatDateOnly(manifest_fee.created_at) }}</h5>
    <template v-if="form.local_adult > 0 || form.local_child > 0">
        <div class="d-flex justify-content-between">
            <h5 class="d-flex align-items-center mb-2">Local Passengers</h5>
        </div>
        <VDevider class="mb-3" />
        <VShowFeeDetailAdditional
            :adult="form.local_adult"
            :child="form.local_child"
            :ticket_adult="form.ticket_local_adult"
            :ticket_child="form.ticket_local_child"
            :adult_fee="form.local_adult_fee"
            :child_fee="form.local_child_fee"
        />
    </template>

    <template v-if="form.foreign_adult > 0 || form.foreign_child > 0">
        <div class="d-flex justify-content-between mt-4">
            <h5 class="d-flex align-items-center mb-2">Foreign Passengers</h5>
        </div>
        <VDevider class="mb-3" />
        <VShowFeeDetailAdditional
            :adult="form.foreign_adult"
            :child="form.foreign_child"
            :ticket_adult="form.ticket_foreign_adult"
            :ticket_child="form.ticket_foreign_child"
            :adult_fee="form.foreign_adult_fee"
            :child_fee="form.foreign_child_fee"
        />
    </template>

    <VDevider class="mb-3" />
    <div class="row">
        <div class="col-12 text-end">
            <div class="form-control-lg bg-primary text-white d-inline">
                Subtotal : <strong>{{ subTotal }}</strong>
            </div>
        </div>
    </div>
</template>
